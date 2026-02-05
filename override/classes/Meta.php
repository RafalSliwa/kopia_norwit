<?php

class Meta extends MetaCore
{
    /**
     * Get product meta tags.
     */
    public static function getProductMetas($idProduct, $idLang, $pageName)
    {
        $product = new Product($idProduct, false, $idLang);
        if (Validate::isLoadedObject($product) && $product->active) {
            $row = Meta::getPresentedObject($product);
            if (empty($row['meta_description'])) {
                $row['meta_description'] = 'Norwit.pl ➤ '.$row['name'].' w atrakcyjnej Cenie ✔️ Sklep budowlany ✔️ Nowoczesne maszyny i akcesoria ✔️ Szeroka oferta ⭐ Kup!';
            }
            if (empty($row['meta_title'])) {
                $row['meta_title'] = $row['name'].' ('.$idProduct.')'.' | Dobra Cena | Sklep Online - Norwit.pl';
            }

            return Meta::completeMetaTags($row, $row['name']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }
        
    
    public static function getCategoryMetas($idCategory, $idLang, $pageName, $title = '')
    {
        $category = new Category($idCategory, $idLang);

        $cacheId = 'Meta::getCategoryMetas' . (int) $idCategory . '-' . (int) $idLang;
        if (!Cache::isStored($cacheId)) {
            if (Validate::isLoadedObject($category)) {
                $row = Meta::getPresentedObject($category);
                if (empty($row['meta_description'])) {
                    $row['meta_description'] = 'Norwit.pl ➤ '.$row['name'].' ✔️ Sklep budowlany ✔️ Nowoczesne maszyny i akcesoria ✔️ Szeroka oferta ⭐ Zobacz!';
                }

                if (is_string($title) && $title !== '') {
                    $row['meta_title'] = $title;
                } else {
                    $row['meta_title'] = $row['meta_title'] ?: $row['name'] . ' | Dobre Ceny | Sklep Online - Norwit.pl';
                }

                $result = Meta::completeMetaTags($row, $row['name']);
            } else {
                $result = Meta::getHomeMetas($idLang, $pageName);
            }
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }
    
    public static function getManufacturerMetas($idManufacturer, $idLang, $pageName)
    {
        $manufacturer = new Manufacturer($idManufacturer, $idLang);
        $pageNumber = (int) Tools::getValue('page');
        if (Validate::isLoadedObject($manufacturer)) {
            $row = Meta::getPresentedObject($manufacturer);
            if (empty($row['meta_description'])) {
                $row['meta_description'] = 'Norwit.pl ➤ Produkty producenta: '.$row['meta_title']. (!empty($pageNumber) ? ' (' . $pageNumber . ')' : '').' ✔️ Sklep budowlany ✔️ Nowoczesne maszyny i akcesoria ✔️ Szeroka oferta ⭐ Zobacz!';
            }
            $row['meta_title'] = $row['meta_title'] ?: $row['name'] .  (!empty($pageNumber) ? ' (' . $pageNumber . ')' : '') . ' - oferta producenta | Dobre Ceny | Sklep Online - Norwit.pl';

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }
    

    /**
     * Get CMS meta tags.
     */
    public static function getCmsMetas($idCms, $idLang, $pageName)
    {
        $cms = new CMS($idCms, $idLang);
        if (Validate::isLoadedObject($cms)) {
            $row = Meta::getPresentedObject($cms);
            $row['meta_title'] = !empty($row['head_seo_title']) ? $row['head_seo_title'] : $row['meta_title'] . ' - Norwit.pl';
            if (empty($row['meta_description'])) {
                $row['meta_description'] = 'Norwit.pl ➤ '.str_replace(" - Norwit.pl","",$row['meta_title']).' ✔️ Sklep budowlany ✔️ Nowoczesne maszyny i akcesoria ✔️ Szeroka oferta ⭐ Sprawdź!';
            }
            
            

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }
}
