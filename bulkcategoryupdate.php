<?php

/**
 * Bulk Category Update Module for PrestaShop
 *
 * Allows bulk updating of product categories via a custom admin interface.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class BulkCategoryUpdate extends Module
{
    public function __construct()
    {
        $this->name = 'bulkcategoryupdate';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'TBSolutions';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Bulk Category Update');
        $this->description = $this->l('Allows bulk updating of product categories.');
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller->controller_name === 'AdminModules' &&
            Tools::getValue('configure') === $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        }

    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitBulkCategoryUpdate')) {
            $selectedProducts = Tools::getValue('bulk_category_selected_products');
            $newCategoryId = (int) Tools::getValue('bulk_category_new_category');
            $newDefaultCategoryId = (int) Tools::getValue('bulk_category_default_category');

            if ($selectedProducts && $newCategoryId) {
                $this->updateProductCategories($selectedProducts, $newCategoryId, $newDefaultCategoryId);
                $output .= $this->displayConfirmation($this->l('Products updated successfully.'));
            } else {
                $output .= $this->displayError($this->l('Please select products and a category.'));
            }
        }

        $output .= $this->renderForm();
        return $output;
    }

    private function updateProductCategories(array $productIds, int $newCategoryId, ?int $newDefaultCategoryId)
    {
        foreach ($productIds as $productId) {
            try {
                $product = new Product($productId);
                if (Validate::isLoadedObject($product)) {
                    $product->updateCategories([$newCategoryId]);
                    if ($newDefaultCategoryId) {
                        $product->id_category_default = $newDefaultCategoryId;
                    }
                    $product->save();
                } else {
                    throw new Exception('Invalid product ID: ' . $productId);
                }
            } catch (Exception $e) {
                error_log('Error updating product: ' . $e->getMessage());
            }
        }
    }    

    private function renderForm()
    {
        $categories = Category::getSimpleCategories($this->context->language->id);

        $this->context->smarty->assign([
            'module_display_name' => $this->displayName,
            'module_description' => $this->description,
            'categories' => $categories,
            'image_label' => $this->l('Afbeelding'),
            'current_category_label' => $this->l('Huidige categorie'),
            'default_category_label' => $this->l('Hoofd categorie'),
            'select_category_label' => $this->l('Selecteer een categorie'),
            'products_label' => $this->l('Producten in de geselecteerde categorie'),
            'id_label' => $this->l('ID'),
            'name_label' => $this->l('Name'),
            'move_to_category_label' => $this->l('Verplaats naar catgegorie'),
            'set_default_category_label' => $this->l('Selecteer hoofdcategorie (Optional)'),
            'none_label' => $this->l('Geen geselecteerd'),
            'update_categories_label' => $this->l('Update categorieen'),
            'link' => $this->context->link,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
