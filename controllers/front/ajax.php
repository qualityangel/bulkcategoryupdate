<?php

class BulkCategoryUpdateAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Haal de actie op en roep de juiste methode aan
        $action = Tools::getValue('action');
        if ($action === 'getProductsByCategory') {
            $this->getProductsByCategory();
        } else {
            $this->ajaxDie(json_encode(['error' => 'Invalid action']));
        }
    }

    private function getProductsByCategory()
    {
        try {
            $categoryId = (int) Tools::getValue('category_id');
            if (!$categoryId) {
                $this->ajaxDie(json_encode(['error' => 'Invalid category ID']));
            }

            // Controleer of de categorie bestaat
            $category = new Category($categoryId, $this->context->language->id);
            if (!Validate::isLoadedObject($category)) {
                $this->ajaxDie(json_encode(['error' => 'Category not found']));
            }

            // SQL-query om producten op te halen
            $sql = 'SELECT 
                        p.`id_product`, 
                        pl.`name`, 
                        p.`id_category_default` AS default_category_id,
                        GROUP_CONCAT(cp.`id_category`) AS category_ids,
                        i.`id_image`
                    FROM `' . _DB_PREFIX_ . 'product` p
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.`id_product` = cp.`id_product`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (p.`id_product` = i.`id_product` AND i.cover = 1)
                    WHERE cp.`id_category` = ' . (int) $categoryId . ' 
                    AND pl.`id_lang` = ' . (int) $this->context->language->id . '
                    AND p.`active` = 1
                    GROUP BY p.`id_product`';

            $products = Db::getInstance()->executeS($sql);

            if (empty($products)) {
                $this->ajaxDie(json_encode([]));
            }

            foreach ($products as &$product) {
                // URL voor afbeelding
                $product['image_url'] = $product['id_image']
                    ? $this->context->link->getImageLink($product['name'], $product['id_product'] . '-' . $product['id_image'], 'small_default')
                    : $this->context->link->getImageLink('default', 'default', 'small_default');

                // Huidige categorieën
                $categories = explode(',', $product['category_ids']);
                $product['categories'] = [];
                foreach ($categories as $catId) {
                    $cat = new Category((int) $catId, $this->context->language->id);
                    if (Validate::isLoadedObject($cat)) {
                        $product['categories'][] = $cat->name;
                    }
                }

                // Combineer categorieën in een string
                $product['categories'] = implode(', ', $product['categories']);

                // Hoofdcategorie
                $defaultCategory = new Category((int) $product['default_category_id'], $this->context->language->id);
                $product['default_category'] = Validate::isLoadedObject($defaultCategory) ? $defaultCategory->name : '';
            }

            $this->ajaxDie(json_encode($products));
        } catch (Exception $e) {
            error_log('Error in getProductsByCategory: ' . $e->getMessage());
            $this->ajaxDie(json_encode(['error' => 'An error occurred while fetching products.']));
        }
    }
}
