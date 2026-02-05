{if $groups|@count > 0}
  <div class="product-variants js-product-variants">
    <h4 class="configure-title">{l s='Product Variants' d='Shop.Theme.Catalog'}</h4>
    <div class="configure_the_product">
      {foreach from=$groups key=id_attribute_group item=group}
        {if !empty($group.attributes) && $group.attributes|@count > 1}
          <div class="clearfix product-variants-item">
            <span class="control-label">
              {$group.name}{l s=': ' d='Shop.Theme.Catalog'}
            </span>
            {if $group.group_type == 'select'}
              <select class="form-control form-control-select" id="group_{$id_attribute_group}" aria-label="{$group.name}"
                data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]">
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                  <option value="{$id_attribute}" title="{$group_attribute.name}" {if $group_attribute.selected}
                    selected="selected" {/if}>{$group_attribute.name}</option>
                {/foreach}
              </select>
            {elseif $group.group_type == 'color'}
              <ul id="group_{$id_attribute_group}">
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                  <li class="pull-xs-left input-container">
                    <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}"
                      name="group[{$id_attribute_group}]" value="{$id_attribute}" title="{$group_attribute.name}"
                      {if $group_attribute.selected} checked="checked" {/if}>
                    <span {if $group_attribute.texture} class="color texture"
                      style="background-image: url({$group_attribute.texture})" {elseif $group_attribute.html_color_code}
                      class="color" style="background-color: {$group_attribute.html_color_code}" {/if}>
                      <span class="attribute-name sr-only">{$group_attribute.name}</span>
                    </span>
                  </li>
                {/foreach}
              </ul>
            {elseif $group.group_type == 'radio'}
              <ul id="group_{$id_attribute_group}">
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                  <li class="input-container pull-xs-left">
                    <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}"
                      name="group[{$id_attribute_group}]" value="{$id_attribute}" {if $group_attribute.selected} checked="checked"
                      {/if}>
                    <span class="radio-label">{$group_attribute.name}</span>
                  </li>
                {/foreach}
              </ul>
            {/if}
          </div>
        {/if}
      {/foreach}
    </div>
  </div>
{/if}