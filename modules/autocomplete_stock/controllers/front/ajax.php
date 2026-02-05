<?php
/*
 * MIT License
 * (c) 2025
 */

class Autocomplete_StockAjaxModuleFrontController extends ModuleFrontController
{
    private const LEMMA_CACHE_TTL = 3600; // 1h
    private const AC_PAYLOAD_TTL  = 60;   // 60s

    private function logInfo(string $msg): void
    {
        // PrestaShopLogger::addLog('[AutocompleteStock] ' . $msg, 1);
    }

    private function getModuleFilePath(string $filename): string
    {
        $root = dirname(__DIR__, 2); // modules/autocomplete_stock
        return $root . '/' . ltrim($filename, '/');
    }

    private function getMachineRoots(): array
    {
        static $roots = null;
        static $logged = false;

        if ($roots === null) {
            $path   = $this->getModuleFilePath('machine_roots.php');
            $loaded = @include $path;
            $roots  = is_array($loaded) ? $loaded : [];

            if (!$logged) {
                $p = realpath($path) ?: $path;
                // PrestaShopLogger::addLog(
                //     '[AutocompleteStock] machine_roots.php ' . (empty($roots) ? 'NOT ' : '') . 'loaded' .
                //     (empty($roots) ? '' : (' ('.count($roots).' roots)')) .
                //     ' path=' . $p,
                //     1
                // );
                $logged = true;
            }
        }
        return $roots;
    }

    private function tokenize(string $text): array
    {
        $text = (string)$text;
        if ($text === '') return [];
        if (preg_match_all("/[0-9A-Za-zÀ-žąćęłńóśźżĄĆĘŁŃÓŚŹŻ'-]+/u", $text, $m)) {
            return $m[0];
        }
        return [];
    }

    private function toAscii(string $s): string
    {
        $map = [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
            'Ą'=>'A','Ć'=>'C','Ę'=>'E','Ł'=>'L','Ń'=>'N','Ó'=>'O','Ś'=>'S','Ź'=>'Z','Ż'=>'Z',
        ];
        return strtr($s, $map);
    }

    private function normAscii(string $s): string
    {
        return $this->toAscii(mb_strtolower($s, 'UTF-8'));
    }

    private function normalizeCompact(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/[^\p{L}\p{N}]+/u', '', $s) ?? '';
        return $s;
    }

    private function tokenizeLower(string $s): array
    {
        $s = mb_strtolower($s, 'UTF-8');
        if (preg_match_all("/[0-9A-Za-zÀ-žąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+/u", $s, $m)) {
            return $m[0];
        }
        return [];
    }

    private function hasNumberCmPattern(string $s): bool
    {
        $s = mb_strtolower($s, 'UTF-8');
        return (bool) preg_match('/\b\d+\s*cm\b/u', $s) || (bool) preg_match('/\d+cm/u', $s);
    }

    private function isProbablyPlural(string $w): bool
    {
        $ends = ['owie','ami','ach','ki','y','i','e'];
        foreach ($ends as $sfx) {
            if (mb_substr($w, -mb_strlen($sfx), null, 'UTF-8') === $sfx) return true;
        }
        return false;
    }

    private function singularizeCandidates(string $w): array
    {
        static $irregulars = null;
        static $irregDiagLogged = false;

        if ($irregulars === null) {
            $path = $this->getModuleFilePath('irregulars.php');
            $loaded = @include $path;
            if (is_array($loaded)) {
                $irregulars = $loaded;
                if (!$irregDiagLogged) {
                    $p = realpath($path) ?: $path;
                    // PrestaShopLogger::addLog('[AutocompleteStock] irregulars.php loaded OK (' . count($irregulars) . ' entries) path=' . $p, 1);
                    $irregDiagLogged = true;
                }
            } else {
                $irregulars = [];
                if (!$irregDiagLogged) {
                    $p = realpath($path) ?: $path;
                    // PrestaShopLogger::addLog('[AutocompleteStock] irregulars.php NOT loaded path=' . $p, 1);
                    $irregDiagLogged = true;
                }
            }
        }

        $wl      = mb_strtolower($w, 'UTF-8');
        $wlAscii = $this->toAscii($wl);

        if (isset($irregulars[$wl]))      return (array) $irregulars[$wl];
        if (isset($irregulars[$wlAscii]))  return (array) $irregulars[$wlAscii];

        $rules = [
            ['owie', ['a']],
            ['ami',  ['a']],
            ['ach',  ['a']],
            ['ki',   ['ka']],
            ['y',    ['a', '']],
            ['i',    ['a', '']],
            ['e',    ['a']],
            ['ów',   ['']],
            ['ow',   ['']],
        ];

        $out = [];
        foreach ($rules as [$from, $tos]) {
            $len = mb_strlen($from, 'UTF-8');
            if ($len > 0 && mb_substr($wl, -$len, null, 'UTF-8') === $from) {
                $stem = mb_substr($wl, 0, mb_strlen($wl, 'UTF-8') - $len, 'UTF-8');
                foreach ((array)$tos as $to) {
                    $base = $stem . $to;
                    if (mb_strlen($base, 'UTF-8') >= 3) $out[] = $base;
                }
            }
        }

        if (empty($out)) {
            $last = mb_substr($wl, -1, null, 'UTF-8');
            if ($last === 'y' || $last === 'i') {
                $base = mb_substr($wl, 0, mb_strlen($wl, 'UTF-8') - 1, 'UTF-8');
                if (mb_strlen($base, 'UTF-8') >= 3) $out[] = $base;
            }
        }

        return array_values(array_unique($out));
    }

    private function smartStemKey(string $t): string
    {
        $w = mb_strtolower($this->toAscii($t), 'UTF-8');
        if ($w === '') return $w;
        $ends = [
            'owie','owych','owego','owemu','owymi','owej','owym','owy','owa','owe',
            'ami','ach','owi','om','em','ie','ow','ów',
            'any','ane','ana','eni','ony','ona','one',
            'ski','cki',
            'ki','ka','ek','ce','cie',
            'y','i','e','a','o','u','ą','ę'
        ];
        foreach ($ends as $sfx) {
            $len = mb_strlen($sfx, 'UTF-8');
            if ($len > 0 && mb_substr($w, -$len, null, 'UTF-8') === $sfx) {
                $base = mb_substr($w, 0, mb_strlen($w, 'UTF-8') - $len, 'UTF-8');
                if (mb_strlen($base, 'UTF-8') >= 4) return $base;
            }
        }
        if (mb_strlen($w, 'UTF-8') > 6) return mb_substr($w, 0, 6, 'UTF-8');
        return $w;
    }

    private function variantsForToken(string $tok): array
    {
        $t = mb_strtolower($this->toAscii($tok), 'UTF-8');

        if (preg_match('/^\d+$/', $t)) {
            return [$t];
        }

        $stem = $this->smartStemKey($t);
        $out  = [];

        foreach ([2,3,4,6] as $n) {
            if (mb_strlen($t, 'UTF-8') >= $n) $out[] = mb_substr($t, 0, $n, 'UTF-8');
        }

        static $syn = null; static $synDiag = false;
        if ($syn === null) {
            $path = $this->getModuleFilePath('synonyms.php');
            $loaded = @include $path;
            $syn = is_array($loaded) ? $loaded : [];
            if (!$synDiag) {
                $p = realpath($path) ?: $path;
                // PrestaShopLogger::addLog('[AutocompleteStock] synonyms.php ' . (empty($syn) ? 'NOT ' : '') . 'loaded' . (empty($syn) ? '' : (' ('.count($syn).' keys)')) . ' path=' . $p, 1);
                $synDiag = true;
            }
        }

        $cands = [$t];
        if (mb_strlen($t, 'UTF-8') >= 8) $cands[] = mb_substr($t, 0, 8, 'UTF-8');
        if (mb_strlen($t, 'UTF-8') >= 6) $cands[] = mb_substr($t, 0, 6, 'UTF-8');

        if ($stem !== '' && $stem !== $t) {
            $cands[] = $stem;
            if (mb_strlen($stem, 'UTF-8') >= 8) $cands[] = mb_substr($stem, 0, 8, 'UTF-8');
            if (mb_strlen($stem, 'UTF-8') >= 6) $cands[] = mb_substr($stem, 0, 6, 'UTF-8');
        }

        foreach (array_unique($cands) as $k) {
            if (!empty($syn[$k]) && is_array($syn[$k])) {
                foreach ($syn[$k] as $s) {
                    $s = mb_strtolower($this->toAscii((string)$s), 'UTF-8');
                    if ($s !== '') $out[] = $s;
                }
            }
        }

        return array_values(array_unique(array_filter($out)));
    }

