{*
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
*}
<ul class="steps nbr_steps_4 anchor{if $step == 2} nbr_steps_active_2{/if}{if $step == 1} nbr_steps_active_1{/if}{if $step >=4} nbr_steps_success{/if}">
    <li class="step1 {if $step >=1}selected{/if}{if $step == 1} active{/if}">
        <a class="{if $step >=1}selected{/if}{if $step == 1} active{/if}">
            <span class="stepNumber">1</span>
            <span class="stepDesc">
                {l s='Product filter' mod='ets_productmanager'}
                <br/>
            </span>
            <span class="chevron"></span>
        </a>
    </li>
    <li class="step2 {if $step >=2}selected{/if}{if $step == 2} active{/if}{if $step<2} disabled{/if}">
        <a class="{if $step >=2}selected{/if}{if $step == 2} active{/if}{if $step<2} disabled{/if}" >
            <span class="stepNumber">2</span>
            <span class="stepDesc">
                {l s='Edit action' mod='ets_productmanager'}
                <br/>
            </span>
            <span class="chevron"></span>
        </a>
    </li>
    <li class="step3 {if $step >=3}selected{/if}{if $step == 3} active{/if}{if $step<3} disabled{/if}">
        <a class="{if $step >=3}selected{/if}{if $step == 3} active{/if}{if $step<3} disabled{/if}">
            <span class="stepNumber">3</span>
            <span class="stepDesc">
                {l s='Preview' mod='ets_productmanager'}
                <br/>
            </span>
            <span class="chevron"></span>
        </a>
    </li>
    <li class="step4 {if $step >=4}selected{/if}{if $step == 4} active{/if}{if $step<4} disabled{/if}">
        <a class="{if $step >=4}selected{/if}{if $step == 4} active{/if}{if $step<4} disabled{/if}">
            <span class="stepNumber">4</span>
            <span class="stepDesc">
                {l s='Finish' mod='ets_productmanager'}
                <br/>
            </span>
            <span class="chevron"></span>
        </a>
    </li>
    <li class="list-massedit">
        {$template_massedits nofilter}
    </li>
</ul>
