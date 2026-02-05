{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author          Roja45
*  @copyright       2016 roja45
*}

<body>

<table class="header">
    <tr>
        <td class="logo">
            {if $logo_path}
                <img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
            {/if}
        </td>
        <td class="title">
            <table style="width: 100%">
                <tr>
                    <td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">#{$title|escape:'html':'UTF-8'}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