    private function getAccessoryHeadsAscii(): array
    {
        static $heads = null;
        if ($heads !== null) return $heads;

        $list = [
            'talerz','dysk',
            'noże','noz','noze','łopatki','lopatki',
            'filtr','zestaw','pakiet','serwis','serwisowy',
            'uchwyt','osprzęt','osprzet','kółka','kolka','wąż','waz','rura','adapter',
            'rama','pokrowiec','olej','smar','bateria','zbiornik','ssawa','szczotka','elastomer',
            'piasta','przekładnia','przekladnia','pasek','belt','chain','łańcuch','lancuch',
        ];
        $heads = array_values(array_unique(array_map(function($s){ return $this->toAscii(mb_strtolower($s,'UTF-8')); }, $list)));
        return $heads;
    }

    private function stemsFromSingulars(array $singulars): array
    {
        $out = [];
        foreach ($singulars as $s) {
            $a = $this->toAscii(mb_strtolower((string)$s, 'UTF-8'));
            if ($a !== '') $out[] = $a;
            $st = $this->smartStemKey($a);
            if ($st !== '' && $st !== $a) $out[] = $st;
        }
        return array_values(array_unique(array_filter($out)));
    }

    private function detectMachineStemsFromQuery(array $queryTokensAscii): array
    {
        $stems = [];

        $roots = $this->getMachineRoots();
        if (!empty($roots)) {
            foreach ($roots as $root => $variants) {
                $rootAscii = $this->toAscii(mb_strtolower($root,'UTF-8'));
                foreach ($queryTokensAscii as $qt) {
                    if ($qt === '' ) continue;
                    if (strpos($qt, $rootAscii) === 0) {
                        $stems[] = $rootAscii;
                        $stemR   = $this->smartStemKey($rootAscii);
                        if ($stemR !== '' ) $stems[] = $stemR;
                        break;
                    }
                }
                if (!empty($variants) && is_array($variants)) {
                    foreach ($variants as $v) {
                        $va = $this->toAscii(mb_strtolower((string)$v,'UTF-8'));
                        foreach ($queryTokensAscii as $qt) {
                            if ($qt === '' ) continue;
                            if (strpos($qt, $va) === 0) {
                                $stems[] = $rootAscii;
                                $stemR   = $this->smartStemKey($rootAscii);
                                if ($stemR !== '' ) $stems[] = $stemR;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        foreach ($queryTokensAscii as $qt) {
            if ($qt === '') continue;
            $st = $this->smartStemKey($qt);
            if (mb_strlen($st,'UTF-8') >= 4) $stems[] = $st;
        }

        $stems = array_values(array_unique(array_filter($stems)));
        return $stems;
    }

    private function isAccessoryForQuery(string $nameAscii, string $firstTokenAscii, array $machineStems): bool
    {
        if (empty($machineStems)) return false;

        $accHeads = $this->getAccessoryHeadsAscii();
        $looksLikeAcc = in_array($firstTokenAscii, $accHeads, true);
        if (!$looksLikeAcc) return false;

        foreach ($machineStems as $stem) {
            if ($stem === '' ) continue;
            if (preg_match('/\bdo\s+' . preg_quote($stem, '/') . '[a-z0-9]*/u', $nameAscii)) {
                return true;
            }
        }
        return false;
    }

    private function computeRelevanceScore(string $name, string $query, array $queryFlexVariants = []): int
    {
        $score = 0;

        $nameLower  = mb_strtolower($name, 'UTF-8');
        $queryLower = mb_strtolower($query, 'UTF-8');
        $nameAscii  = $this->toAscii($nameLower);
        $queryAscii = $this->toAscii($queryLower);

        $nameCompact        = $this->normalizeCompact($nameLower);
        $queryCompact       = $this->normalizeCompact($queryLower);
        $nameAsciiCompact   = $this->normalizeCompact($nameAscii);
        $queryAsciiCompact  = $this->normalizeCompact($queryAscii);

        $nameTokens       = $this->tokenizeLower($nameLower);
        $queryTokens      = $this->tokenizeLower($queryLower);
        $nameTokensAscii  = $this->tokenizeLower($nameAscii);
        $queryTokensAscii = $this->tokenizeLower($queryAscii);

        $nameLemmas  = $this->getPseudoLemmas($nameLower);
        $queryLemmas = $this->getPseudoLemmas($queryLower);

        if (!empty($queryTokens)) {
            $maxStart = max(0, count($nameTokens) - count($queryTokens));
            for ($i = 0; $i <= $maxStart; $i++) {
                if (array_slice($nameTokens, $i, count($queryTokens)) === $queryTokens) { $score += 80; break; }
            }
        }
        if ($queryLower !== '' && in_array($queryLower, $nameTokens, true)) $score += 50;
        if ($queryLower !== '') foreach ($nameTokens as $t) { if (strpos($t, $queryLower) === 0) { $score += 30; break; } }

        if (!empty($queryTokensAscii)) {
            $maxStart = max(0, count($nameTokensAscii) - count($queryTokensAscii));
            for ($i = 0; $i <= $maxStart; $i++) {
                if (array_slice($nameTokensAscii, $i, count($queryTokensAscii)) === $queryTokensAscii) { $score += 60; break; }
            }
        }
        if ($queryAscii !== '' && in_array($queryAscii, $nameTokensAscii, true)) $score += 40;
        if ($queryAscii !== '') foreach ($nameTokensAscii as $t) { if (strpos($t, $queryAscii) === 0) { $score += 25; break; } }

        if ($queryCompact !== '' && $nameCompact !== '' && strpos($nameCompact, $queryCompact) !== false) $score += 25;
        if ($queryAsciiCompact !== '' && $nameAsciiCompact !== '' && strpos($nameAsciiCompact, $queryAsciiCompact) !== false) $score += 20;

        // Strong starts-with boosts
        if ($queryLower !== '' && mb_strpos($nameLower, $queryLower, 0, 'UTF-8') === 0) $score += 200;
        if ($queryCompact !== '' && mb_strpos($nameCompact, $queryCompact, 0, 'UTF-8') === 0) $score += 200;
        if ($queryAscii !== '' && mb_strpos($nameAscii, $queryAscii, 0, 'UTF-8') === 0) $score += 160;

        foreach ($queryFlexVariants as $variant) {
            if ($variant !== '' && mb_strpos($nameLower, $variant, 0, 'UTF-8') === 0) { $score += 170; break; }
        }

        if ($queryLower === 'cm' && $this->hasNumberCmPattern($nameLower)) $score += 5;

        if (!empty($nameLemmas) && !empty($queryLemmas)) {
            $inter = array_intersect($nameLemmas, $queryLemmas);
            if (!empty($inter)) $score += 50;
            $prefHit = false;
            foreach ($queryLemmas as $ql) foreach ($nameLemmas as $nl) {
                if ($ql !== '' && strpos($nl, $ql) === 0) { $prefHit = true; break 2; }
            }
            if ($prefHit) $score += 25;
        }

        $hasPluralInQuery = false; $singulars = [];
        foreach ($this->tokenizeLower($queryLower) as $qt) {
            if ($this->isProbablyPlural($qt)) { $hasPluralInQuery = true; $singulars = array_merge($singulars, $this->singularizeCandidates($qt)); }
        }
        $singulars = array_values(array_unique($singulars));

        if ($hasPluralInQuery && !empty($singulars)) {
            $singularBonusExact  = 140;
            $singularBonusPrefix = 80;
            $headBonus           = 80;

            $nameTokens      = $this->tokenizeLower($nameLower);
            $nameTokensAscii = $this->tokenizeLower($this->toAscii($nameLower));
            $firstToken      = $nameTokens[0] ?? '';
            $firstTokenAscii = $nameTokensAscii[0] ?? '';

            foreach ($singulars as $sing) {
                $singAscii = $this->toAscii($sing);

                if (in_array($sing, $nameTokens, true) || in_array($singAscii, $nameTokensAscii, true)) {
                    $score += $singularBonusExact;
                    if (($firstToken !== '' && $firstToken === $sing) || ($firstTokenAscii !== '' && $firstTokenAscii === $singAscii)) $score += $headBonus;
                    continue;
                }

                $prefixGiven = false;
                foreach ($nameTokens as $t) if (strpos($t, $sing) === 0) { $score += $singularBonusPrefix; $prefixGiven = true; break; }
                if (!$prefixGiven) foreach ($nameTokensAscii as $t) if (strpos($t, $singAscii) === 0) { $score += $singularBonusPrefix; break; }

                if (($firstToken !== '' && strpos($firstToken, $sing) === 0) ||
                    ($firstTokenAscii !== '' && strpos($firstTokenAscii, $singAscii) === 0)) {
                    $score += $headBonus;
                }
            }
        }

        $penDoPhrase    = $hasPluralInQuery ? 420 : 200;
        $penAccGeneric  = $hasPluralInQuery ? 360 : 120;
        $penAccGroup    = $hasPluralInQuery ? 380 : 180;

        foreach ($this->tokenizeLower($queryAscii) as $qtAscii) {
            if ($qtAscii !== '' && preg_match('/\bdo\s+' . preg_quote($qtAscii, '/') . '\b/u', $nameAscii)) {
                $score -= $penDoPhrase;
                break;
            }
        }

        $accHeadsGeneric = [
            'talerz','dysk','plyta','plyty','płyta','płyty',
            'noz','noze','noży','noże',
            'lopat','lopatki','łopat','łopatki',
            'holder','osprzet','osprzęt','elastomer',
            'pakiet','serwis','serwisowy',
        ];
        $firstTokenAsciiName = $this->toAscii($this->tokenizeLower($nameLower)[0] ?? '');
        $looksLikeAccessoryGeneric = in_array($firstTokenAsciiName, array_map([$this,'toAscii'], $accHeadsGeneric), true);

        if ($looksLikeAccessoryGeneric) {
            foreach ($this->tokenizeLower($queryAscii) as $qtAscii) {
                if ($qtAscii !== '' && strpos($nameAscii, $qtAscii) !== false) {
                    $score -= $penAccGeneric;
                    break;
                }
            }
        }

        if ($hasPluralInQuery && $looksLikeAccessoryGeneric) {
            static $msCache = [];
            $qa = $this->normAscii($query);
            if (!isset($msCache[$qa])) {
                $msCache[$qa] = $this->detectMachineStemsFromQuery($this->tokenizeLower($qa));
            }
            $machineStemsForQuery = $msCache[$qa];

            if (!empty($machineStemsForQuery)) {
                foreach ($machineStemsForQuery as $st) {
                    if ($st !== '' && strpos($nameAscii, $st) !== false) {
                        $score -= 520;
                        break;
                    }
                }
            }
        }

        static $accessoryGroups = null;
        static $accDiagLogged = false;
        if ($accessoryGroups === null) {
            $path = $this->getModuleFilePath('accessory_groups.php');
            $loaded = @include $path;
            if (is_array($loaded)) {
                $accessoryGroups = $loaded;
                if (!$accDiagLogged) {
                    $p = realpath($path) ?: $path;
                    // PrestaShopLogger::addLog('[AutocompleteStock] accessory_groups.php loaded OK (' . count($accessoryGroups) . ' groups) path=' . $p, 1);
                    $accDiagLogged = true;
                }
            } else {
                $accessoryGroups = [];
                if (!$accDiagLogged) {
                    $p = realpath($path) ?: $path;
                    // PrestaShopLogger::addLog('[AutocompleteStock] accessory_groups.php NOT loaded path=' . $p, 1);
                    $accDiagLogged = true;
                }
            }
        }

        foreach ($accessoryGroups as $groupStem => $accList) {
            $mentionsGroup = false;
            foreach ($this->tokenizeLower($queryAscii) as $qt) if (strpos($qt, $groupStem) === 0) { $mentionsGroup = true; break; }
            if (!$mentionsGroup) continue;

            $isAccessory = in_array($firstTokenAsciiName, array_map([$this,'toAscii'], $accList), true);
            if ($isAccessory && strpos($nameAscii, $groupStem) !== false) $score -= $penAccGroup;
            if (preg_match('/\bdo\s+' . preg_quote($groupStem, '/') . '[a-zęóąśłżźćń]*/u', $nameAscii)) $score -= $penAccGroup;
        }

        $machineRoots = $this->getMachineRoots();
        if (!empty($machineRoots)) {
            $nameTokens      = $this->tokenizeLower($nameLower);
            $firstTokAscii   = $this->toAscii($nameTokens[0] ?? '');
            foreach ($machineRoots as $root => $variants) {
                $rootAscii = $this->toAscii(mb_strtolower($root, 'UTF-8'));

                $mentions = false;
                foreach ($this->tokenizeLower($queryAscii) as $qt) {
                    if ($qt === $rootAscii || strpos($qt, $rootAscii) === 0) { $mentions = true; break; }
                }
                if (!$mentions && is_array($variants)) {
                    foreach ($variants as $v) {
                        $va = $this->toAscii(mb_strtolower((string)$v, 'UTF-8'));
                        foreach ($this->tokenizeLower($queryAscii) as $qt) {
                            if ($qt === $va || strpos($qt, $va) === 0) { $mentions = true; break 2; }
                        }
                    }
                }
                if (!$mentions) continue;

                if ($firstTokAscii !== '' &&
                    ($firstTokAscii === $rootAscii || strpos($firstTokAscii, $rootAscii) === 0)) {
                    $score += 260;
                } elseif (strpos($nameAscii, $rootAscii) === 0) {
                    $score += 210;
                }
            }
        }

        return $score;
    }

    private function sqlCompact(string $expr): string
    {
        $e = $expr;
        foreach ([' ', '-', '_', '/', '\\\\', '.', ','] as $ch) {
            $e = 'REPLACE(' . $e . ', ' . "'" . $ch . "'" . ', \'\')';
        }
        return 'LOWER(' . $e . ')';
    }

    private function sqlAscii(string $expr): string
    {
        $repl = [
            'ą' => 'a','ć' => 'c','ę' => 'e','ł' => 'l','ń' => 'n','ó' => 'o','ś' => 's','ź' => 'z','ż' => 'z',
            'Ą' => 'A','Ć' => 'C','Ę' => 'E','Ł' => 'L','Ń' => 'N','Ó' => 'O','Ś' => 'S','Ź' => 'Z','Ż' => 'Z',
        ];
        $e = $expr;
        foreach ($repl as $from => $to) {
            $e = 'REPLACE(' . $e . ", '" . $from . "', '" . $to . "')";
        }
        return 'LOWER(' . $e . ')';
    }

    private function getPseudoLemmas(string $q): array
    {
        $q = trim($q);
        if ($q === '') return [];

        $cacheKey = 'astock:pseudo_lemmas:' . md5($q);
        if ($cached = Cache::retrieve($cacheKey)) {
            $arr = json_decode($cached, true);
            if (is_array($arr)) return $arr;
        }

        $tokens = $this->tokenize($q);
        $lemmas = [];

        $nounSuffixes = ['owie','ami','ach','owi','om','em','ie','ów','ą','ę','y','a','e','i','u','o'];
        $adjSuffixes  = ['owych','owego','owemu','owymi','owej','owym','owy','owa','owe','ych','ymi','ego','emu','ej','ym','ie','ą','y','a','e','i','o'];
        $verbSuffixes = ['aliśmy','ałyśmy','ajcie','ajmy','ając','ają','acie','amy','ałem','ałam','ała','ało','ali','ały',
                         'esz','emy','eść','eć','esz','em','eś','ą','ę','ać','ić','ąć','ł','ła','ło','li','ły'];

        foreach ($tokens as $tok) {
            $w = mb_strtolower($tok, 'UTF-8');
            $lemmas[] = $w;
            $lemmas[] = $this->toAscii($w);

            $variants = [$w];
            foreach ([$nounSuffixes, $adjSuffixes, $verbSuffixes] as $sfxList) {
                $stripped = $w;
                foreach ($sfxList as $sfx) {
                    $len = mb_strlen($sfx, 'UTF-8');
                    if ($len >= 1 && mb_substr($w, -$len, null, 'UTF-8') === $sfx) {
                        $base = mb_substr($w, 0, mb_strlen($w, 'UTF-8') - $len, 'UTF-8');
                        if (mb_strlen($base, 'UTF-8') >= 3) { $stripped = $base; break; }
                    }
                }
                if ($stripped !== $w) $variants[] = $stripped;
            }
            foreach ($variants as $v) {
                $lemmas[] = $v;
                $lemmas[] = $this->toAscii($v);
            }
        }

        $lemmas = array_values(array_unique(array_filter($lemmas, function ($s) {
            $s = (string)$s;
            return $s !== '' && mb_strlen($s, 'UTF-8') >= 2;
        })));
        if (count($lemmas) > 16) $lemmas = array_slice($lemmas, 0, 16);

        Cache::store($cacheKey, json_encode($lemmas, JSON_UNESCAPED_UNICODE), self::LEMMA_CACHE_TTL);
        return $lemmas;
    }

    private function buildWhereWithLemmas(
        string $fieldExpr,
        string $like,
        string $collate,
        array $lemmas
    ): string {
        $parts   = [];
        $parts[] = $fieldExpr . ' COLLATE ' . $collate . ' LIKE "' . $like . '"';
        foreach ($lemmas as $lemma) {
            $likeLemma = '%' . pSQL($lemma, true) . '%';
            $parts[]   = $fieldExpr . ' COLLATE ' . $collate . ' LIKE "' . $likeLemma . '"';
        }
        return '(' . implode(' OR ', $parts) . ')';
    }

    private function buildWhereWithCompact(
        string $fieldExpr,
        string $like,
        string $collate,
        array $lemmas,
        string $compactQuery,
        array $compactLemmas,
        bool $queryHasSpace
    ): string {
        $parts = [];

        $parts[] = $this->buildWhereWithLemmas($fieldExpr, $like, $collate, $lemmas);

        $compactExpr      = $this->sqlCompact($fieldExpr);
        $asciiExpr        = $this->sqlAscii($fieldExpr);
        $asciiCompactExpr = $this->sqlCompact($asciiExpr);

        $lemmasAscii = array_values(array_unique(array_filter(array_map(function ($l) {
            return $this->toAscii((string)$l);
        }, $lemmas))));
        foreach ($lemmasAscii as $la) {
            $parts[] = $asciiExpr . ' LIKE "' . '%' . pSQL($la, true) . '%"';
        }

        if ($compactQuery !== '') {
            $parts[] = $compactExpr . ' LIKE "' . '%' . pSQL($compactQuery, true) . '%"';

            $compactQueryAscii = $this->normalizeCompact($this->toAscii($compactQuery));
            if ($compactQueryAscii !== '') {
                $parts[] = $asciiCompactExpr . ' LIKE "' . '%' . pSQL($compactQueryAscii, true) . '%"';
            }

            if ($queryHasSpace) {
                $parts[] = $fieldExpr . ' COLLATE ' . $collate . ' LIKE "' . '%' . pSQL($compactQuery, true) . '%"';
                if ($compactQueryAscii !== '') {
                    $parts[] = $asciiExpr . ' LIKE "' . '%' . pSQL($compactQueryAscii, true) . '%"';
                }
            }
        }

        foreach ($compactLemmas as $cl) {
            $parts[] = $compactExpr . ' LIKE "' . '%' . pSQL($cl, true) . '%"';

            $clA = $this->normalizeCompact($this->toAscii($cl));
            if ($clA !== '') {
                $parts[] = $asciiCompactExpr . ' LIKE "' . '%' . pSQL($clA, true) . '%"';
            }

            if (preg_match('/\p{L}{2,}\p{L}{2,}/u', $cl) && mb_strlen($cl, 'UTF-8') > 6) {
                $withSpace = preg_replace('/(\p{L}{2,})(\p{L}{2,})/u', '$1 $2', $cl, 1);
                if ($withSpace) {
                    $parts[] = $fieldExpr . ' COLLATE ' . $collate . ' LIKE "' . '%' . pSQL($withSpace, true) . '%"';
                }
                if ($clA !== '' && preg_match('/[a-z0-9]{2,}[a-z0-9]{2,}/i', $clA) && mb_strlen($clA, 'UTF-8') > 6) {
                    $withSpaceA = preg_replace('/([a-z0-9]{2,})([a-z0-9]{2,})/i', '$1 $2', $clA, 1);
                    if ($withSpaceA) {
                        $parts[] = $asciiExpr . ' LIKE "' . '%' . pSQL($withSpaceA, true) . '%"';
                    }
                }
            }
        }

        $parts = array_values(array_unique($parts));
        return '(' . implode(' OR ', $parts) . ')';
    }

    private function buildWhereWithAllTokens(string $fieldExpr, array $tokens, string $collate): string
    {
        $asciiExpr = $this->sqlAscii($fieldExpr);
        $parts = [];
        foreach ($tokens as $token) {
            $likeRaw = '%' . pSQL($token, true) . '%';
            $likeAsc = '%' . pSQL($this->toAscii((string)$token), true) . '%';
            $parts[] = '('
                . $fieldExpr . ' COLLATE ' . $collate . ' LIKE "' . $likeRaw . '"'
                . ' OR ' . $asciiExpr . ' LIKE "' . $likeAsc . '"'
                . ')';
        }
        if (empty($parts)) return '1';
        return '(' . implode(' AND ', $parts) . ')';
    }

    private function bestMatchPos(string $name, string $query): int
    {
        $min = PHP_INT_MAX;

        $pos = mb_stripos($name, $query, 0, 'UTF-8');
        if ($pos !== false) $min = min($min, (int)$pos);

        $posA = stripos($this->toAscii(mb_strtolower($name, 'UTF-8')), $this->toAscii(mb_strtolower($query, 'UTF-8')));
        if ($posA !== false) $min = min($min, (int)$posA);

        $posC = stripos($this->normalizeCompact($name), $this->normalizeCompact($query));
        if ($posC !== false) $min = min($min, (int)$posC);

        return $min;
    }

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $context = Context::getContext();
            $search  = (string) Tools::getValue('s');

            if ($search === '') {
                echo json_encode(['categories' => [], 'manufacturers' => [], 'products' => [], 'custom' => []], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $idLang = (int) $context->language->id;
            $idShop = (int) $context->shop->id;

            $searchLower = mb_strtolower($search, 'UTF-8');
            $searchAscii = $this->normAscii($search);

            // === cache key using ASCII+COMPACT (so "FH 5001" == "FH5001")
            $searchAsciiComp = $this->normalizeCompact($searchAscii);
            $cacheKeyBase    = $searchAsciiComp !== '' ? $searchAsciiComp : $searchAscii;
            $payloadCacheKey = 'astock:ac_payload:' . $idShop . ':' . $idLang . ':' . md5($cacheKeyBase);

            if ($cached = Cache::retrieve($payloadCacheKey)) {
                echo $cached;
                exit;
            }

            $showCats  = (bool) Configuration::get('ASTOCK_SHOW_CATEGORIES');
            $cLimit    = max(0, (int) Configuration::get('ASTOCK_CATEGORIES_LIMIT')); // 0 = bez limitu
            $showMans  = (bool) Configuration::get('ASTOCK_SHOW_MANUFACTURERS');
            $mLimit    = max(0, (int) Configuration::get('ASTOCK_MANUFACTURERS_LIMIT'));
            $pLimit    = max(1, (int) Configuration::get('ASTOCK_PRODUCTS_LIMIT'));
            $onlyAvail = (bool) Configuration::get('ASTOCK_ONLY_AVAILABLE');
            $imgType   = Configuration::get('ASTOCK_IMAGE_TYPE') ?: 'small_default';

            $like          = '%' . pSQL($search, true) . '%';
            $collate       = 'utf8mb4_polish_ci';
            $queryHasSpace = (mb_strpos($search, ' ', 0, 'UTF-8') !== false);

            $lemmas        = $this->getPseudoLemmas($search);
            $compactQuery  = $this->normalizeCompact($search);
            $compactLemmas = [];
            foreach ($lemmas as $l) {
                $c = $this->normalizeCompact($l);
                if ($c !== '' && $c !== $l) $compactLemmas[] = $c;
            }
            $tokensRaw = $this->tokenize($search);
            if ($queryHasSpace) $compactLemmas[] = $this->normalizeCompact($search);
            if (count($tokensRaw) > 1) $compactLemmas[] = $this->normalizeCompact(implode('', $tokensRaw));
            $compactLemmas = array_values(array_unique($compactLemmas));

            $queryFlexVariants = [];
            $flexSuffixes = ['y','i','a','e','ów','owy','owe','owa','ego','emu','ami','ach','om','em','ą','ę'];
            foreach ($flexSuffixes as $suffix) {
                $len = mb_strlen($suffix, 'UTF-8');
                if ($len > 0 && mb_substr($searchLower, -$len, null, 'UTF-8') === $suffix) {
                    $base = mb_substr($searchLower, 0, mb_strlen($searchLower, 'UTF-8') - $len, 'UTF-8');
                    if (mb_strlen($base, 'UTF-8') >= 3) {
                        foreach ($flexSuffixes as $otherSuffix) $queryFlexVariants[] = $base . $otherSuffix;
                        $queryFlexVariants[] = $base;
                    }
                }
            }
            $queryFlexVariants = array_values(array_unique($queryFlexVariants));

            $tokensAscii = $this->tokenizeLower($searchAscii);
            $this->logInfo('Input raw="' . $search . '" ascii="' . $searchAscii . '"');
            $this->logInfo('Tokens ASCII: ' . implode(', ', $tokensAscii));
            $this->logInfo('Compact: ' . $compactQuery);

            // ===== ref compact expr (used in manufacturers & products)
            $refCompactExpr = $this->sqlCompact('p.reference');
            $refCompactCond = $compactQuery !== '' ? (' OR ' . $refCompactExpr . ' LIKE ' . '"' . '%' . pSQL($compactQuery, true) . '%' . '"') : '';

           /* =========================
            * MANUFACTURERS
            * ========================= */
            $manufacturers = [];
            if ($showMans && $mLimit > 0) {

                // PrestaShopLogger::addLog('[AutocompleteStock][MAN] BEGIN manufacturers search="' . $search . '" compact="' . $compactQuery . '"', 1);

                // SKU-like: litery + cyfry po zbiciu (compact)
                $isSkuLike = ($compactQuery !== '' && preg_match('/[a-z]/i', $compactQuery) && preg_match('/\d/', $compactQuery));
                // PrestaShopLogger::addLog('[AutocompleteStock][MAN] isSkuLike=' . ($isSkuLike ? '1' : '0'), 1);

                // Literaly do ref_boost
                $searchLowerRaw = mb_strtolower($search, 'UTF-8');
                $likePrefixRaw  = pSQL($searchLowerRaw, true) . '%';
                $likePrefixComp = pSQL($compactQuery, true) . '%';
                $refLowerExpr   = 'LOWER(p.reference)';
                $refCompExpr    = $refCompactExpr;

                // Przyda się też compact(name) i asciiCompact(name) do "zawężenia SKU"
                $nameCompactExpr      = $this->sqlCompact('pl.name');
                $nameAsciiCompactExpr = $this->sqlCompact($this->sqlAscii('pl.name'));

                // ===== (A) Marki po NAZWIE =====
                $qM1 = new DbQuery();
                $qM1->select('m.id_manufacturer, m.name, COUNT(DISTINCT p.id_product) AS product_count')
                    ->from('manufacturer', 'm')
                    ->innerJoin('manufacturer_shop', 'ms', 'ms.id_manufacturer = m.id_manufacturer AND ms.id_shop = '.(int)$idShop)
                    ->leftJoin('product', 'p', 'p.id_manufacturer = m.id_manufacturer')
                    ->leftJoin('product_shop', 'ps',
                        'ps.id_product = p.id_product AND ps.id_shop = '.(int)$idShop.' AND ps.active = 1 AND ps.visibility IN ("both","search")')
                    ->leftJoin('product_lang', 'pl',
                        'pl.id_product = p.id_product AND pl.id_lang = '.(int)$idLang.' AND pl.id_shop = '.(int)$idShop)
                    ->where(
                        'm.active = 1 AND ' . $this->buildWhereWithCompact('m.name', $like, $collate, $lemmas, $compactQuery, $compactLemmas, $queryHasSpace)
                    )
                    ->groupBy('m.id_manufacturer, m.name')
                    ->orderBy('product_count DESC, m.name ASC')
                    ->limit($mLimit);

                // Dla SKU-like możesz całkiem wyłączyć M1, żeby nie łapać "marek po nazwie":
                if ($isSkuLike) {
                    $mansByName = [];
                    // PrestaShopLogger::addLog('[AutocompleteStock][MAN] mansByName SKIPPED for SKU-like', 1);
                } else {
                    $sqlM1 = method_exists($qM1, 'build') ? $qM1->build() : (string)$qM1;
                    // PrestaShopLogger::addLog('[AutocompleteStock][MAN] SQL M1 (by name): ' . $sqlM1, 1);
                    $mansByName = Db::getInstance()->executeS($qM1) ?: [];
                    // PrestaShopLogger::addLog('[AutocompleteStock][MAN] mansByName count=' . count($mansByName), 1);
                }

                // ===== (B) Marki z PRODUKTÓW + ref_boost =====
                $qM2 = new DbQuery();
                $qM2->select('
                    m.id_manufacturer,
                    m.name,
                    COUNT(DISTINCT p.id_product) AS product_count,
                    MAX(
                        (' . $refLowerExpr . ' = "' . pSQL($searchLowerRaw, true) . '")
                        OR ("' . pSQL($compactQuery, true) . '" <> "" AND ' . $refCompExpr . ' = "' . pSQL($compactQuery, true) . '")
                        OR (' . $refLowerExpr . ' LIKE "' . $likePrefixRaw . '")
                        OR ("' . pSQL($compactQuery, true) . '" <> "" AND ' . $refCompExpr . ' LIKE "' . $likePrefixComp . '")
                        OR ("' . pSQL($compactQuery, true) . '" <> "" AND ' . $refCompExpr . ' LIKE "%' . pSQL($compactQuery, true) . '%")
                    ) AS ref_boost
                ')
                    ->from('product', 'p')
                    ->innerJoin('product_shop', 'ps',
                        'ps.id_product = p.id_product AND ps.id_shop = '.(int)$idShop.' AND ps.active = 1 AND ps.visibility IN ("both","search")')
                    ->innerJoin('product_lang', 'pl',
                        'pl.id_product = p.id_product AND pl.id_lang = '.(int)$idLang.' AND pl.id_shop = '.(int)$idShop)
                    ->innerJoin('manufacturer', 'm', 'm.id_manufacturer = p.id_manufacturer AND m.active = 1')
                    ->innerJoin('manufacturer_shop', 'ms', 'ms.id_manufacturer = m.id_manufacturer AND ms.id_shop = '.(int)$idShop);

                $whereBase = '('
                    . $this->buildWhereWithCompact('pl.name', $like, $collate, $lemmas, $compactQuery, $compactLemmas, $queryHasSpace)
                    . ' OR p.reference LIKE "'.$like.'"'
                    . $refCompactCond
                    . ' OR p.ean13 LIKE "'.$like.'"'
                    . ')';

                if ($isSkuLike && $compactQuery !== '') {
                    $strictSkuCond = '('
                        . $nameCompactExpr . ' LIKE "%' . pSQL($compactQuery, true) . '%"'
                        . ' OR ' . $nameAsciiCompactExpr . ' LIKE "%' . pSQL($compactQuery, true) . '%"'
                        . ')';
                    $qM2->where($whereBase . ' AND ' . $strictSkuCond);
                    // PrestaShopLogger::addLog('[AutocompleteStock][MAN] SKU-like strict WHERE added', 1);
                } else {
                    $qM2->where($whereBase);
                }

                $qM2->groupBy('m.id_manufacturer, m.name')
                    ->orderBy('product_count DESC, m.name ASC')
                    ->limit($mLimit);

                $sqlM2 = method_exists($qM2, 'build') ? $qM2->build() : (string)$qM2;
                // PrestaShopLogger::addLog('[AutocompleteStock][MAN] SQL M2 (from products + ref_boost): ' . $sqlM2, 1);

                $mansFromProducts = Db::getInstance()->executeS($qM2) ?: [];
                // PrestaShopLogger::addLog('[AutocompleteStock][MAN] mansFromProducts count=' . count($mansFromProducts), 1);

                $mmap = [];
                foreach ([$mansByName, $mansFromProducts] as $lst) {
                    foreach ($lst as $r) {
                        $mid   = (int)$r['id_manufacturer'];
                        $boost = isset($r['ref_boost']) ? (int)$r['ref_boost'] : 0;
                        if (!isset($mmap[$mid])) {
                            $mmap[$mid] = [
                                'id_manufacturer' => $mid,
                                'name'            => $r['name'],
                                'product_count'   => (int)($r['product_count'] ?? 0),
                                'ref_boost'       => $boost,
                            ];
                        } else {
                            $mmap[$mid]['product_count'] = max($mmap[$mid]['product_count'], (int)($r['product_count'] ?? 0));
                            $mmap[$mid]['ref_boost']     = max((int)$mmap[$mid]['ref_boost'], $boost);
                        }
                    }
                }
                $manufacturers = array_values($mmap);

                if ($isSkuLike) {
                    $onlyRef = array_values(array_filter($manufacturers, function($m){ return (int)($m['ref_boost'] ?? 0) === 1; }));
                    if (!empty($onlyRef)) {
                        $manufacturers = $onlyRef;
                    }
                }

                $needle = (string)$search;
                usort($manufacturers, function($a, $b) use ($needle, $queryFlexVariants, $isSkuLike) {
                    if ($isSkuLike) {
                        $ra = (int)($a['ref_boost'] ?? 0);
                        $rb = (int)($b['ref_boost'] ?? 0);
                        if ($ra !== $rb) return $rb <=> $ra;
                    }
                    $sa = $this->computeRelevanceScore($a['name'], $needle, $queryFlexVariants);
                    $sb = $this->computeRelevanceScore($b['name'], $needle, $queryFlexVariants);
                    if ($sa !== $sb) return $sb <=> $sa;
                    $cmp = ($b['product_count'] ?? 0) <=> ($a['product_count'] ?? 0);
                    if ($cmp !== 0) return $cmp;
                    return strcasecmp($a['name'], $b['name']);
                });

                $manufacturers = array_slice($manufacturers, 0, $mLimit);

                foreach ($manufacturers as &$m) {
                    $m['url'] = $context->link->getManufacturerLink((int)$m['id_manufacturer']);
                    $logoPath = _PS_MANU_IMG_DIR_ . (int)$m['id_manufacturer'] . '-medium_default.jpg';
                    if (file_exists($logoPath)) {
                        $m['logo'] = $context->link->getMediaLink(_THEME_MANU_DIR_ . (int)$m['id_manufacturer'] . '-medium_default.jpg');
                    } else {
                        $m['logo'] = null;
                    }
                }
                unset($m);
            }

            /* =========================
             * PRODUCTS
             * ========================= */
            $sqlLimit = min(max($pLimit * 5, $pLimit), 50);

            $q = new DbQuery();
            $q->select('p.id_product, p.reference, pl.name, pl.link_rewrite, img.id_image')
              ->from('product', 'p')
              ->innerJoin('product_shop', 'ps',
                    'ps.id_product=p.id_product AND ps.id_shop='.(int)$idShop.' AND ps.active=1 AND ps.visibility IN ("both","search")')
              ->innerJoin('product_lang', 'pl',
                    'pl.id_product=p.id_product AND pl.id_lang='.(int)$idLang.' AND pl.id_shop='.(int)$idShop)
              ->leftJoin('image', 'img', 'img.id_product=p.id_product AND img.cover=1')
              ->where(
                  $this->buildWhereWithCompact('pl.name', $like, $collate, $lemmas, $compactQuery, $compactLemmas, $queryHasSpace)
                  . ' OR p.reference LIKE "'.$like.'"'
                  . $refCompactCond
                  . ' OR p.ean13 LIKE "'.$like.'"'
                  . ' OR ' . $this->buildWhereWithAllTokens('pl.name', $this->tokenize($search), $collate)
              );

            $nameLowerExpr    = 'LOWER(pl.name)';
            $nameCompactExpr  = $this->sqlCompact('pl.name');
            $nameAsciiExpr    = $this->sqlAscii('pl.name');
            $nameAsciiCompact = $this->sqlCompact($this->sqlAscii('pl.name'));
            $tokensForPresence = $this->tokenizeLower($searchAscii);

            $mkGroup = function(string $tok) use ($collate, $nameLowerExpr, $nameCompactExpr, $nameAsciiExpr, $nameAsciiCompact): string {
                $t = mb_strtolower($tok, 'UTF-8');

                $cands = [$t];

                $stemT = $this->smartStemKey($t);
                if ($stemT !== '' && $stemT !== $t) $cands[] = $stemT;

                foreach (array_unique(array_merge([$t], $this->singularizeCandidates($t))) as $base) {
                    if ($base === '') continue;
                    $cands[] = $base;
                    $sb = $this->smartStemKey($base);
                    if ($sb !== '' && $sb !== $base) $cands[] = $sb;
                }

                if (preg_match('/[yie]$/u', $t) === 1) {
                    $cands[] = mb_substr($t, 0, mb_strlen($t, 'UTF-8') - 1, 'UTF-8');
                }

                $cands = array_values(array_unique(array_filter($cands)));

                $parts = [];
                foreach ($cands as $alt) {
                    $altAscii  = $this->toAscii($alt);
                    $likeRaw   = '%' . pSQL($alt, true) . '%';
                    $likeAsc   = '%' . pSQL($altAscii, true) . '%';
                    $likeComp  = '%' . pSQL($this->normalizeCompact($alt), true) . '%';
                    $likeAComp = '%' . pSQL($this->normalizeCompact($altAscii), true) . '%';

                    $parts[] = '('
                        . $nameLowerExpr     . ' COLLATE '.$collate.' LIKE "'.$likeRaw.'"'
                        . ' OR ' . $nameAsciiExpr    . ' LIKE "'.$likeAsc.'"'
                        . ' OR ' . $nameCompactExpr  . ' LIKE "'.$likeComp.'"'
                        . ' OR ' . $nameAsciiCompact . ' LIKE "'.$likeAComp.'"'
                        . ')';
                }

                return '(' . implode(' OR ', array_values(array_unique($parts))) . ')';
            };

            $mustGroups = [];
            foreach ($tokensForPresence as $tok) {
                if ($tok === '') continue;
                $mustGroups[] = $mkGroup($tok);
            }

            $presenceMust = '1';
            if (!empty($mustGroups)) {
                $presenceMust = '(' . implode(' AND ', $mustGroups) . ')';
            }

            $presenceGlue = '';
            if (count($tokensForPresence) >= 2) {
                $glued = $this->normalizeCompact(implode(' ', $tokensForPresence));
                if ($glued !== '') {
                    $presenceGlue = '('
                        . $nameCompactExpr  . ' LIKE "%'.pSQL($glued, true).'%"'
                        . ' OR ' . $nameAsciiCompact . ' LIKE "%'.pSQL($glued, true).'%"'
                        . ')';
                }
            }

            $presenceStrongRef = '('
                . 'p.reference LIKE "'.$like.'"'
                . $refCompactCond
                . ' OR p.ean13 LIKE "'.$like.'"'
                . ')';

            $presenceFinal = $presenceMust;
            if ($presenceGlue !== '') {
                $presenceFinal = '(' . $presenceFinal . ' OR ' . $presenceGlue . ')';
            }
            $presenceFinal = '(' . $presenceFinal . ' OR ' . $presenceStrongRef . ')';

            $isSkuLikeStrict = ($compactQuery !== '' && preg_match('/^(?=.*[a-z])(?=.*\d)[a-z0-9]{3,}$/i', $compactQuery) === 1);
            $tokensForCats   = $this->tokenize($search);

            $homeId = (int) Configuration::get('PS_HOME_CATEGORY');
            $root   = Category::getRootCategory();
            $rootId = $root ? (int) $root->id : 0;

            $catNameWhereBase =
                'c.active = 1 AND c.id_category NOT IN ('.(int)$homeId.','.(int)$rootId.') AND ' .
                $this->buildWhereWithCompact('cl.name', $like, $collate, $lemmas, $compactQuery, $compactLemmas, $queryHasSpace);

            if (!$isSkuLikeStrict && count($tokensForCats) >= 2) {
                $catNameWhereBase = '(' . $catNameWhereBase . ') AND ' .
                    $this->buildWhereWithAllTokens('cl.name', $tokensForCats, $collate);
            }

            $catExistsSql = 'EXISTS (
                SELECT 1
                FROM '._DB_PREFIX_.'category_product cp
                INNER JOIN '._DB_PREFIX_.'category c ON c.id_category = cp.id_category AND c.active = 1
                INNER JOIN '._DB_PREFIX_.'category_lang cl ON cl.id_category = c.id_category
                    AND cl.id_lang = '.(int)$idLang.' AND cl.id_shop = '.(int)$idShop.'
                WHERE cp.id_product = p.id_product
                  AND ' . $catNameWhereBase . '
            )';

            $finalProductsWhere = $presenceFinal;
            if (!$isSkuLikeStrict) {
                $finalProductsWhere = '(' . $presenceFinal . ' OR ' . $catExistsSql . ')';
            }

            $q->where($finalProductsWhere);
            $this->logInfo('Presence: groups=' . count($mustGroups) . '; glue=' . ($presenceGlue ? 'yes' : 'no') . '; tokens(ascii)=' . implode(' ', $tokensForPresence));
            $this->logInfo('Products WHERE extended with category EXISTS: ' . (!$isSkuLikeStrict ? 'YES' : 'NO') . ' (isSkuLikeStrict=' . ($isSkuLikeStrict ? '1' : '0') . ')');

            $q->limit($sqlLimit);

            $sqlString = method_exists($q, 'build') ? $q->build() : (string)$q;
            $this->logInfo('SQL: ' . $sqlString);

            $rows = Db::getInstance()->executeS($q);
            $this->logInfo('DB rows fetched: ' . count($rows));

            $queryIsPlural = false;
            $queryTokensLower2 = $this->tokenizeLower($searchAscii);
            foreach ($queryTokensLower2 as $qt) {
                if ($this->isProbablyPlural($qt)) { $queryIsPlural = true; break; }
            }

            $qTokensAsciiForFilter = $this->tokenizeLower($searchAscii);
            $machineStems = $this->detectMachineStemsFromQuery($qTokensAsciiForFilter);

            $pluralSingulars = [];
            foreach ($queryTokensLower2 as $qt) {
                if ($this->isProbablyPlural($qt)) {
                    $pluralSingulars = array_merge($pluralSingulars, $this->singularizeCandidates($qt));
                }
            }
            $pluralSingulars = array_values(array_unique(array_filter($pluralSingulars)));
            $fallbackStemsFromSing = $this->stemsFromSingulars($pluralSingulars);

            if (empty($machineStems) && !empty($fallbackStemsFromSing)) {
                $machineStems = $fallbackStemsFromSing;
                $this->logInfo('Using fallback stems from singulars; stems='
                    . implode(',', array_slice($machineStems, 0, 5)) . (count($machineStems) > 5 ? '…' : ''));
            }

            $this->logInfo('Machine stems from query: ' . implode(', ', $machineStems));

            $link     = $context->link;
            $productsNonAcc = [];
            $productsAll    = [];

            $searchLowerInput   = $searchLower;
            $searchCompactInput = $compactQuery;

            foreach ($rows as $r) {
                $qty = (int) StockAvailable::getQuantityAvailableByProduct((int)$r['id_product'], 0, $idShop);
                if ($onlyAvail && $qty <= 0) continue;

                $imgUrl = !empty($r['id_image'])
                    ? $link->getImageLink($r['link_rewrite'], (int)$r['id_image'], $imgType)
                    : $link->getMediaLink(_THEME_IMG_DIR_.'no-picture-'.$imgType.'.jpg');

                $refRaw   = (string)($r['reference'] ?? '');
                $refLower = mb_strtolower($refRaw, 'UTF-8');
                $refComp  = $this->normalizeCompact($refLower);

                $rawEq      = ($refLower !== '' && $refLower === $searchLowerInput);
                $rawPrefix  = ($refLower !== '' && $searchLowerInput !== '' && mb_strpos($refLower, $searchLowerInput, 0, 'UTF-8') === 0);
                $compEq     = ($refComp !== '' && $refComp === $searchCompactInput);
                $compPrefix = ($refComp !== '' && $searchCompactInput !== '' && strpos($refComp, $searchCompactInput) === 0);
                $refBoost   = ($rawEq || $rawPrefix || $compEq || $compPrefix) ? 1 : 0;

                $rowData = [
                    'id_product'   => (int) $r['id_product'],
                    'name'         => $r['name'],
                    'quantity'     => $qty,
                    'link_rewrite' => $r['link_rewrite'],
                    'url'          => $link->getProductLink((int)$r['id_product']),
                    'cover'        => ['bySize' => [$imgType => ['url' => $imgUrl]]],
                    'ref_boost'    => $refBoost,
                ];

                $productsAll[] = $rowData;

                if ($queryIsPlural && !empty($machineStems)) {
                    $nameAsciiRow  = $this->toAscii(mb_strtolower($r['name'], 'UTF-8'));
                    $nameTokensRow = $this->tokenizeLower(mb_strtolower($r['name'], 'UTF-8'));
                    $firstTokAscii = $this->toAscii($nameTokensRow[0] ?? '');

                    if ($this->isAccessoryForQuery($nameAsciiRow, $firstTokAscii, $machineStems)) {
                        static $accFilterLogged = false;
                        if (!$accFilterLogged) {
                            $this->logInfo('hard-filter: accessory removed (plural query)');
                            $accFilterLogged = true;
                        }
                        continue;
                    }
                }

                $productsNonAcc[] = $rowData;
            }

            $this->logInfo('Products fetched=' . count($productsAll) . '; afterNonAcc=' . count($productsNonAcc) . '; final(before cut)=' . count($productsNonAcc));

            $products = $productsNonAcc;
            if (empty($products) && !empty($productsAll)) {
                $this->logInfo('hard-filter removed all; fallback includes accessories');
                $products = $productsAll;
            }

            $needle = (string)$search;
            $flexVariants = $queryFlexVariants;
            usort($products, function ($a, $b) use ($needle, $flexVariants) {
                $ra = (int)($a['ref_boost'] ?? 0);
                $rb = (int)($b['ref_boost'] ?? 0);
                if ($ra !== $rb) return $rb <=> $ra;

                $az = (int)(($a['quantity'] ?? 0) > 0);
                $bz = (int)(($b['quantity'] ?? 0) > 0);
                if ($az !== $bz) return $bz <=> $az;

                $scoreA = $this->computeRelevanceScore($a['name'], $needle, $flexVariants);
                $scoreB = $this->computeRelevanceScore($b['name'], $needle, $flexVariants);
                if ($scoreA !== $scoreB) return $scoreB <=> $scoreA;

                $pa = $this->bestMatchPos($a['name'], $needle);
                $pb = $this->bestMatchPos($b['name'], $needle);
                if ($pa !== $pb) return $pa <=> $pb;

                $cmp = ($b['quantity'] ?? 0) <=> ($a['quantity'] ?? 0);
                if ($cmp !== 0) return $cmp;

                $la = strlen($a['name'] ?? '');
                $lb = strlen($b['name'] ?? '');
                if ($la !== $lb) return $la <=> $lb;

                return ($a['id_product'] ?? 0) <=> ($b['id_product'] ?? 0);
            });

           /* =========================
            * CATEGORIES (Szukaj w) – NA PODSTAWIE ZNALEZIONYCH PRODUKTÓW
            * ========================= */
            $categories = [];
            if ($showCats) {
                // PrestaShopLogger::addLog('[AutocompleteStock][CAT] BEGIN categories search="' . $search . '" compact="' . $compactQuery . '"', 1);

                $isSkuLikeStrict = ($compactQuery !== '' && preg_match('/^(?=.*[a-z])(?=.*\d)[a-z0-9]{3,}$/i', $compactQuery) === 1);
                // PrestaShopLogger::addLog('[AutocompleteStock][CAT] isSkuLikeStrict=' . ($isSkuLikeStrict ? '1' : '0'), 1);

                $sourceForCats = !empty($productsNonAcc) ? $productsNonAcc : $productsAll;
                $prodIds = array_values(array_unique(array_map(function ($p) {
                    return (int)($p['id_product'] ?? 0);
                }, $sourceForCats)));
                $prodIds = array_filter($prodIds);

                $homeId = (int) Configuration::get('PS_HOME_CATEGORY');
                $root   = Category::getRootCategory();
                $rootId = $root ? (int) $root->id : 0;

                $catIds = [];

                if (!empty($prodIds)) {
                    $qC = new DbQuery();
                    $qC->select('
                        c.id_category,
                        cl.name,
                        MIN(cs.position) AS position,
                        COUNT(DISTINCT cp.id_product) AS product_count
                    ')
                    ->from('category_product', 'cp')
                    ->innerJoin('category', 'c', 'c.id_category = cp.id_category AND c.active = 1')
                    ->innerJoin('category_shop', 'cs', 'cs.id_category = c.id_category AND cs.id_shop = '.(int)$idShop)
                    ->innerJoin('category_lang', 'cl', 'cl.id_category = c.id_category AND cl.id_lang = '.(int)$idLang.' AND cl.id_shop = '.(int)$idShop)
                    ->where('c.id_category NOT IN ('.(int)$homeId.','.(int)$rootId.') AND cp.id_product IN ('.implode(',', array_map('intval', $prodIds)).')')
                    ->groupBy('c.id_category, cl.name')
                    ->orderBy('product_count DESC, cl.name ASC');

                    if ($cLimit > 0) { $qC->limit((int)$cLimit); }

                    $categories = Db::getInstance()->executeS($qC) ?: [];
                    $catIds = array_column($categories, 'id_category');
                }

                $qC2 = new DbQuery();
                $qC2->select('c.id_category, cl.name, cs.position')
                    ->from('category', 'c')
                    ->innerJoin('category_lang', 'cl', 'cl.id_category = c.id_category AND cl.id_lang = '.(int)$idLang.' AND cl.id_shop = '.(int)$idShop)
                    ->innerJoin('category_shop', 'cs', 'cs.id_category = c.id_category AND cs.id_shop = '.(int)$idShop);

                $whereCatsByNameBase =
                    'c.active = 1 AND c.id_category NOT IN ('.(int)$homeId.','.(int)$rootId.') AND ' .
                    $this->buildWhereWithCompact('cl.name', $like, $collate, $lemmas, $compactQuery, $compactLemmas, $queryHasSpace);

                $nameCompactExpr      = $this->sqlCompact('cl.name');
                $nameAsciiCompactExpr = $this->sqlCompact($this->sqlAscii('cl.name'));

                $searchLowerRaw = mb_strtolower($search, 'UTF-8');
                $strongCatNameHit = '('
                . 'cl.name COLLATE '.$collate.' LIKE "' . pSQL($search, true) . '%"'
                . ' OR ' . $this->sqlAscii('cl.name') . ' LIKE "' . pSQL($this->toAscii($searchLowerRaw), true) . '%"'
                . ' OR ' . $nameCompactExpr . ' LIKE "' . pSQL($compactQuery, true) . '%"'
                . ')';

                if ($isSkuLikeStrict && $compactQuery !== '') {
                    $strictSkuCond = '('
                        . $nameCompactExpr . ' LIKE "%' . pSQL($compactQuery, true) . '%"'
                        . ' OR ' . $nameAsciiCompactExpr . ' LIKE "%' . pSQL($compactQuery, true) . '%"'
                        . ')';

                    $qC2->where('((' . $whereCatsByNameBase . ') AND ' . $strictSkuCond . ') OR ' . $strongCatNameHit);
                } else {
                    $qC2->where($whereCatsByNameBase);
                }

                $qC2->orderBy('cs.position ASC, cl.name ASC');
                if ($cLimit > 0) { $qC2->limit((int)$cLimit); }

                $matchedCats = Db::getInstance()->executeS($qC2) ?: [];

                foreach ($matchedCats as $mc) {
                    if (!in_array($mc['id_category'], $catIds, true)) {
                        $categories[] = [
                            'id_category'   => (int) $mc['id_category'],
                            'name'          => $mc['name'],
                            'position'      => $mc['position'],
                            'product_count' => 0,
                        ];
                        $catIds[] = (int)$mc['id_category'];
                    }
                }

                $hasStrongHit = function(string $name) use ($search, $compactQuery) : bool {
                    $n = mb_strtolower($name, 'UTF-8');
                    $raw = mb_strtolower($search, 'UTF-8');
                    $asc = $this->toAscii($n);
                    $rawAsc = $this->toAscii($raw);
                    $comp = $this->normalizeCompact($n);
                    return ($raw !== '' && strpos($n, $raw) === 0)
                        || ($rawAsc !== '' && strpos($asc, $rawAsc) === 0)
                        || ($compactQuery !== '' && strpos($comp, $compactQuery) === 0);
                };

                usort($categories, function($a, $b) use ($search, $queryFlexVariants, $hasStrongHit) {
                    $ha = $hasStrongHit((string)$a['name']);
                    $hb = $hasStrongHit((string)$b['name']);
                    if ($ha !== $hb) return $hb <=> $ha;

                    $sa = $this->computeRelevanceScore((string)$a['name'], (string)$search, $queryFlexVariants);
                    $sb = $this->computeRelevanceScore((string)$b['name'], (string)$search, $queryFlexVariants);
                    if ($sa !== $sb) return $sb <=> $sa;

                    $pa = (int)($a['product_count'] ?? 0);
                    $pb = (int)($b['product_count'] ?? 0);
                    if ($pa !== $pb) return $pb <=> $pa;

                    return strcasecmp((string)$a['name'], (string)$b['name']);
                });

                foreach ($categories as &$c) {
                    $c['url'] = $context->link->getCategoryLink((int)$c['id_category']);
                }
                unset($c);
            }

            // Przycięcie produktów do limitu
            $products = array_slice($products, 0, $pLimit);

            /* =========================
             * CUSTOM ENTRIES (banerki/linki z modułu) — filtrowane po kategoriach
             * ========================= */
            $custom = [];
            try {
                $now = date('Y-m-d H:i:00');

                // Zbierz ID dopasowanych kategorii (po wcześniejszym wyszukiwaniu)
                $matchedCatIds = [];
                foreach ((array)$categories as $catRow) {
                    $cid = (int)($catRow['id_category'] ?? 0);
                    if ($cid > 0) $matchedCatIds[$cid] = true;
                }
                $matchedIdsList = implode(',', array_map('intval', array_keys($matchedCatIds)));

                // Budujemy warunek:
                // - jeśli są dopasowane kategorie → bierz globalne (NULL) OR przypisane do tych kategorii
                // - jeśli nie ma dopasowanych kategorii → tylko globalne (NULL)
                $catWhere = '(ac.id_category IS NULL OR ac.id_category = 0)';
                    if ($matchedIdsList !== '') {
                        $catWhere = '(' . $catWhere
                            . ' OR ac.id_category IN (' . $matchedIdsList . '))';
                    }
                $sql = '
                    SELECT ac.id_entry, ac.title, ac.link, ac.image, ac.alt, ac.id_category, cl.name AS category_name
                    FROM `'._DB_PREFIX_.'autocomplete_stock_custom` ac
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                      ON cl.id_category = ac.id_category
                     AND cl.id_lang = '.(int)$idLang.'
                     AND cl.id_shop = '.(int)$idShop.'
                    WHERE (ac.date_start IS NULL OR ac.date_start <= "'.pSQL($now).'")
                      AND (ac.date_end   IS NULL OR ac.date_end   >= "'.pSQL($now).'")
                      AND ' . $catWhere . '
                    ORDER BY ac.position ASC, ac.id_entry DESC
                    LIMIT 12
                ';

                $rowsCustom = Db::getInstance()->executeS($sql) ?: [];

                foreach ($rowsCustom as $r) {
                    if (empty($r['image'])) { continue; }

                    // pełny URL do /modules/autocomplete_stock/uploads/<plik>
                    $rel = $this->module->getPathUri() . 'uploads/' . rawurlencode($r['image']);
                    $imgUrl = $context->link->getMediaLink($rel);

                    $custom[] = [
                        'id'             => (int)$r['id_entry'],
                        'title'          => (string)$r['title'],
                        'alt'            => (string)$r['alt'],
                        'link'           => (string)$r['link'],
                        'image'          => $imgUrl,
                        'id_category'    => $r['id_category'] !== null ? (int)$r['id_category'] : null,
                        'category_name'  => $r['category_name'] ?? null,
                    ];
                }
                $this->logInfo('Custom entries fetched: ' . count($custom) . ' (catFiltered='.(empty($matchedCatIds)?'global-only':'global+matched').')');
            } catch (Exception $e) {
                $this->logInfo('Custom entries error: ' . $e->getMessage());
                $custom = [];
            }

            // RETURN – z 'custom' zawierającym id_category i category_name
            $payload = json_encode([
                'categories'    => $categories,
                'manufacturers' => $manufacturers,
                'products'      => $products,
                'custom'        => $custom,
            ], JSON_UNESCAPED_UNICODE);

            Cache::store($payloadCacheKey, $payload, self::AC_PAYLOAD_TTL);
            echo $payload;
            exit;

        } catch (Exception $e) {
            // PrestaShopLogger::addLog('AutocompleteStock AJAX error: '.$e->getMessage(), 3, 0, __CLASS__, 0, true);
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['categories' => [], 'manufacturers' => [], 'products' => [], 'custom' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
