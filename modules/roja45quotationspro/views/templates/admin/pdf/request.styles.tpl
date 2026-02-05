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
{assign var=color_header value="#F0F0F0"}
{assign var=color_border value="#000000"}
{assign var=color_border_lighter value="#D6D4D4"}
{assign var=color_line_even value="#FFFFFF"}
{assign var=color_line_odd value="#F9F9F9"}
{assign var=font_size_text value="9pt"}
{assign var=font_size_header value="9pt"}
{assign var=font_size_product value="7pt"}
{assign var=height_header value="20px"}
{assign var=table_padding value="4px"}

<style>
    table, th, td {
        margin: 0!important;
        padding: 0!important;
        vertical-align: middle;
        font-size: {$font_size_text|escape:'html':'UTF-8'};
        white-space: nowrap;
    }

    table.product {
        border: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
    }

    table#addresses-tab tr td {
        font-size: large;
    }

    table#summary-tab {
        padding: {$table_padding|escape:'html':'UTF-8'};
        border: 1pt solid {$color_border|escape:'html':'UTF-8'};
    }

    #total-tab {
        border: 0px solid {$color_border|escape:'html':'UTF-8'};
    }
    #total-tab th.totals-title {
        background-color: #f8f8f8;
        padding: {$table_padding|escape:'html':'UTF-8'};
        border-left: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
        border-top: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
    }
    #total-tab td.totals-value {
        text-align: right;
        padding: {$table_padding|escape:'html':'UTF-8'};
        border-left: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
        border-right: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
        border-top: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
    }

    table#note-tab {
        padding: {$table_padding|escape:'html':'UTF-8'};
        border: 1px solid {$color_border|escape:'html':'UTF-8'};
    }
    table#note-tab td.note{
        word-wrap: break-word;
    }
    table#tax-tab {
        padding: {$table_padding|escape:'html':'UTF-8'};
        border: 1pt solid {$color_border|escape:'html':'UTF-8'};
    }
    table#payment-tab,
    table#shipping-tab {
        padding: {$table_padding|escape:'html':'UTF-8'};
        border: 1px solid {$color_border|escape:'html':'UTF-8'};
    }

    tr.discount th.header {
        border-top: 1px solid {$color_border|escape:'html':'UTF-8'};
    }

    tr.color_line_even {
        background-color: {$color_line_even|escape:'html':'UTF-8'};
    }

    tr.color_line_odd {
        background-color: {$color_line_odd|escape:'html':'UTF-8'};
    }

    tr.customization_data td {
    }

    th.header {
        font-size: {$font_size_header|escape:'html':'UTF-8'};
        height: {$height_header|escape:'html':'UTF-8'};
        background-color: {$color_header|escape:'html':'UTF-8'};
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
    }

    th.header-right {
        font-size: {$font_size_header|escape:'html':'UTF-8'};
        height: {$height_header|escape:'html':'UTF-8'};
        background-color: {$color_header|escape:'html':'UTF-8'};
        vertical-align: middle;
        text-align: right;
        font-weight: bold;
    }

    th.product {
        border: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
        vertical-align: middle;
        font-size: {$font_size_product|escape:'html':'UTF-8'};
        padding: {$table_padding|escape:'html':'UTF-8'};
    }

    td.product {
        border: 1px solid {$color_border_lighter|escape:'html':'UTF-8'};
        vertical-align: middle;
        font-size: {$font_size_product|escape:'html':'UTF-8'};
        padding: {$table_padding|escape:'html':'UTF-8'};
    }

    th.payment,
    th.shipping {
        background-color: {$color_header|escape:'html':'UTF-8'};
        vertical-align: middle;
        font-weight: bold;
    }

    th.tva {
        background-color: {$color_header|escape:'html':'UTF-8'};
        vertical-align: middle;
        font-weight: bold;
    }

    tr.separator td {
        border-top: 1px solid #000000;
    }

    .left {
        text-align: left;
    }

    .fright {
        float: right;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    .no_top_border {
        border-top:hidden;
        border-bottom:1px solid black;
        border-left:1px solid black;
        border-right:1px solid black;
    }

    .grey {
        background-color: {$color_header|escape:'html':'UTF-8'};

    }

    /* This is used for the border size */
    .white {
        background-color: #FFFFFF;
    }

    .big,
    tr.big td{
        font-size: 110%;
    }

    .small, table.small th, table.small td {
        font-size:small;
    }
</style>