<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/exceptions/EtsSeoChatGptException.php';
require_once __DIR__ . '/traits/EtsSeoTranslationTrait.php';
require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';
/**
 * Class EtsSeoChatGpt
 *
 * @since 2.5.3
 */
class EtsSeoChatGpt
{
    use EtsSeoTranslationTrait;
    use EtsSeoGetInstanceTrait;
    /**
     * @var bool
     */
    private $active;
    /**
     * @var string
     */
    private $apiToken;

    /**
     * EtsSeoChatGpt constructor.
     */
    public function __construct()
    {
        $this->active = (bool) Configuration::get('ETS_SEO_CHAT_GPT_ENABLE');
        if ($this->active) {
            $token = (string) Configuration::get('ETS_SEO_CHAT_GPT_API_TOKEN');
            if (!strlen($token)) {
                $this->active = false;
            }
            $this->apiToken = $token;
        }
        if ((bool) Configuration::get('ETS_TRANS_ENABLE_CHATGPT')) {
            $this->active = false;
        }
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string $message
     * @param bool $fullResult
     *
     * @return string
     *
     * @throws \EtsSeoChatGptException
     */
    public function chat($message, $fullResult = false)
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiToken,
        ];
        $temperature = 0.7;
        $max_tokens = 4000;
        $top_p = 1;
        $frequency_penalty = 0;
        $presence_penalty = 0;
        $data = [
            'model' => 'gpt-3.5-turbo',
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
            'top_p' => $top_p,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
            'stop' => '[" Human:", " AI:"]',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => str_replace('"', '', urldecode($message)),
                ],
            ],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        if ($result && ($result = json_decode($result, true))) {
            if (isset($result['error']) && $result['error']) {
                if (isset($result['error']['message']) && $result['error']['message']) {
                    throw new \EtsSeoChatGptException(sprintf($this->l('ChatGPT API returned error: %s'), $result['error']['message']), -101);
                }

                throw new \EtsSeoChatGptException($this->l('ChatGPT API request failed'), -100);
            }
            if (isset($result['choices']) && ($choices = $result['choices']) && isset($choices[0]['message']['content']) && $choices[0]['message']['content']) {
                return $fullResult ? $result : $choices[0]['message']['content'];
            }

            throw new \EtsSeoChatGptException($this->l('ChatGPT API request failed'), -2);
        }

        throw new \EtsSeoChatGptException($this->l('ChatGPT API request failed'), -3);
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public static function installDefaultGptTemplates()
    {
        if (!class_exists(EtsSeoGptTemplate::class)) {
            require_once __DIR__ . '/EtsSeoGptTemplate.php';
        }
        if (!class_exists(Ets_Seo_Define::class)) {
            require_once __DIR__ . '/../defines.php';
        }
        $list = Ets_Seo_Define::getInstance()->getGptTemplates();
        $rs = true;
        foreach ($list as $pageType => $values) {
            foreach ($values as $item) {
                $template = new EtsSeoGptTemplate();
                $template->label = $item['label'];
                $template->display_page = $pageType;
                $template->content = $item['content'];
                $rs &= $template->save();
            }
        }

        return $rs;
    }
}
