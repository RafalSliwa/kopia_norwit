{function name="menu" nodes=[] depth=0 parent=null}
    {if $nodes|count}
      <ul class="top-menu {if $depth == 0}container{/if}" {if $depth == 0}id="horizontal-menu"{/if} data-depth="{$depth}">
        {foreach from=$nodes item=node}
            <li class="{$node.type}{if $node.current} current {/if}" id="{$node.page_identifier}">
              <a
                class="{if $depth >= 0}dropdown-item{/if}{if $depth === 1} dropdown-submenu{/if}"
                href="{$node.url}" data-depth="{$depth}"
                {if $node.open_in_new_window} target="_blank" {/if}
              >
                {if $node.children|count}
                  {* Cannot use page identifier as we can have the same page several times *}
                  {assign var=_expand_id value=10|mt_rand:100000}
                  <span class="pull-xs-right hidden-lg-up">
                    <span data-target="#top_sub_menu_{$_expand_id}" data-toggle="collapse" class="navbar-toggler collapse-icons">
                      <i class="fa-icon add">&nbsp;</i>
                      <i class="fa-icon remove">&nbsp;</i>
                    </span>
                  </span>
                {/if}
                {$node.label}
              </a>
              {if $node.children|count}
              <div {if $depth === 0} class="popover sub-menu js-sub-menu collapse"{else} class="collapse"{/if} id="top_sub_menu_{$_expand_id}">
                {menu nodes=$node.children depth=$node.depth parent=$node}
              	{if $node.image_urls}
                  <div class="menu-banners">
                    {foreach from=$node.image_urls item=image_url}
                    <div class="menu-banner">
                      <img src="{$image_url}" alt="" />
                    </div>
                    {/foreach}
                  </div>
                {/if}
                </div>
              {/if}
            </li>
        {/foreach}
      </ul>
    {/if}
{/function}

{*<div id="text-offer">{l s='Free International Delivery' d='Shop.Theme.Global'}</div>*}
<div id="mobile_top_menu_wrapper" class="row hidden-lg-up">
  <div class="mobile-menu-inner">
    <div class="menu-icon">
      <div class="cat-title">{l s='Menu' d='Shop.Theme.Global'}</div>		  
    </div>
    {*<div class="js-top-menu mobile" id="_mobile_top_menu"></div>*}
    <div class="js-top-menu mobile" id="_mobile_main_menu"></div>
  </div>
</div>

<div class="menu horizontal-menu col-lg-12 js-top-menu position-static hidden-md-down" id="_desktop_main_menu">
	{menu nodes=$menu.children}
</div>
<span id="moremenu_text" style="display:none;">{l s='More' d='Shop.Theme.Global'}</span>
<span id="morecategory_text" style="display:none;">{l s='More Categories' d='Shop.Theme.Global'}</span>
<span id="lesscategory_text" style="display:none;">{l s='Less Categories' d='Shop.Theme.Global'}</span>