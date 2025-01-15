<div class="panel">
    <h3>{$module_display_name}</h3>
    <form id="bulk-category-form" method="post" action="{$link->getAdminLink('AdminModules', true)}&configure=bulkcategoryupdate">
        <!-- Categorie-selectie -->
        <div class="form-group">
            <label for="category-select">{$select_category_label}</label>
            <select id="category-select" name="bulk_category_select_category" class="form-control" aria-label="Selecteer een categorie">
                <option value="0">{$none_label}</option>
                {foreach from=$categories item=category}
                    <option value="{$category.id_category}" {if $category.id_category == $selected_category_id}selected{/if}>{$category.name}</option>
                {/foreach}
            </select>
        </div>

        <!-- Producttabel -->
        <div class="form-group">
            <label>{$products_label}</label>
            <table class="table table-striped table-bordered" id="product-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" /></th>
                        <th>{$id_label}</th>
                        <th>{$image_label}</th>
                        <th>{$name_label}</th>
                        <th>{$current_category_label}</th>
                        <th>{$default_category_label}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">{$none_label}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Nieuwe categorie selecteren -->
        <div class="form-group">
            <label for="new-category">{$move_to_category_label}</label>
            <select id="new-category" name="bulk_category_new_category" class="form-control" aria-label="Verplaats naar categorie">
                <option value="0">{$none_label}</option>
                {foreach from=$categories item=category}
                    <option value="{$category.id_category}" {if $category.id_category == $selected_new_category_id}selected{/if}>{$category.name}</option>
                {/foreach}
            </select>
        </div>

        <!-- Hoofdcategorie instellen -->
        <div class="form-group">
            <label for="default-category">{$set_default_category_label}</label>
            <select id="default-category" name="bulk_category_default_category" class="form-control" aria-label="Stel hoofdcategorie in">
                <option value="0">{$none_label}</option>
                {foreach from=$categories item=category}
                    <option value="{$category.id_category}" {if $category.id_category == $selected_default_category_id}selected{/if}>{$category.name}</option>
                {/foreach}
            </select>
        </div>

        <!-- Opslaan -->
        <button id="update-categories-btn" type="submit" name="submitBulkCategoryUpdate" class="btn btn-primary">
            {$update_categories_label}
        </button>
    </form>
</div>
