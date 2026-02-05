{if isset($allegroDescriptionStyle)}
    <style>{$allegroDescriptionStyle}</style>
{/if}

<div class="allegro-description">
    {foreach $allegroDescription->sections as $section}
        <section class="section">
            {foreach $section->items as $item}
                <div class="item {if $section->items|@count > 1}item-6{else}item-12{/if}">
                    <section class="{if $item->type == 'TEXT'}text{else}image{/if}-item">
                        {if $item->type == 'TEXT'}
                            {$item->content}
                        {else}
                            <img src="{$item->url|regex_replace:'/original/':'s512'}" alt="">
                        {/if}
                    </section>
                </div>
            {/foreach}
        </section>
    {/foreach}
</div>
