{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$settings = $cb->getWrapperSettingsFields($id_wrapper)}
{$carousel_settings = $cb->getWrapperSettingsFields($id_wrapper, 'carousel')}
<div class="cb-wrapper w-{$settings.display_type.value|intval}{if !$banners} empty{/if}" data-id="{$id_wrapper|intval}">
    <div class="w-actions">
        <form class="w-settings-form">
            <input type="hidden" name="id_wrapper" value="{$id_wrapper|intval}">
            {foreach $settings as $k => $field}
                {include file="./form-group.tpl"
                    name = "settings[$k]"
                    field = $field
                    group_class = 'inline-block wrapper-form-group'
                    label_class = 'inline-label'
                    input_wrapper_class = 'inline-block'
                    input_class = 'save-on-the-fly'
                }
            {/foreach}
            <button type="button" class="callSettings btn btn-default w-2-element" data-settings="carousel">
                <i class="icon-wrench"></i> {l s='Carousel settings' mod='custombanners'}
            </button>
            <a href="#" class="addBanner pull-right">
                <i class="icon-plus"></i>
                <span class="btn-txt">{l s='New banner' mod='custombanners'}</span>
            </a>
        </form>
        <form class="carousel-settings-form form-horizontal panel w-2-element" style="display:none">
            <input type="hidden" name="id_wrapper" value="{$id_wrapper|intval}">
            <div class="col-lg-6">
                {foreach $carousel_settings as $k => $field}
                    {if $k == 'm'}</div><div class="col-lg-6">{/if}
                    {include file="./form-group.tpl"
                        name = "settings[$k]"
                        field = $field
                        label_class = 'control-label col-lg-6'
                        input_wrapper_class = 'col-lg-6'
                    }
                {/foreach}
            </div>
            <div class="p-footer">
                <button class="btn btn-default saveCarouselSettings"><i class="process-icon-save"></i> {l s='Save' mod='custombanners'}</button>
            </div>
        </form>

        <a href="#" class="deleteWrapper" title="{l s='Delete wrapper' mod='custombanners'}"><i class="icon-trash"></i></a>
        <a href="#" class="dragger w-dragger">
            <i class="icon icon-arrows-v"></i>
        </a>
        <div class="settings-container" style="display:none;"></div>
    </div>
    <div class="cb-list">
        {foreach $banners as $banner}
            {include file="./banner-form.tpl" banner = $banner}
        {/foreach}
    </div>
</div>
{* since 3.0.1 *}
