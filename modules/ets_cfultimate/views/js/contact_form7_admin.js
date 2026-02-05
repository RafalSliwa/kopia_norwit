/**
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
 */

const ETS_CFU_DO_SHOW = 1;
const ETS_CFU_DO_HIDE = 2;
const ETS_CFU_DO_SHOW_MULTIPLE = 3;
const ETS_CFU_DO_HIDE_MULTIPLE = 4;

const ETS_CFU_OPERATOR_CONTAINER = 1;
const ETS_CFU_OPERATOR_DO_NOT_CONTAINER = 2;
const ETS_CFU_OPERATOR_EMPTY = 3;
const ETS_CFU_OPERATOR_FILLED = 4;
const ETS_CFU_OPERATOR_EQUAL = 5;
const ETS_CFU_OPERATOR_NOT_EQUAL = 6;
const ETS_CFU_OPERATOR_LESS_THAN = 7;
const ETS_CFU_OPERATOR_GREATER_THAN = 8;
const ETS_CFU_OPERATOR_BEFORE = 9;
const ETS_CFU_OPERATOR_AFTER = 10;

const ETS_CFU_INPUT_TYPES = ['menu', 'radio', 'checkbox'];
const ETS_CFU_TYPE_IGNORE = ['html', 'quiz', 'acceptance', 'recaptcha', 'captcha', 'submit'];


var ETS_CFU_FIELD_LABEL = ETS_CFU_FIELD_LABEL || 'field'
    , ETS_CFU_FIELDS_VALID = ETS_CFU_FIELDS_VALID || 'Contact form needs to have at least 2 or more input fields'
    ,
    ETS_CFU_MAX_CONDITION_MSG = ETS_CFU_MAX_CONDITION_MSG || 'The maximum number of logical conditions that can be added has been reached'
    , ETS_CFU_IF_REQUIRED = ETS_CFU_IF_REQUIRED || 'If is required'
    , ETS_CFU_FIELDS_REQUIRED = ETS_CFU_FIELDS_REQUIRED || 'Fields are required'
    , ETS_CFU_VALUE_REQUIRED = ETS_CFU_VALUE_REQUIRED || 'Value is required'
    , ETS_CFU_VALUE_INVALID = ETS_CFU_VALUE_INVALID || 'Value is invalid'
    , ETS_CFU_IS_INVALID = ETS_CFU_IS_INVALID || 'is invalid'
    , ETS_CFU_IS_REQUIRED = ETS_CFU_IS_REQUIRED || 'is required'
;

// defined.
var ets_cfu_default = {};
var ets_cfu_multi_lang = false;
var ets_cfu_default_lang = 1;
var ets_cfu_email_is_exist = []
var ets_cfu_email_is_invalid = [];
var ets_cfu_file_attachments = [];
var ets_cfu_short_codes = [];
var ets_cfu_mail_tagged = [];
var ets_cfu_languages = ets_cfu_languages || false;
var ets_cfu_fields = []
    , ets_cfu_fields_select = []
    , ets_cfu_if_select = []
;
var ets_cfu_merge_fields = [];

//end defined.
function copyToClipboard(el) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(el.text()).select();
    document.execCommand("copy");
    $temp.remove();
    el.append('<span class="copied_text">Copied</span>');
    setTimeout(function () {
        el.removeClass('copy');
        $('.copied_text').remove();
    }, 500);
}

function decodeHTMLEntities(text) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = text;
    return textArea.value;
}

function ets_cfu_replace_nodes(nodes, pattern, value) {
    for (let i = 0; i < nodes.length; i++) {
        let curNode = nodes[i];
        if (typeof curNode.attributes !== typeof undefined) {
            let curNodeAttributes = curNode.attributes;
            for (let ii = 0; ii < curNodeAttributes.length; ii++) {
                curNodeAttributes[ii].nodeValue = curNodeAttributes[ii].nodeValue.replace(pattern, value);
            }
        }
        if (curNode.nodeType === 3) {
            if (curNode.data && curNode.data.trim() !== '') {
                curNode.data = curNode.data.replace(pattern, value);
            }
        }
        if (curNode.nodeType === 1) {
            ets_cfu_replace_nodes(curNode.childNodes, pattern, value);
        }
    }
}

function ets_cfu_disable_all_fields(reload) {
    if (reload)
        ets_cfu_reload_select();
    if (ets_cfu_fields_select && ets_cfu_if_select) {
        let items = $('.ets_cfu_condition_item:not([data-id=0])');
        if (items.length > 0) {
            items.each(function () {
                let _this = $(this)
                    , id = _this.attr('data-id')
                ;
                _this.find(`#if_${id} > option:not(:selected)`)
                    .each(function () {
                        let this_ = $(this);
                        this_.removeAttr('disabled');
                        if (ets_cfu_fields_select.indexOf(this_.attr('value')) !== -1) {
                            this_.attr('disabled', 'disabled');
                        }
                    });
                _this.find(`#fields_${id} > option:not(:selected)`)
                    .each(function () {
                        let _this_ = $(this);
                        _this_.removeAttr('disabled');
                        if (ets_cfu_fields_select.indexOf(_this_.attr('value')) !== -1 || ets_cfu_if_select.indexOf(_this_.attr('value')) !== -1) {
                            _this_.attr('disabled', 'disabled');
                        }
                    });
            });
        }
    }
}

function ets_cfu_reload_select() {
    ets_cfu_fields_select = [];
    ets_cfu_if_select = [];
    let items = $('.ets_cfu_condition_item:not([data-id=0])');
    if (items.length > 0) {
        items.each(function () {
            let _this = $(this)
                , id = _this.attr('data-id')
                , _if = _this.find(`#if_${id} > option:selected`).attr('value')
                , _do = parseInt(_this.find(`#do_${id} > option:selected`).attr('value'))
            ;
            if (_if && ets_cfu_if_select.indexOf(_if) === -1) {
                ets_cfu_if_select.push(_if);
            }
            if (_this.find(`#fields_${id} > option:selected`).length > 0) {
                if (_do === ETS_CFU_DO_HIDE_MULTIPLE || _do === ETS_CFU_DO_SHOW_MULTIPLE) {
                    _this.find(`#fields_${id} > option:selected`).each(function () {
                        if (ets_cfu_fields_select.indexOf($(this).attr('value')) === -1) {
                            ets_cfu_fields_select.push($(this).attr('value'));
                        }
                    });
                } else {
                    let field = _this.find(`#fields_${id} > option:selected`).attr('value');
                    if (ets_cfu_fields_select.indexOf(field) === -1) {
                        ets_cfu_fields_select.push(field);
                    }
                }
            }
        });
    }
}

function ets_cfu_init_fields(item) {
    if (!item)
        return;
    let id = item.attr('data-id')
        , fields = item.find(`select[id=fields_${id}] > option:selected`)
    ;
    let items = [];
    if (fields.length > 0) {
        fields.each(function () {
            items.push($(this).attr('value'));
        });
    }
    if (ets_cfu_fields) {
        let $option = `<option value="-1" data-type="--">${ETS_CFU_SELECT_AN_ITEM_LABEL}</option>`;
        ets_cfu_fields.forEach((field) => {
            let selected = '', __if = $(`#if_${id} > option:selected`).val();
            if (field.name !== __if && items.indexOf(field.name) !== -1)
                selected = ' selected';
            else if (field.name === __if || ets_cfu_fields_select.indexOf(field.name) !== -1 || ets_cfu_if_select.indexOf(field.name) !== -1)
                selected = ' disabled';
            $option += `<option value="${field.name}" data-type="${field.type}"${selected}>${field.label}</option>`;
        });
        $(`#fields_${id}`).html($option);
    }
}

function ets_cfu_init_if(item, reload) {
    if (!item)
        return;
    let id = item.attr('data-id')
        , _if = item.find(`select[id=if_${id}] > option:selected`).attr('value')
    ;
    if (ets_cfu_fields) {
        let $option = `<option value="--" data-type="--">${ETS_CFU_SELECT_AN_ITEM_LABEL}</option>`;
        ets_cfu_fields.forEach((field) => {
            let selected = '', disabled = '';
            if (_if === field.name) {
                selected = ' selected';
            }
            if (ets_cfu_fields_select.indexOf(field.name) !== -1) {
                disabled = ' disabled';
            }
            $option += `<option value="${field.name}" data-type="${field.type}"${selected}${disabled}>${field.label}</option>`;
        });
        item.find(`#if_${id}`).html($option);
        ets_cfu_init_operator(item, reload);
        ets_cfu_init_fields(item);
    }
}

function ets_cfu_init_reload() {
    let items = $('.ets_cfu_condition_item:not([data-id=0])');
    if (items.length > 0) {
        items.each(function () {
            ets_cfu_init_operator($(this), true);
        });
    }
}

function ets_cfu_init_condition(reinit) {
    ets_cfu_fields = [];
    let fields_valid = []
    $('.ets_cfu_add_form .ets_cfu_input').each(function () {
        let _this = $(this);
        if (_this.attr('data-name') && _this.attr('data-type') !== 'submit') {
            let type = _this.attr('data-type');
            if (ETS_CFU_TYPE_IGNORE.indexOf(type) === -1) {
                let name = _this.attr('data-name');
                let label = _this.find('.ets_cfu_label_' + id_language).text();
                if (label.trim() === '') {
                    let help_field = _this.find('.ets_cfu_help_block').text();
                    label = `${help_field} ${ETS_CFU_FIELD_LABEL}(${name})`;
                }
                let field_values = [];
                let value = _this.find('.ets_cfu_values_' + id_language).text();
                if (value && ETS_CFU_INPUT_TYPES.indexOf(type) !== -1) {
                    let values = value.split('\n');
                    if (values.length > 0) {
                        values.forEach((item) => {
                            if (item.indexOf(':default') !== -1) item = item.replace(':default', '');
                            let val = item.split('|');
                            field_values.push({
                                label: val[0].trim(),
                                value: (val.length > 1 ? val[1].trim() : val[0].trim())
                            })
                        });
                    }
                }
                if (typeof ETS_CFU_LANGUAGES !== typeof undefined && ETS_CFU_LANGUAGES.length > 0 && ETS_CFU_INPUT_TYPES.indexOf(type) !== -1) {
                    ETS_CFU_LANGUAGES.forEach((id_lang) => {
                        let value = _this.find('.ets_cfu_values_' + id_lang).text();
                        if (value) {
                            let values = value.split('\n');
                            if (values.length > 0) {
                                let old_values = field_values;
                                values.forEach((item, loop) => {
                                    if (item.indexOf(':default') !== -1) item = item.replace(':default', '');
                                    let val = item.split('|');
                                    let new_value = (val.length > 1 ? val[1].trim() : val[0].trim());
                                    if (old_values[loop].value !== new_value) {
                                        field_values[loop].value += `|${new_value}`;
                                    }
                                });
                            }
                        }
                    });
                }
                fields_valid.push(name);
                ets_cfu_fields.push({
                    name: name,
                    type: type,
                    label: label,
                    values: field_values,
                });
            }
        }
    });
    ets_cfu_disable_all_fields(true);
    if (ets_cfu_fields)
        $('#condition_fields_form').val(JSON.stringify(ets_cfu_fields));
    if (reinit) {
        let items = $('.ets_cfu_condition_item:not([data-id=0])');
        if (items.length > 0) {
            items.each(function () {
                let _this = $(this)
                    , id = _this.attr('data-id')
                    , _if = _this.find(`#if_${id} > option:selected`).attr('value')
                    , _do = parseInt(_this.find(`#do_${id} > option:selected`).attr('value'))
                ;
                let keep_field = false;
                if (_this.find(`#fields_${id} > option:selected`).length > 0) {
                    if (_do === ETS_CFU_DO_HIDE_MULTIPLE || _do === ETS_CFU_DO_SHOW_MULTIPLE) {
                        _this.find(`#fields_${id} > option:selected`).each(function () {
                            let field = $(this).attr('value');
                            if (fields_valid.indexOf(field) !== -1) {
                                keep_field = true;
                            }
                        });
                    } else {
                        let field = _this.find(`#fields_${id} > option:selected`).attr('value');
                        if (fields_valid.indexOf(field) !== -1) {
                            keep_field = true;
                        }
                    }
                } else
                    keep_field = true;
                if (fields_valid.indexOf(_if) !== -1 && keep_field) {
                    ets_cfu_init_if($(this), true);
                } else {
                    $(this).remove();
                    if ($('.ets_cfu_condition_item:not([data-id=0])').length < 2) {
                        $('.ets_cfu_form_empty.condition').show();
                        $('.ets_cfu_action.condition').hide();
                    }
                }
            });
        }
    }
}

function ets_cfu_init_operator(item, selected) {
    let id = item.attr('data-id')
        , operator = $(`#operator_${id}`)
        , val = $(`#operator_${id} > option:selected`).attr('value')
        , _if = $(`#if_${id}`);
    operator.find('option').hide();
    switch (_if.find('option:selected').data('type')) {
        case 'text':
        case 'textarea':
        case 'email':
        case 'password':
            operator.find(`option[value=${ETS_CFU_OPERATOR_CONTAINER}], option[value=${ETS_CFU_OPERATOR_DO_NOT_CONTAINER}], option[value=${ETS_CFU_OPERATOR_EMPTY}]`).show();
            if (!selected)
                operator.find(`option[value=${ETS_CFU_OPERATOR_CONTAINER}]`).prop('selected', true);
            break;
        case 'tel':
        case 'file':
        case 'referrence':
            operator.find(`option[value=${ETS_CFU_OPERATOR_EMPTY}], option[value=${ETS_CFU_OPERATOR_FILLED}]`).show();
            if (!selected)
                operator.find(`option[value=${ETS_CFU_OPERATOR_EMPTY}]`).prop('selected', true);
            break;
        case 'number':
            operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}], option[value=${ETS_CFU_OPERATOR_NOT_EQUAL}], option[value=${ETS_CFU_OPERATOR_LESS_THAN}], option[value=${ETS_CFU_OPERATOR_GREATER_THAN}]`).show();
            if (!selected)
                operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}]`).prop('selected', true);
            break;
        case 'date':
            operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}], option[value=${ETS_CFU_OPERATOR_NOT_EQUAL}],option[value=${ETS_CFU_OPERATOR_BEFORE}], option[value=${ETS_CFU_OPERATOR_AFTER}]`).show();
            if (!selected)
                operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}]`).prop('selected', true);
            break;
        case 'menu':
        case 'checkbox':
        case 'radio':
            operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}], option[value=${ETS_CFU_OPERATOR_NOT_EQUAL}]`).show();
            if (!selected)
                operator.find(`option[value=${ETS_CFU_OPERATOR_EQUAL}]`).prop('selected', true);
            break;
    }
    if (!selected && val) {
        //
    }
    ets_cfu_init_value(item, selected);
}

function ets_cfu_get_field_values(field) {
    let value = [];
    if (ets_cfu_fields) {
        ets_cfu_fields.forEach((item) => {
            if (item.name === field) {
                value = item.values;
            }
        });
    }

    return value;
}

function ets_cfu_init_value(item, reload) {
    let id = item.attr('data-id')
        , operator = $(`#operator_${id}`)
        , type = $(`#if_${id} > option:selected`).attr('data-type')
        , field_value = $(`#field_value_${id}`)
    ;
    if (!reload) {
        let field_values = [], _if = $(`#if_${id}`).val();
        if (ETS_CFU_INPUT_TYPES.indexOf(type) !== -1) {
            field_values = ets_cfu_get_field_values(_if);
        }
        switch (type) {
            case 'menu':
                let options = '';
                if (field_values) {
                    field_values.forEach((field) => {
                        if (field.label !== '')
                            options += `<option value="${field.value}">${field.label}</option>`;
                    });
                }
                field_value.html(`<select id="value_${id}" name="value[${id}]">${options}</select>`);
                break;
            case 'radio':
            case 'checkbox':
                let list = '';
                if (field_values) {
                    field_values.forEach((field) => {
                        if (field.label !== '')
                            list += `<label class="ets_cfu_condition_label_radio" for="value_${id}_${field.value}"><input id="value_${id}_${field.value}" type="${type}" name="value[${id}][]" value="${field.value}" />${field.label}</label>`;
                    });
                }
                field_value.html(list);
                break;
            default:
                field_value.html(`<input type="text" id="value_${id}" name="value[${id}]" value=""/>`);
                break;
        }
    }
    let value = $(`#value_${id}`);
    switch (operator.find('option:selected').val()) {
        case `${ETS_CFU_OPERATOR_EMPTY}`:
        case `${ETS_CFU_OPERATOR_FILLED}`:
            field_value.closest('.row').hide();
            break;
        default:
            field_value.closest('.row').show();
            if (type === 'date') {
                value.datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                });
            }
            break;
    }
}

function ets_cfu_init_fields_type(item) {
    let id = item.attr('data-id')
        , fields = $(`#fields_${id}`)
        , _do = $(`#do_${id}`);
    switch (_do.find('option:selected').val()) {
        case `${ETS_CFU_DO_SHOW_MULTIPLE}`:
        case `${ETS_CFU_DO_HIDE_MULTIPLE}`:
            fields.attr('multiple', true);
            break;
        default:
            fields.attr('multiple', false);
            break;
    }
}

function ets_cfu_init_random() {
    return Math.floor((1 + Math.random()) * 0x100000000).toString(16).substring(1);
}

function ets_cfu_invalid() {
    let fields = $('.ets_cfu_condition_item:not([data-id=0])');
    if (fields.length > 0) {
        fields.removeClass('error').find('.error').removeClass('error');
        fields.find('.ets_cfu_condition_error').remove();
        let _errors = [];
        fields.each(function () {
            let _this = $(this)
                , id = _this.attr('data-id')
                , type = _this.find(`#if_${id} > option:selected`).attr('data-type')
                , label = _this.find(`#if_${id} > option:selected`).text()
                , operator = parseInt($(`#operator_${id} > option:selected`).attr('value'))
                , fields = _this.find(`#fields_${id} > option:selected`)
            ;
            if (type === '--') {
                _errors.push({
                    id: id,
                    error: ETS_CFU_IF_REQUIRED,
                    type: type,
                    field_id: `#if_${id}`,
                });
            }
            if (fields.length < 1 || fields.length === 1 && fields.attr('value') === '-1') {
                _errors.push({
                    id: id,
                    error: ETS_CFU_FIELDS_REQUIRED,
                    type: type,
                    field_id: `#fields_${id}`,
                });
            }
            if (operator !== ETS_CFU_OPERATOR_EMPTY && operator !== ETS_CFU_OPERATOR_FILLED) {
                let value = null, _error = null, field_id = null;
                if (ETS_CFU_INPUT_TYPES.indexOf(type) === -1) {
                    value = $(`#value_${id}`).val();
                    if (!value) {
                        _error = ` ${ETS_CFU_VALUE_REQUIRED}`;
                    }
                }
                switch (type) {
                    case 'menu':
                        value = $(`#value_${id} > option:checked`);
                        if (value.length < 1) {
                            _error = `${ETS_CFU_VALUE_REQUIRED}`;
                        }
                        break;
                    case 'radio':
                        field_id = `#field_value_${id}`;
                    case 'checkbox':
                        field_id = `#field_value_${id}`;
                        value = $(`input[name="value[${id}][]"]:checked`);
                        if (value.length < 1) {
                            _error = `${ETS_CFU_VALUE_REQUIRED}`;
                        }
                        break;
                    case 'date':
                        let isValidDate = Date.parse(value);
                        if (isNaN(isValidDate))
                            _error = ` ${ETS_CFU_VALUE_INVALID}`;
                        break;
                    case 'email':
                        if (!/^([a-zA-Z0-9\.\-\@\_]+)$/.test(value)) {
                            _error = `${ETS_CFU_VALUE_INVALID}`;
                        }
                        break;
                    case 'number':
                        if (isNaN(value)) {
                            _error = `${ETS_CFU_VALUE_INVALID}`;
                        }
                        break;
                    case 'url':
                        if (!/^(?:https?):\/\/(\w+:?\w*)?(\S+)(:\d+)?(\/|\/([\w#!:.?+=&%!\-\/]))?$/.test(value)) {
                            _error = `${ETS_CFU_VALUE_INVALID}`;
                        }
                        break;
                    case 'phone':
                        if (!/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im.test(value))
                            _error = `${ETS_CFU_VALUE_INVALID}`;
                        break;
                }
                if (_error !== null) {
                    _errors.push({
                        id: id,
                        error: _error,
                        type: type,
                        field_id: field_id !== null ? field_id : `#value_${id}`,
                    });
                }
            }
        });
        if (_errors.length > 0) {
            _errors.forEach((item) => {
                let rule = $(`.ets_cfu_condition_item[data-id=${item.id}]`);
                rule.addClass('error');
                rule.find(`${item.field_id}`).addClass('error').after(`<p class="ets_cfu_condition_error">${item.error}</p>`);
            });
            return _errors;
        }
    }
    return true;
}

function ets_cfu_get_merge_fields(name) {
    let found = -1;
    if (ets_cfu_merge_fields.length > 0 && name !== '') {
        for (let i = 0; i < ets_cfu_merge_fields.length; i++) {
            if (ets_cfu_merge_fields[i].id === name) {
                found = ets_cfu_merge_fields[i].value;
                break;
            }
        }
    }
    return found;
}

function ets_cfu_init_mailchimp() {
    if (ets_cfu_fields.length < 0) {
        return;
    }
    let data = $('#mailchimp_mapping_data');
    if (data.length > 0 && data.val() !== '' && ets_cfu_merge_fields.length <= 0) {
        let mapping_data = JSON.parse(data.val());
        if (Object.keys(mapping_data).length > 0) {
            Object.keys(mapping_data).forEach((key) => {
                if (typeof mapping_data[key] !== typeof undefined && mapping_data[key] !== '-1') {
                    ets_cfu_merge_fields.push({
                        id: key,
                        value: mapping_data[key].tag,
                    });
                }
            })
        }
    }
    let tr = $('.table-merge-fields tr[data-name="[name]"]'), table = $('.table-merge-fields > tbody');
    ets_cfu_fields.forEach((item) => {
        if ($(`.table-merge-fields tr[data-name="${item.name}"]`).length <= 0) {
            let row = tr.clone();
            ets_cfu_replace_nodes(row, '[name]', `${item.name}`);
            ets_cfu_replace_nodes(row, '[label]', `${item.label}`);
            row.removeAttr('style');
            let choice = row.find('select[name^=mailchimp_merge_field]');
            choice.prop('disabled', false);
            let selectedVal = ets_cfu_get_merge_fields(item.name);
            choice.find(`>option[value="${selectedVal}"]`).prop('selected', true);
            table.append(row);
        }
    });
}
function onoffCaptcha(){
    if($('input#ETS_CFU_ENABLE_RECAPTCHA_on[name="ETS_CFU_ENABLE_RECAPTCHA"]:checked').val()==1)
    {
        $('.form-group.form_group_contact.google.google2,.form-group.form_group_contact.google.google3').removeClass('hide');
        if($('input#id_recaptcha_v2[name="ETS_CFU_RECAPTCHA_TYPE"]:checked').val()==1){
            $('.form-group.form_group_contact.google.google2.capv2').hide();

        }else{
            $('.form-group.form_group_contact.google.google3.capv3').hide();
        }
    }
    else{
        $('.form-group.form_group_contact.google.google2,.form-group.form_group_contact.google.google3').addClass('hide');
    }
}
$(document).ready(function () {
    $(document).on('click', '.form-group.template.template2 p.help-block > span, .form-group .help-block > code', function () {
        copyToClipboard($(this));
    });
    ets_cfu_init();
    ets_cfu_disable_all_fields(true);
    ets_cfu_init_reload();
    /*init*/
    onoffCaptcha();
    $(document).on('click','input[name="ETS_CFU_ENABLE_RECAPTCHA"]',function(){
        onoffCaptcha();
    });

    $(document).on('click', 'button[name=etsCfuSubmitSaveContact], #etsCfuSubmitSaveAndStayContact', function (e) {
        if (ets_cfu_invalid() !== true) {
            e.preventDefault();
            $('.form-group.form_group_contact').hide();
            $('.ets_form_tab_header span').removeClass('active');
            $('.ets_form_tab_header span[data-tab=condition]').addClass('active');
            $('.form-group.form_group_contact.' + $('.ets_form_tab_header .active').attr('data-tab')).show();
        }
    });
    $(document).on('change', '[id^=field_value_] ,[id^=value_],[id^=fields_],[id^=if_]', function (e) {
        e.preventDefault();
        let _this = $(this)
            , item = _this.closest('.ets_cfu_condition_item')
        ;
        if (ets_cfu_invalid() === true) {
            item.removeClass('error');
            item.find('.ets_cfu_condition_error').remove();
            item.find('.error').removeClass('error');
        }
    });
    $(document).on('change', 'select[id^=do]', function (e) {
        e.preventDefault();
        let item = $(this).closest('.ets_cfu_condition_item');
        ets_cfu_init_fields_type(item);
    });
    $(document).on('click', '.ets_cfu_condition_remove', function (e) {
        e.preventDefault();
        let item = $(this).closest('.ets_cfu_condition_item')
            , count = $('.ets_cfu_condition_item:not([data-id=0])').length;
        item.remove();
        if (count < 2) {
            $('.ets_cfu_form_empty.condition').show();
            $('.ets_cfu_action.condition').hide();
        }
        ets_cfu_disable_all_fields(true);
    });
    $(document).on('click', '.ets_cfu_add_condition2', function (e) {
        e.preventDefault();
        ets_cfu_reload_select();
        if (ets_cfu_fields.length < 2) {
            showErrorMessage(ETS_CFU_FIELDS_VALID);
            return;
        }
        if (ets_cfu_fields.length === (ets_cfu_fields_select.length + ets_cfu_if_select.length)) {
            showErrorMessage(ETS_CFU_MAX_CONDITION_MSG);
            return;
        }
        let no_errors = ets_cfu_invalid();
        if (no_errors !== true) {
            return;
        }
        let item = $('.ets_cfu_condition_item[data-id=0]').clone(true, true)
            , count = ets_cfu_init_random()
            , form = $('.ets_cfu_condition_form');
        while ($(`.ets_cfu_condition_item[data-id=${count}]`).length > 0) {
            count = ets_cfu_init_random();
        }
        ets_cfu_replace_nodes(item, '_0', `_${count}`);
        ets_cfu_replace_nodes(item, '[0]', `[${count}]`);
        item.attr('data-id', count);
        item.find('select, input').prop('disabled', false);
        item.show();
        form.append(item);
        ets_cfu_init_if(item, false);
        let _if_ = $(`#if_${count} > option:selected`).attr('value'),
            _fields_ = $(`#fields_${count} > option:selected`).attr('value')
        ;
        if (typeof _if_ !== typeof undefined && _if_ !== '' && ets_cfu_if_select.indexOf(_if_) === -1) {
            ets_cfu_if_select.push(_if_);
        }
        if (typeof _fields_ !== typeof undefined && _fields_ !== '' && ets_cfu_if_select.indexOf(_fields_) === -1) {
            ets_cfu_fields_select.push(_fields_);
        }
        ets_cfu_disable_all_fields(false);
    });
    $(document).on('change', 'select[id^=if]', function () {
        let item = $(this).closest('.ets_cfu_condition_item');
        ets_cfu_init_operator(item, false);
        ets_cfu_init_fields(item);
        ets_cfu_disable_all_fields(true);
    });
    $(document).on('change', 'select[id^=operator]', function () {
        let item = $(this).closest('.ets_cfu_condition_item');
        ets_cfu_init_value(item, true);
        if (ets_cfu_invalid() === true) {
            item.removeClass('error');
            item.find('.ets_cfu_condition_error').remove();
            item.find('.error').removeClass('error');
        }
    });
    $(document).on('click', '.ets_cfu_add_condition.first_item', function (e) {
        e.preventDefault();
        if (ets_cfu_fields.length < 2) {
            showErrorMessage('Contact form needs to have at least 2 or more input fields');
            return;
        }
        let item = $('.ets_cfu_condition_item[data-id=0]').clone(true, true)
            , count = ets_cfu_init_random()
            , form = $('.ets_cfu_condition_form');
        while ($(`.ets_cfu_condition_item[data-id=${count}]`).length > 0) {
            count = ets_cfu_init_random();
        }
        ets_cfu_replace_nodes(item, '_0', `_${count}`);
        ets_cfu_replace_nodes(item, '[0]', `[${count}]`);
        item.attr('data-id', count);
        item.show();
        item.find('select, input').prop('disabled', false);
        form.append(item);
        ets_cfu_init_if(item, false);
        $('.ets_cfu_form_empty.condition').hide();
        $('.ets_cfu_action.condition').show();
        ets_cfu_disable_all_fields(true);
    });
    $(document).on('change', 'select[id^=fields_]', function (e) {
        e.preventDefault();
        ets_cfu_disable_all_fields(true);
    });


    $('.edit_contact_form .form-group.form_group_contact, .integration .form-group.form_group_contact').hide();

    if ($('.ets_form_tab_header').length > 0) {
        $('.form-group.form_group_contact.' + $('.ets_form_tab_header .active').attr('data-tab')).show();
        $('.form-group.form_group_contact:not(.' + $('.ets_form_tab_header .active').attr('data-tab') + ')').hide();
    }

    let tabHeaderActive = $('.ets_form_tab_header .active');
    ets_cfu_init_condition();
    if (tabHeaderActive.attr('data-tab') === 'condition' || tabHeaderActive.attr('data-tab') === 'mailchimp') {
        ets_cfu_init_mailchimp();
    }
    if (tabHeaderActive.attr('data-tab') === 'mail') {
        $('.ets_cfu_mail_menu .ets_cfu_item.admin:not(.active)').addClass('active');
        $('.form-group.form_group_contact.mail2').hide();
    }
    if (tabHeaderActive.attr('data-tab') === 'general_settings') {
        if ($('input[name="open_form_by_button"]:checked').val() === 1)
            $('.form-group.form_group_contact.general_settings2').show();
        else
            $('.form-group.form_group_contact.general_settings2').hide();
        if ($('input[name="save_message"]:checked').val() === '1')
            $('.form-group.form_group_contact.general_settings4').show();
        else
            $('.form-group.form_group_contact.general_settings4').hide();
    }

    if ($('input[name="ETS_CFU_ENABLE_TEMPLATE"]:checked').val() === 1)
        $('.form-group.form_group_contact.template2').show();
    else
        $('.form-group.form_group_contact.template2').hide();
    $('.cfu-content-block').show();

    /*end init*/
    function ets_cfu_mailchimp_enabled(val) {
        if (val === '0') {
            $('.form-group.form_group_contact.mailchimp.mailchimp_api_key').addClass('hide');
            $('.form-group.form_group_contact.mailchimp.mailchimp_audience').addClass('hide');
            $('.form-group.form_group_contact.mailchimp.mailchimp_merge_fields').addClass('hide');
        } else {
            $('.form-group.form_group_contact.mailchimp.mailchimp_api_key').removeClass('hide');
            if ($('#mailchimp_api_key').val() !== '')
                $('.form-group.form_group_contact.mailchimp.mailchimp_audience').removeClass('hide');
            if ($('#mailchimp_audience > option:selected').attr('value') !== '--')
                $('.form-group.form_group_contact.mailchimp.mailchimp_merge_fields').removeClass('hide');
        }
    }

    ets_cfu_mailchimp_enabled($('input[name=mailchimp_enabled]:checked').val());
    $(document).on('change', 'input[name=mailchimp_enabled]', function (e) {
        e.preventDefault();
        ets_cfu_mailchimp_enabled($(this).val());
    });
    $(document).on('change', '#mailchimp_audience', function (e) {
        e.preventDefault();
        let _this = $(this), form = $(this).closest('form');
        if (!_this.hasClass('active') && typeof ETS_CFU_ADMIN_CONTACT_FORM_LINK !== typeof undefined) {
            _this.addClass('active')
            let api_key = form.find('#mailchimp_api_key').val(), list_id = _this.val();
            $.ajax({
                url: ETS_CFU_ADMIN_CONTACT_FORM_LINK,
                data: `ajax=1&api_key=${api_key}&list_id=${list_id}&get_mailchimp_audience=1`,
                dataType: 'json',
                type: 'GET',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        let option = `<option value="-1">${ETS_CFU_DO_NOT_IMPORT_LABEL}</option>`;
                        if (typeof json.merge_fields !== typeof undefined && json.merge_fields && Object.keys(json.merge_fields).length > 0) {
                            Object.keys(json.merge_fields).forEach((key) => {
                                option += `<option value="${key}" data-id="${json.merge_fields[key].id}">${json.merge_fields[key].name}</option>`;
                            });
                            $('select[name="mailchimp_merge_field[[name]]"]').html(option);
                            if (ets_cfu_fields.length > 0)
                                $('.form_group_contact.mailchimp.mailchimp_merge_fields').removeClass('hide');
                            $(`.table-merge-fields tbody > tr:not([data-name="[name]"])`).remove();
                            ets_cfu_init_mailchimp();
                        } else {
                            $('.form_group_contact.mailchimp.mailchimp_merge_fields').addClass('hide');
                            $('select[name="mailchimp_merge_field[[name]]"]').html(option);
                            $(`.table-merge-fields tbody > tr:not([data-name="[name]"])`).remove();
                        }
                    }
                },
                error: function () {
                    _this.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.btn_setup_mailchimp', function (e) {
        e.preventDefault();
        let _this = $(this), form = $(this).closest('form');
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            let api_key = form.find('#mailchimp_api_key').val();
            $.ajax({
                url: _this.attr('href'),
                data: `ajax=1&api_key=${api_key}`,
                dataType: 'json',
                type: 'GET',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        if (json.error) {
                            showErrorMessage(json.message);
                            $('.form_group_contact.mailchimp.mailchimp_audience').addClass('hide');
                        } else {
                            if (json.message)
                                showSuccessMessage(json.message);
                            if (json.mailchimp_audience) {
                                let option = '<option value="--">--</option>';
                                json.mailchimp_audience.forEach((audience) => {
                                    option += `<option value="${audience.id}">${audience.label}</option>`;
                                });
                                $('#mailchimp_audience').html(option);
                                $('.form_group_contact.mailchimp.mailchimp_audience').removeClass('hide');
                            }
                        }
                        $('.form_group_contact.mailchimp.mailchimp_merge_fields').addClass('hide');
                        $('select[name="mailchimp_merge_field[[name]]"]').html(`<option value="-1">${ETS_CFU_DO_NOT_IMPORT_LABEL}</option>`);
                        $(`.table-merge-fields tbody > tr:not([data-name="[name]"])`).remove();
                    }
                },
                error: function () {
                    _this.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.btn_check_api_key', function (e) {
        e.preventDefault();
        let _this = $(this), form = $(this).closest('form');
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            let api_key = form.find('#mailchimp_api_key').val(), id_contact = form.find('#id_contact').val();
            $.ajax({
                url: _this.attr('href'),
                data: `ajax=1&api_key=${api_key}&id_contact=${id_contact}`,
                dataType: 'json',
                type: 'POST',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        if (json.error) {
                            showErrorMessage(json.message);
                        } else {
                            showSuccessMessage(json.message);
                        }
                    }
                },
                error: function () {
                    _this.removeClass('active');
                }
            });
        }
    });
    $(document).on('change', 'select[name^=mailchimp_merge_field]', function (e) {
        e.preventDefault();
        let _this = $(this)
            , name = $(this).attr('name')
            , val = _this.find('>option:selected').attr('value')
            , choiceName = _this.find('>option:selected').text()
            , id = _this.data('id')
        ;
        if ($(`select[name^=mailchimp_merge_field]:not([name="${name}"]) > option[value="${val}"]:not([value="-1"]):selected`).length > 0) {
            showErrorMessage('Mapping field "%s" is exist.'.replace('%s', choiceName));
            _this.find('>option[value=-1]').prop('selected', true);
            return true;
        }
        if (val === '-1') {
            if (ets_cfu_merge_fields.length > 0) {
                for (let i = 0; i < ets_cfu_merge_fields.length; i++) {
                    if (ets_cfu_merge_fields[i].id === id) {
                        ets_cfu_merge_fields.splice(i, 1);
                        break;
                    }
                }
            }
        } else {
            ets_cfu_merge_fields.push({
                id: id,
                value: val,
            });
        }
    });

    function ets_cfu_file_attach(mail) {
        $('.form-group.form_group_contact.mail.attach').hide();
        if ($('.form-group.form_group_contact.' + mail + '.attach').find('input.ets_cfu_file').length > 0) {
            $('.form-group.form_group_contact.' + mail + '.attach').show();
        } else {
            $('.form-group.form_group_contact.' + mail + '.attach').hide();
        }
    }

    function ets_cfu_enabled_email2() {
        if ($('.ets_cfu_item.mail2.active').length > 0) {
            var tabs = $('.ets_cfu_mail_menu li.ets_cfu_item.active');
            $('.form-group.form_group_contact.mail:not(.ets_cfu_form_wrapper,.menu)').hide();
            $('.form-group.form_group_contact.mail.' + tabs.data('tab')).show();
            $('.form-group.form_group_contact.mail2:not(.attach)').show();
            ets_cfu_file_attach('mail2');
        }
    }

    ets_cfu_enabled_email2();
    $(document).on('click', '.ets_cfu_mail_menu li.ets_cfu_item', function () {
        if (!$(this).hasClass('active')) {
            $('input[name="current_tab_email"]').val($(this).attr('data-tab'));

            $('.ets_cfu_mail_menu li.ets_cfu_item.active').removeClass('active');
            $(this).addClass('active');
            if ($(this).is('.mail1')) {
                $('.form-group.form_group_contact.mail1:not(.ets_cfu_form_wrapper):not(.menu,.attach)').show();
                $('.form-group.form_group_contact.mail2').hide();
            } else {
                $('.form-group.form_group_contact.mail1:not(.ets_cfu_form_wrapper):not(.menu)').hide();
                $('.form-group.form_group_contact.mail2').show();
            }
            ets_cfu_file_attach($(this).data('tab'));
        }
    });

    $(document).on('click', '#list-replies li', function (e) {
        if ($('.content-reply-full .content-message').has(e.target).length === 0) {
            if (!$(this).hasClass('opened'))
                $('#list-replies li').removeClass('opened');
            $(this).toggleClass('opened');
        }
    });
    if ($('.ctf7-left-block').length > 1) {
        var i = 1;
        $('.ctf7-left-block').each(function () {
            if (i > 1)
                $(this).addClass('hidden');
            i++;
        });
    }
    $('.message-delete').click(function () {
        var result = confirm(detele_confirm);
        if (result) {
            return true;
        }
        return false;
    });
    $('.message_readed_all').click(function () {
        if (this.checked) {
            $('.message_readed').prop('checked', true);
        } else {
            $('.message_readed').prop('checked', false);
        }
        displayBulkAction();
    });
    $(document).on('click', '.message_readed', function () {
        displayBulkAction();
    });
    $(document).on('change', 'input[type="range"]', function () {
        if ($(this).prev('.rang-value').length > 0)
            $(this).prev('.rang-value').html($(this).val());
    });
    $(document).on('click', '.message_special', function () {

        let _this = $(this);
        let special = _this.attr('data');
        let id_contact_message = _this.val();
        if (!$('body').hasClass('formloading')) {
            $('body').addClass('formloading');
            $.ajax({
                url: '',
                data: 'etsCfuSubmitSpecialActionMessage=1&special=' + special + '&id_contact_message=' + id_contact_message,
                type: 'post',
                dataType: 'json',
                async: true,
                cache: false,
                success: function (json) {
                    $('body').removeClass('formloading');
                    if (json) {
                        if (json.msg)
                            showSuccessMessage(json.msg);
                        let keys = Object.keys(json.messages);
                        keys.forEach(function (k) {
                            $('#tr-message-' + k).html(decodeHTMLEntities(json.messages[k]));
                        });
                    }
                },
                error: function () {
                    $('body').removeClass('formloading');
                }
            });
        }
    });
    $(document).on('change', '#bulk_action_message', function () {
        $('.alert.alert-success').hide();
        if ($('#bulk_action_message').val() == 'delete_selected') {
            var result = confirm(detele_confirm);
            if (!result) {
                $(this).val('');
                return false;
            }

        }
        $('body').addClass('formloading');
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('etsCfuSubmitBulkActionMessage', 1);
        $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (json) {
                $('body').removeClass('formloading');
                if ($('#bulk_action_message').val() == 'delete_selected') {
                    if (json.url_reload)
                        window.location.href = json.url_reload;
                    else
                        location.reload();
                } else {
                    let keys = Object.keys(json.messages);
                    keys.forEach(function (k) {
                        $('#tr-message-' + k).html(decodeHTMLEntities(json.messages[k]));
                        $('#tr-message-' + k + ' .message_readed').prop('checked', true);
                        if ($('#bulk_action_message').val() == 'mark_as_read') {
                            $('#tr-message-' + k).removeClass('no-reaed');
                        } else
                            $('#tr-message-' + k).addClass('no-reaed');
                    });
                    $('.count_messages').html(json.count_messages);
                    if (json.count_messages > 0)
                        $('.count_messages').removeClass('hide');
                    else
                        $('.count_messages').addClass('hide');
                    displayBulkAction();
                    $('#bulk_action_message').val('');
                }
            },
            error: function (xhr, status, error) {
                $('body').removeClass('formloading');
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    });
    if ($('#list-contactform').length) {
        var $myContactform = $("#list-contactform");
        if ($myContactform.length) {
            $myContactform.sortable({
                opacity: 0.6,
                handle: ".dragHandle",
                update: function () {
                    var order = $(this).sortable("serialize") + "&action=etsCfuUpdateContactFormOrdering";
                    $.ajax({
                        type: 'POST',
                        headers: {"cache-control": "no-cache"},
                        url: '',
                        async: true,
                        cache: false,
                        dataType: "json",
                        data: order,
                        success: function (jsonData) {
                            $('#form-contact').append('<div class="ets_successfull_ajax"><span>' + text_update_position + '</span></div>');
                            setTimeout(function () {
                                $('.ets_successfull_ajax').remove();
                            }, 1500);
                            var i = 1;
                            $('.dragGroup span').each(function () {
                                $(this).html(i + (jsonData.page - 1) * 20);
                                i++;
                            });

                        }
                    });
                },
                stop: function (event, ui) {
                }
            });
        }
    }

    if ($('input[name="current_tab"]').val()) {
        $('.ets_form_tab_header span').removeClass('active');
        $('.ets_form_tab_header span[data-tab="' + $('input[name="current_tab"]').val() + '"]').addClass('active');
    }
    $(document).on('change', '.title_form', function () {
        var name = $(this).attr('name'),
            obj = name.split('_'),
            id_lang = obj[obj.length - 1];
        $('#title_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
        if (!ets_cfu_is_updating) {
            $('#title_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
        } else if ($('#title_alias_' + id_lang).val() == '')
            $('#title_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
    });
    $(document).on('change', '.title_tk_page', function () {
        var name = $(this).attr('name'),
            obj = name.split('_'),
            id_lang = obj[obj.length - 1];
        $('#thank_you_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
        if (!ets_cfu_is_updating) {
            $('#thank_you_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
        } else if ($('#thank_you_alias_' + id_lang).val() == '')
            $('#thank_you_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
    });

    $(document).on('click', '.ets_cfu_tab_source li', function () {
        if (!$(this).hasClass('active')) {
            $('.ets_cfu_tab_source li').removeClass('active');
            $(this).addClass('active');
            if ($(this).attr('data-id') == 'etsCfuPreview') {
                if ($('.translatable-field').length > 0)
                    $texteara = $('.translatable-field:not(:hidden) textarea.wpcfu-form');
                else
                    $texteara = $('textarea.ets_cfu_form');
                $('body').addClass('formloading');
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: '',
                    async: true,
                    cache: false,
                    dataType: "json",
                    data: {
                        'etsCfuGetFormElementAjax': 1,
                        'short_code': $texteara.val()
                    },
                    success: function (jsonData) {
                        if ($('.ets_cfu_tab_source li.active').attr('data-id') == 'etsCfuPreview') {
                            $('.ets_cfu_tab.preview').html(jsonData.form_html);
                            $('.ets_cfu_tab').removeClass('active');
                            $('.ets_cfu_tab.preview').addClass('active');
                            if ($('input[type="range"]').length) {
                                $('input[type="range"]').each(function () {
                                    if ($(this).prev('.rang-value').length > 0)
                                        $(this).prev('.rang-value').html($(this).val());
                                });
                            }
                            if ($(".ets_cfu_tab .datepicker").length > 0) {
                                $(".ets_cfu_tab .datepicker").datepicker({
                                    prevText: '',
                                    nextText: '',
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true,
                                });
                            }
                            if ($('.autoload_rte_ctf7').length && typeof tinyMCE !== 'undefined' && tinyMCE.editors.length > 0) {
                                tinySetup({
                                    editor_selector: "autoload_rte_ctf7"
                                });
                            }
                        }
                        $('body').removeClass('formloading');
                    }
                });
            } else {
                $('.ets_cfu_tab').removeClass('active');
                $('.ets_cfu_tab.' + $(this).attr('data-id')).addClass('active');
            }
        }
    });
    $(document).on('click', '.ctf_view_message', function () {
        $('body').addClass('formloading');
        message_readed = $(this).closest('tr').find('.message_readed').attr('data');
        $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            dataType: "json",
            data: 'etsCfuAjax=1&etsCfuMessageReaded=' + message_readed,
            success: function (jsonData) {
                $('body').removeClass('formloading');
                if (jsonData) {
                    if (jsonData.errors) {
                        showErrorMessage(jsonData.errors, 3500);
                        return false;
                    }
                    $('.ctf-popup-wapper-admin #form-message-preview').html(jsonData.message_html);
                    $('.ctf-popup-wapper-admin').addClass('show');
                    if (message_readed == 0) {
                        for (var k in jsonData.messages) {
                            $('#tr-message-' + k).html(jsonData.messages[k]);
                            $('#tr-message-' + k).removeClass('no-reaed');
                        }
                        $('.count_messages').html(jsonData.count_messages);
                        if (jsonData.count_messages > 0)
                            $('.count_messages').removeClass('hide');
                        else
                            $('.count_messages').addClass('hide');
                        displayBulkAction();
                    }
                }
            },
            error: function () {
                $('body').removeClass('formloading');
            }
        });
        return false;
    });
    $(document).on('click', '.ctf-short-code', function () {
        $(this).select();
        document.execCommand("copy");
        $(this).next().addClass('copied');
        setTimeout(function () {
            $('.copied').removeClass('copied');
        }, 2000);
    });
    $(document).on('click', '.ets_cfu_short_code', function () {
        if ($(this).hasClass('shortcode')) {
            var htmlRender = '';
            $('.ets_cfu_block_ul').find('span[class*=ets_cfu_short_code]').each(function () {
                var nameValue = $(this).attr('data-name');
                if (typeof nameValue !== "undefined" && nameValue !== false) {
                    var inputWrap = $('.ets_cfu_input[data-name="' + nameValue + '"]');
                    var dataType = inputWrap.attr('data-type');
                    if (typeof dataType === "undefined" || dataType === false || dataType === "submit" || dataType === "quiz" || dataType === "acceptance" || dataType === "captcha" || dataType === "html") {
                        return;
                    }
                    $.each(ets_cfu_languages, function (index, value) {
                        if (($('.form_group_contact.mail .translatable-field.lang-' + value.id_lang).is(":visible")) || ets_cfu_languages.length == 1) {
                            var elLable = inputWrap.find('.ets_cfu_label_' + value.id_lang);
                            var elValue = inputWrap.find('.ets_cfu_values_' + value.id_lang).first();
                            var label = '';
                            if ((typeof elLable !== "undefined" && elLable !== false)) {
                                label = elLable.html();

                            } else if ((typeof elValue !== "undefined" && elValue !== false)) {
                                label = elValue.html();
                            }
                            htmlRender += '<p>';
                            htmlRender += label ? label + ': ' : '';
                            htmlRender += '[' + nameValue + ']';
                            htmlRender += '</p>';
                        }
                    });
                }

            });

            if (ets_cfu_languages.length == 1) {
                $('.message_body:visible iframe').contents().find('body').empty().html(htmlRender);
            } else {
                $('.message_body:visible .translatable-field').each(function () {
                    if ($(this).is(':visible')) {
                        $(this).find('iframe').contents().find('body').empty().html(htmlRender);
                    }
                });
            }

        } else {
            ets_cfu_copy_to_clipboard($(this));
            if (typeof ets_cfu_copy_msg !== "undefined")
                showSuccessMessage(ets_cfu_copy_msg, 3500);
        }
    });
    $(document).on('click', '.action-reply-message', function () {
        $('.view-message').hide();
        $('#module_form_reply-message').show();
        $('.view-message .success').hide();
        $('textarea[name="message_reply"]').focus();
        $('#replay-message-form input[type=file]').val('');
    });
    $(document).on('click', 'button[name="backReplyMessage"]', function () {
        $('.view-message').show();
        $('#module_form_reply-message').hide();
        $('.view-message .success').hide();
    });
    $(document).on('click', 'button[name="etsCfuSubmitReplyMessage"]', function (evt) {
        evt.preventDefault();
        $('body').addClass('formloadingReply');
        $('.module_error').parent().remove();
        $('.view-message .success').hide();
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('etsCfuSubmitReplyMessage', 1);
        $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (json) {
                $('body').removeClass('formloadingReply');
                if (json.error) {
                    $('#module_form_reply-message .form-wrapper').append(json.error);
                } else {
                    showSuccessMessage(json.success, 3500);
                    $('.view-message').show();
                    $('#module_form_reply-message').hide();
                    $('ul#list-replies').append(json.reply);
                    $('tr#tr-message-' + json.id_message + ' td.replies').html('<i class="action-enabled"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg></i>');
                    $('textarea[name="message_reply"]').val('');
                    $('#replay-message-form input[type=file]').val('');
                }
            },
            error: function (xhr, status, error) {
                $('body').removeClass('formloadingReply');
            }
        });
        return false;
    });
    $(document).on('submit', '#form-contact-preview form', function () {
        return false;
    });
    $(document).on('click', '.ets_cfu-submit', function () {
        return false;
    });
    $(document).on('click', '.ctf_close_popup', function () {
        $(this).closest('.ctf-popup-wapper-admin').removeClass('show');
    });
    $(document).on('click', '.preview-contact', function () {
        $('body').addClass('formloading');
        $(this).closest('.btn-group').removeClass('open');
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: $(this).attr('href'),
            async: true,
            cache: false,
            dataType: "json",
            data: '',
            success: function (jsonData) {
                $('.ctf-popup-wapper-admin').addClass('show');
                $('.ctf-popup-wapper-admin #form-contact-preview').html(jsonData.form_html);
                $('body').removeClass('formloading');
            }
        });
        return false;
    });
    if ($('.ets_form_tab_header .active').length > 0 && $('.ets_form_tab_header .active').attr('data-tab') == 'form') {
        $('.form-group.form_group_contact.form.short_code').hide();
    }

    if ($('.ets_form_tab_header .active').length > 0 && $('.ets_form_tab_header .active').attr('data-tab') == 'thank_you') {
        ets_cfu_form_changed(true);
    }
    $(document).on('click', '.ets_form_tab_header span', function () {
        if (!$(this).hasClass('active')) {
            $('.form-group.form_group_contact').hide();
            $('.ets_form_tab_header span').removeClass('active');
            $(this).addClass('active');
            if ($(this).attr('data-tab') == 'export_import') {
                $('button[name="etsCfuBtnSubmit"]').hide();
            } else
                $('button[name="etsCfuBtnSubmit"]').show();
            $('.form-group.form_group_contact.' + $('.ets_form_tab_header .active').attr('data-tab')).show();
            if ($('.ets_form_tab_header .active').attr('data-tab') == 'form') {
                $('.form-group.form_group_contact.form.short_code').hide();
            }
            if ($('.ets_form_tab_header .active').attr('data-tab') === 'condition') {
                $('.form-group.form_group_contact.form.condition').hide();
            }
            $('input[name="current_tab"]').val($(this).attr('data-tab'));
            if ($('.ets_form_tab_header .active').attr('data-tab') == 'mail') {
                var tabs = $('.ets_cfu_mail_menu li.ets_cfu_item.active');
                $('.form-group.form_group_contact.mail:not(.ets_cfu_form_wrapper,.menu)').hide();
                $('.form-group.form_group_contact.mail.' + tabs.data('tab')).show();
                ets_cfu_enabled_email2();
            }
            if ($('.ets_form_tab_header .active').attr('data-tab') == 'general_settings') {
                if ($('input[name="open_form_by_button"]:checked').val() == 1)
                    $('.form-group.form_group_contact.general_settings2').show();
                else
                    $('.form-group.form_group_contact.general_settings2').hide();
                if ($('input[name="save_message"]:checked').val() == 1)
                    $('.form-group.form_group_contact.general_settings4').show();
                else
                    $('.form-group.form_group_contact.general_settings4').hide();

                ets_cfu_button_enabled();
                ets_cfu_button_floating_enabled();
            } else
                $('.form-group.form_group_contact.general_settings').removeClass('hide show');
            if ($('.ets_form_tab_header .active').attr('data-tab') == 'template') {
                if ($('input[name="ETS_CFU_ENABLE_TEMPLATE"]:checked').val() == 1)
                    $('.form-group.form_group_contact.template2').show();
                else
                    $('.form-group.form_group_contact.template2').hide();
            }
            if ($(this).data('tab') == 'mail') {
                ets_cfu_to_short_codes();
                ets_cfu_form_changed(true);
            }
            if ($(this).data('tab') === 'condition' || $(this).data('tab') === 'mailchimp') {
                ets_cfu_init_condition();
            }
            if ($(this).data('tab') === 'mailchimp') {
                ets_cfu_init_mailchimp();
            }
            if ($(this).data('tab') == 'google') {
                ETS_CTF_JS.select_v2_v3();
            }
            if ($(this).attr('data-tab') == 'thank_you') {
                handle_switch_thank_page();
                ets_cfu_form_changed(true);
            }
        }
    });

    $(document).on('click', '.ets_cfu_delete_image', function () {
        var wrapper = $(this).parents('.ets_cfu_image_wrapper').eq(0);
        $('input[name="' + wrapper.data('id') + '"]').val(1);
        wrapper.remove();
        ets_cfu_form_changed(true);
    });

    function ets_cfu_button_enabled() {
        var open_form_by_button = $('input[name=open_form_by_button]:checked').val();
        if (open_form_by_button == '1') {
            $('.form-group.form_group_contact.general_settings.open_form').addClass('show').removeClass('hide');
            ets_cfu_button_icon_enabled();
        } else
            $('.form-group.form_group_contact.general_settings.open_form').addClass('hide').removeClass('show');
    }

    function ets_cfu_button_floating_enabled() {
        var button_popup_enabled = $('input[name=button_popup_enabled]:checked').val();
        if (button_popup_enabled == '1') {
            $('.form-group.form_group_contact.general_settings.floating').addClass('show').removeClass('hide');
            ets_cfu_button_position();
            ets_cfu_floating_icon_enabled();
        } else
            $('.form-group.form_group_contact.general_settings.floating').addClass('hide').removeClass('show');
    }

    function ets_cfu_button_position() {
        var button_popup_position = $('select[name="button_popup_position"]')
        switch (button_popup_position.val()) {
            case 'middle_right':
                $('.form-group.button_popup_left, .form-group.button_popup_bottom').addClass('hide').removeClass('show');
                $('.form-group.button_popup_right, .form-group.button_popup_top').addClass('show').removeClass('hide');
                break;
            case 'bottom_right':
                $('.form-group.button_popup_left, .form-group.button_popup_top').addClass('hide').removeClass('show');
                $('.form-group.button_popup_right, .form-group.button_popup_bottom').addClass('show').removeClass('hide');
                break;
            case 'middle_left':
                $('.form-group.button_popup_right, .form-group.button_popup_bottom').addClass('hide').removeClass('show');
                $('.form-group.button_popup_left, .form-group.button_popup_top').addClass('show').removeClass('hide');
                break;
            case 'bottom_left':
                $('.form-group.button_popup_right, .form-group.button_popup_top').addClass('hide').removeClass('show');
                $('.form-group.button_popup_left, .form-group.button_popup_bottom').addClass('show').removeClass('hide');
                break;
        }
    }

    function ets_cfu_button_icon_enabled() {
        var button_icon_enabled = $('input[name=button_icon_enabled]:checked').val();
        if (button_icon_enabled == '1') {
            $('.form-group.form_group_contact.general_settings.open_form.button_icon_custom, .form-group.form_group_contact.general_settings.open_form.button_icon_custom_file').addClass('show').removeClass('hide');
        } else {
            $('.form-group.form_group_contact.general_settings.open_form.button_icon_custom, .form-group.form_group_contact.general_settings.open_form.button_icon_custom_file').addClass('hide').removeClass('show');
        }
    }

    function ets_cfu_floating_icon_enabled() {
        var floating_icon_enabled = $('input[name=floating_icon_enabled]:checked').val();
        if (floating_icon_enabled == '1') {
            $('.form-group.form_group_contact.general_settings.floating.floating_icon_custom, .form-group.form_group_contact.general_settings.floating.floating_icon_custom_file').addClass('show').removeClass('hide');
        } else {
            $('.form-group.form_group_contact.general_settings.floating.floating_icon_custom, .form-group.form_group_contact.general_settings.floating.floating_icon_custom_file').addClass('hide').removeClass('show');
        }
    }

    if ($('.ets_form_tab_header span[data-tab=general_settings].active').length > 0) {
        ets_cfu_button_enabled();
        ets_cfu_button_floating_enabled();
    }

    $(document).on('change', 'input[name="open_form_by_button"]', function () {
        ets_cfu_button_enabled();
    });
    $(document).on('change', 'input[name="button_popup_enabled"]', function () {
        ets_cfu_button_floating_enabled();
    });
    $(document).on('change', 'select[name="button_popup_position"]', function () {
        ets_cfu_button_position();
    });
    $(document).on('change', 'input[name="button_icon_enabled"]', function () {
        ets_cfu_button_icon_enabled();
    });
    $(document).on('change', 'input[name="floating_icon_enabled"]', function () {
        ets_cfu_floating_icon_enabled();
    });

    $(document).on('click', 'button.ets_cfu_reset_color', function () {
        $('input[name=button_text_color]').val('#ffffff').css({
            'background-color': 'rgb(255, 255, 255)',
            'color': 'black'
        });
        $('input[name=button_background_color]').val('#2fb5d2').css({'background-color': '#2fb5d2', 'color': 'black'});
        $('input[name=button_hover_color]').val('#ffffff').css({
            'background-color': 'rgb(255, 255, 255)',
            'color': 'black'
        });
        $('input[name=button_background_hover_color]').val('#2592a9').css({
            'background-color': '#2592a9',
            'color': 'white'
        });
    });
    $(document).on('click', 'button.ets_cfu_floating_reset_color', function () {
        $('input[name=floating_text_color]').val('#ffffff').css({
            'background-color': 'rgb(255, 255, 255)',
            'color': 'black'
        });
        $('input[name=floating_background_color]').val('#2fb5d2').css({
            'background-color': '#2fb5d2',
            'color': 'black'
        });
        $('input[name=floating_hover_color]').val('#ffffff').css({
            'background-color': 'rgb(255, 255, 255)',
            'color': 'black'
        });
        $('input[name=floating_background_hover_color]').val('#2592a9').css({
            'background-color': '#2592a9',
            'color': 'white'
        });
    });

    $(document).on('focus', '#button_icon_custom', function () {
        $('.button_icon_custom_icons_ul').addClass('active');
    });
    $(document).on('mouseover', '.button_icon_custom_icons_ul', function () {
        $('.button_icon_custom_icons_ul').addClass('in');
    });
    $(document).on('mouseout', '.button_icon_custom_icons_ul', function () {
        $('.button_icon_custom_icons_ul').removeClass('in');
    });
    $(document).on('focusout', '#button_icon_custom', function () {
        $('.button_icon_custom_icons_ul:not(.in).active').removeClass('active');
    });
    $(document).on('click', '.button_icon_custom_icons_ul .ets_cfu_icon_li', function () {
        $('#button_icon_custom').val($(this).data('id'));
        if ($('.button_icon_custom_selected').length <= 0) {
            $('#button_icon_custom').before('<span class="button_icon_custom_selected">' + $(this).html() + '</span>');
        } else
            $('.button_icon_custom_selected').html($(this).html());
        $('.button_icon_custom_icons_ul').removeClass('in active');
        ets_cfu_form_changed(true);
    });

    // Floating button:
    $(document).on('focus', '#floating_icon_custom', function () {
        $('.floating_icon_custom_icons_ul').addClass('active');
    });
    $(document).on('mouseover', '.floating_icon_custom_icons_ul', function () {
        $('.floating_icon_custom_icons_ul').addClass('in');
    });
    $(document).on('mouseout', '.floating_icon_custom_icons_ul', function () {
        $('.floating_icon_custom_icons_ul').removeClass('in');
    });
    $(document).on('focusout', '#floating_icon_custom', function () {
        $('.floating_icon_custom_icons_ul:not(.in).active').removeClass('active');
    });
    $(document).on('click', '.floating_icon_custom_icons_ul .ets_cfu_icon_li', function () {
        $('#floating_icon_custom').val($(this).data('id'));
        if ($('.floating_icon_custom_selected').length <= 0) {
            $('#floating_icon_custom').before('<span class="floating_icon_custom_selected">' + $(this).html() + '</span>');
        } else
            $('.floating_icon_custom_selected').html($(this).html());
        $('.floating_icon_custom_icons_ul').removeClass('in active');
        ets_cfu_form_changed(true);
    });
    // End floating button:

    $(document).on('click', 'input[name="ETS_CFU_ENABLE_TEMPLATE"]', function () {
        if ($('input[name="ETS_CFU_ENABLE_TEMPLATE"]:checked').val() == 1)
            $('.form-group.template2').show();
        else
            $('.form-group.template2').hide();
    });
    $(document).on('click', 'input[name="save_message"]', function () {
        if ($('input[name="save_message"]:checked').val() == 1)
            $('.form-group.form_group_contact.general_settings4').show();
        else
            $('.form-group.form_group_contact.general_settings4').hide();
    });

    $(document).on('keyup change', '.edit_contact_form :input', function () {
        ets_cfu_form_changed(true);
    });
    $(document).on('click', '.tag-generator-list .thickbox:not(.disabled)', function (evt) {
        evt.preventDefault();
        ets_cfu_popup_generator($(this));
        if ($('#TB_ajaxContent form').length > 0) {
            ets_cfu_update($('#TB_ajaxContent form'));
            ets_cfu_close_popup();
            ets_cfu_init_condition(true);
        }
        return false;
    });
    $(document).on('click', '.ets_cfu_add_form .ets_cfu_btn_edit_input', function () {
        var button = $(this),
            input_field = $(this).parents('.ets_cfu_input');
        if ($('.ets_cfu_panel_inputs .ets_cfu_input_' + input_field.data('type')).length > 0 && !input_field.hasClass('editing')) {
            input_field.addClass('editing');
            $('.ets_cfu_panel_inputs .ets_cfu_input_' + input_field.data('type') + ' a.thickbox:not(.disabled)').click();
        }
    });
    $(document).on('click', '.ets_cfu_add_form .ets_cfu_btn_copy_input', function () {
        var button = $(this),
            form_field = button.parents('.ets_cfu_input'),
            input_type = form_field.data('type'),
            input_name = form_field.data('name');
        var duplicate = form_field.clone();
        if (duplicate.length > 0) {
            var rename = input_type + '-' + Math.floor(Math.random() * 1000);
            duplicate.attr('data-name', rename);
            if (duplicate.is('[data-mailtag]') && duplicate.attr('data-mailtag')) {
                duplicate.attr('data-mailtag', 0);
            }
            if (ets_cfu_multi_lang) {
                duplicate.find('span[class*=ets_cfu_short_code]').each(function () {
                    if ($(this).html()) {
                        $(this).html($(this).html().replace(input_name, rename));
                    }
                });
            } else {
                duplicate.find('.ets_cfu_short_code_' + ets_cfu_default_lang).html(duplicate.find('.ets_cfu_short_code_' + ets_cfu_default_lang).text().replace(input_name, rename));
            }
        }
        form_field.after(duplicate);
        ets_cfu_form_editor();
        ets_cfu_render_form();
    });
    $(document).on('click', '.ets_cfu_add_form .ets_cfu_btn_delete_input', function () {
        var button = $(this),
            column = button.parents('.ets_cfu_col_box');
        button.parents('.ets_cfu_input').remove();
        ets_cfu_form_editor();
        ets_cfu_render_form();
        ets_cfu_autoload_sc();
    });
    //update form_input.
    $(document).on('click change', 'form.tag-generator-panel .control-box :input', function () {
        var input_form = $(this).parents('form.tag-generator-panel');
        if (input_form.length > 0) {
            ets_cfu_build_input($(this), input_form);
            ets_cfu_normalize($(this));
            ets_cfu_update(input_form);
            ets_cfu_init_condition(true);
        }
    });
    //end update form input.
    $(document).on('click', '#TB_closeWindowButton', function (e) {
        ets_cfu_close_form_input();
    });
    //insert tag from add.
    $(document).on('click', 'input.insert-tag', function (evt) {
        evt.preventDefault();
        var input_form = $(this).parents('form.tag-generator-panel');
        if (input_form.length > 0) {
            ets_cfu_mail_tags(input_form, false);
            ets_cfu_add_input_rows(input_form);
            ets_cfu_col_sortable();
            ets_cfu_close_form_input();
        }
    });
    $(document).mouseup(function (e) {
        var container = $("#TB_content");
        var colorpanel = $('#mColorPicker');
        if (!container.is(e.target)
            && container.has(e.target).length === 0 && !colorpanel.is(e.target) && colorpanel.has(e.target).length === 0
            && ($('#mColorPicker').length <= 0 || ($('#mColorPicker').length > 0 && $('#mColorPicker').css('display') == 'none'))
        ) {
            ets_cfu_close_form_input();
        }
        /*var container_popup_content = $('.ctf-popup-content');
        if (container_popup_content.has(e.target).length === 0) {
            $('.ctf-popup-wapper-admin').removeClass('show');
        }*/
        //new
        if (!$('.ets_cfu_form_load').is(e.target) && $('.ets_cfu_form_load').has(e.target).length === 0) {
            ets_cfu_close_popup();
        }

    });
    $(document).keyup(function (e) {
        if (e.keyCode == 27) {
            $('.ctf-popup-wapper-admin').removeClass('show');
            ets_cfu_close_form_input();
            ets_cfu_close_popup();
            $('.button_icon_custom_icons_ul.active').removeClass('active');
        }
    });
    $(document).on('click', '.ets_cfu_close_popup', function () {
        ets_cfu_close_popup();
    });
    $(document).on('click', '.ets_cfu_btn_copy', function () {
        var button = $(this);
        ets_cfu_clone_rows(button);
    });
    $(document).on('click', '.ets_cfu_add_row', function (evt) {
        evt.preventDefault();
        if ($('.ets_cfu_form_popup').length > 0 && $('.ets_cfu_form_popup.active').length <= 0) {
            if ($('.ets_cfu_form_load').length > 0 && $('.ets_cfu_form_load .ets_cfu_rows').length <= 0) {
                $('.ets_cfu_form_load').html($('.ets_cfu_row_group').html());
            }
            $('.ets_cfu_form_load .ets_cfu_title').html(ets_cfu_add_row_title);
            $('.ets_cfu_form_popup').addClass('active');
        }
    });
    $(document).on('click', '.ets_cfu_form_popup.active .ets_cfu_box', function () {
        var element = $(this).clone();
        element.find('.ets_cfu_title_box').remove();
        ets_cfu_add_rows($('.ets_cfu_add_form'), element);
        ets_cfu_close_popup();
    });
    $(document).on('click', '.ets_cfu_add_form .ets_cfu_btn_edit', function () {
        var button = $(this);
        ets_cfu_edit_rows(button);
    });
    $(document).on('click', '.ets_cfu_add_form .ets_cfu_btn_delete', function () {
        var button = $(this);
        ets_cfu_delete_rows(button);
    });
    $(document).on('click', '.ets_cfu_add_input', function (evt) {
        evt.preventDefault();
        var button = $(this);
        if (!button.hasClass('adding') && $('.ets_cfu_form_popup').length > 0 && $('.ets_cfu_form_popup.active').length <= 0) {
            button.addClass('adding');
            if ($('.ets_cfu_form_load').length > 0 && $('.ets_cfu_form_load .ets_cfu_panel_inputs').length <= 0) {
                $('.ets_cfu_form_load').html($('.ets_cfu_input_group').html());
            }
            $('.ets_cfu_form_popup').addClass('active');
        }
    });

    $(document).on('change', '.ets_cfu_ul .ets_cfu_li :input', function () {
        ets_cfu_email_is_exist = ets_cfu_email_is_invalid = [];
        var ul_element = $(this).parents('.ets_cfu_ul'),
            parent_ul = ul_element.data('ul'),
            element = $(this);

        if ($('#' + parent_ul).length > 0 && ul_element.length > 0) {
            $('#' + parent_ul).val(ets_cfu_email_generator(ul_element, element));
            if (ul_element.is('.mail-tag')) {
                if ($(this).val()) {
                    ets_cfu_mail_tagged[parent_ul + $(this).data('type')] = $(this).val();
                }
            }
        }
    });
    $(document).on('click', '.ets_cfu_ul .ets_cfu_add', function () {
        var element = $(this).parents('.ets_cfu_li').clone(),
            ul_element = $(this).parents('.ets_cfu_ul'),
            li_element = $(this).parents('.ets_cfu_li'),
            key = ul_element.data('ul');

        if (!element.find('.ets_cfu_email').val()) {
            li_element.find('.ets_cfu_email').focus();
            showErrorMessage(ets_cfu_msg_email_required, 3500);
            return false;
        } else if ((typeof ets_cfu_email_is_exist[key] !== "undefined" && ets_cfu_email_is_exist[key]) || (typeof ets_cfu_email_is_invalid[key] !== "undefined" && ets_cfu_email_is_invalid[key])) {
            li_element.find('.ets_cfu_email').focus();
            return false;
        }
        if (element.length > 0 && ul_element.length > 0) {
            element.find(':input').val('');
            li_element.find('.button .btn')
                .removeClass('ets_cfu_add')
                .addClass('ets_cfu_del')
                .attr('title', (typeof ets_cfu_label_delete !== "undefined" ? ets_cfu_label_delete : 'Delete'))
                .find('i')
                .removeClass('icon-plus-circle')
                .addClass('icon-trash-o');
            ul_element.append(element);
        }
    });
    $(document).on('click', '.ets_cfu_ul .ets_cfu_del', function () {
        if (typeof ets_cfu_delete_msg !== "undefined" && confirm(ets_cfu_delete_msg)) {
            var ul_element = $(this).parents('.ets_cfu_ul'),
                li_element = $(this).parents('.ets_cfu_li'),
                input_generator = $('#' + ul_element.data('ul'));

            if (li_element.length > 0 && input_generator.length > 0) {
                li_element.remove();
                input_generator.val(ets_cfu_email_generator(ul_element, false));
            }
        }
    });
    $(document).on('change', '.ets_cfu_ul_files :input.ets_cfu_file', function () {

        var ul_files = $(this).parents('.ets_cfu_ul_files'),
            input_attachment = $(this).parents('.ets_cfu_ul_files').next();

        if (input_attachment.length > 0) {
            var file_attachments = [];
            ul_files.find(':input.ets_cfu_file').each(function () {
                if ($(this).is(':checked'))
                    file_attachments.push($(this).val());
            });
            input_attachment.val(file_attachments.join(','));
        }
    });
    $(document).on('click', '#TB_backWindowButton', function () {
        if ($('.ets_cfu_add_form_contact').length > 0 && $('.ets_cfu_add_form_contact .ets_cfu_add_input.adding').length > 0) {
            var btnBack = $('.ets_cfu_add_form_contact .ets_cfu_add_input.adding');
            $('#TB_closeWindowButton').click();
            btnBack.trigger('click');
        }
    });
});

function ets_cfu_copy_to_clipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

function ets_cfu_email_generator(ul_element, $this) {
    var emails = [],
        count = 0,
        key = ul_element.data('ul');
    ul_element.find('.ets_cfu_li').each(function () {
        var element = $(this).find('.ets_cfu_email');
        if (element.length > 0 && element.val()) {
            if ($this.length > 0) {
                if ($this.hasClass('ets_cfu_email') && $this.val().trim() == element.val().trim())
                    count++;
                if (count > 1) {
                    ets_cfu_email_is_exist[key] = true;
                    showErrorMessage(ets_cfu_msg_email_exist, 3500);
                    $this.focus();
                    return false;
                }
                if (!ets_cfu_is_email(element.val())) {
                    ets_cfu_email_is_invalid[key] = true;
                    showErrorMessage(ets_cfu_msg_email_invalid.replace(/%s/g, element.val()), 3500);
                    element.focus();
                    return false;
                }
            }
            var element = $(this).find('.ets_cfu_name').val() + '<' + element.val() + '>';
            emails.push(element);
        }
    });
    return emails.join(',');
}

function ets_cfu_is_email(email) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?|\[(.*?)\]$/i;
    return pattern.test(email);
}

function ets_cfu_init() {
    if ($('.ets_cfu_add_contact').length > 0) {
        ets_cfu_multi_lang = $('.ets_cfu_add_contact').data('multi-lang') ? 1 : 0;
        ets_cfu_default_lang = $('.ets_cfu_add_contact').data('default-lang');
    }

    if ($('.form_group_contact.mail').length > 0) {
        $('.form_group_contact.mail').wrapAll('<div class="form-group form_group_contact mail ets_cfu_form_wrapper"></div>');
        if ($('.ets_cfu_form_wrapper').length > 0) {
            $('.ets_cfu_form_wrapper').prepend('<div class="ets_cfu_block">');
            $('.ets_cfu_form_wrapper .mail').wrapAll('<div class="ets_cfu_form_email"></div>');
            if ($('.ets_cfu_form_email .ets_cfu_block_short_code').length > 0) {
                var element = $('.ets_cfu_form_email .ets_cfu_block_short_code').clone();
                element.removeClass('form_group_contact mail hide');
                $('.ets_cfu_form_wrapper .ets_cfu_block').append(element);
            }
        }
        ets_cfu_to_short_codes();
        ets_cfu_mail_is_tagged();
        ets_cfu_autoload_sc();
    }

    if ($('.ets_cfu_add_form').length > 0) {
        ets_cfu_row_sortable();
        ets_cfu_col_sortable();
    }

    if ($('.bootstrap .alert').length > 0) {
        setTimeout(function () {
            $('.bootstrap .alert-success, .bootstrap .alert-error').hide();
        }, 3500);
    }
    ets_cfu_form_changed(false);
    var loaded = setInterval(function () {
        if ($('.ets_form_tab_header span.active').length > 0 && $('.ets_form_tab_header span.active').data('tab') == 'mail') {
            ets_cfu_form_changed(true);
            clearInterval(loaded);
        }
    }, 350);
    ets_rebuild_forms();
}

function ets_rebuild_forms() {
    if ($('.ets_cfu_input_generator .ets_cfu_form_data').length > 0) {
        var ik = 0;
        $('.ets_cfu_input_generator .ets_cfu_form_data').each(function () {
            var element = $(this);
            if (ets_cfu_languages && ets_cfu_multi_lang) {
                $.each(ets_cfu_languages, function (i, item) {
                    if (element.find('span[class^=ets_cfu_][class$=_' + item.id_lang + ']').length <= 0 && element.find('span[class^=ets_cfu_][class$=_' + ets_cfu_default_lang + ']').length > 0) {
                        element.find('span[class^=ets_cfu_][class$=_' + ets_cfu_default_lang + ']').each(function () {
                            var copy_element = $(this).clone(true);
                            copy_element.attr('class', copy_element.attr('class').replace(/^(ets_cfu_)(.*?)_([1-9])$/, '$1$2_' + item.id_lang));
                            copy_element.hide();
                            element.append(copy_element);
                            ik++;
                        });
                    }
                });
            }
        });
        if (ik > 0) {
            ets_cfu_form_editor();
            ets_cfu_form_changed(true);
        }
    }
}

function ets_cfu_form_changed(form_edited) {
    $('button[name=etsCfuSubmitSaveContact], #etsCfuSubmitSaveAndStayContact').prop('disabled', !form_edited);
}

function ets_cfu_file_attachment() {
    $('input.file_attachment').each(function () {
        var input_files = $(this);
        if (ets_cfu_file_attachments.length <= 0) {
            input_files.prev().remove();
            input_files.val('').parents('.form_group_contact').eq(0).hide();
        } else if (input_files.length > 0) {
            input_files.prev('.ets_cfu_ul_files').remove();
            input_files.before('<ul class="ets_cfu_ul_files">');
            var ul_file = input_files.prev('.ets_cfu_ul_files'),
                file_values = input_files.val().split(','),
                file_attachments = [];
            if (ul_file.length > 0) {
                var li_file = '';
                $.each(ets_cfu_file_attachments, function (i, short_code) {
                    li_file += '<li class="ets_cfu_li"><label>';
                    li_file += '<input type="checkbox" ' + (file_values.indexOf(short_code) !== -1 ? 'checked="checked"' : '') + ' name="' + input_files.attr('name') + '[]" class="ets_cfu_file" value="' + short_code + '">';
                    li_file += '<span class="ets_cfu_label ctf-short-code">' + short_code + '</span>';
                    li_file += '</label></li>';
                    file_attachments.push(short_code);
                });
                ul_file.html(li_file);
                input_files.val(file_attachments.join(','));
            }
        }
    });
}

function ets_cfu_to_short_codes() {
    if ($('.ets_cfu_add_form_contact').length > 0) {
        var ul_short_code = $('.ets_cfu_block .ets_cfu_block_ul');
        if (ul_short_code.length > 0) {
            var element = '', title = ul_short_code.data('title');
            ets_cfu_file_attachments = [];
            $('.ets_cfu_add_form .ets_cfu_input').each(function () {
                if ($(this).attr('data-name')) {
                    var short_code = '[' + $(this).attr('data-name') + ']';
                    element += '<li class="ets_cfu_block_li"><span class="ets_cfu_block_item ets_cfu_short_code" data-name="' + $(this).attr('data-name') + '" title="' + title + '">' + short_code + '</span></li>';
                    if ($(this).data('type') == 'file' && ets_cfu_file_attachments.indexOf(short_code) < 0) {
                        ets_cfu_file_attachments.push(short_code);
                    }
                }
            });
            element += '<li class="ets_cfu_block_li short_code_all"><span class="ets_cfu_block_item ets_cfu_short_code shortcode" title="' + 'Click to general' + '">' + '[all-fields]' + '</span></li>';
            ul_short_code.html(element);
        }
        ets_cfu_file_attachment();
    }
}

function ets_cfu_mail_is_tagged() {
    ets_cfu_mail_tagged = [];
    if ($('form #id_contact').length > 0 && parseInt($('form #id_contact').val()) > 0) {
        var mail_tags = $('ul.ets_cfu_ul.mail-tag');
        if (mail_tags.length > 0) {
            mail_tags.each(function () {
                var form_inputs = $(this).find('li.ets_cfu_li:first input'),
                    parent_ul = $(this).data('ul');
                if (form_inputs.length > 0) {
                    form_inputs.each(function () {
                        if ($(this).val()) {
                            ets_cfu_mail_tagged[parent_ul + $(this).data('type')] = $(this).val();
                        }
                    });
                }
            });
        }
    }
}

//insert form tag.
function ets_cfu_mail_tags(input_form) {
    if (input_form.length > 0) {
        var key = input_form.data('id'),
            mail_tag = input_form.find(':input.mail-tag'),
            form_inputs = $('.ets_cfu_add_form .ets_cfu_input[data-type=' + key + ']');
        if (mail_tag.length > 0 && mail_tag.is(':checked')) {
            ets_cfu_short_codes[key] = mail_tag.val();
            if (form_inputs.length > 0) {
                form_inputs.each(function () {
                    $(this).attr('data-mailtag', 0);
                });
            }
        }
    }

    cts_cfu_update_mail_tags();
}

function ets_cfu_autoload_sc() {
    if ($('.ets_cfu_add_form').length > 0) {
        ets_cfu_short_codes = [];
        $('.ets_cfu_add_form .ets_cfu_input[data-mailtag=1]').each(function () {
            ets_cfu_short_codes[$(this).data('type')] = '[' + $(this).data('name') + ']';
        });
    }
    cts_cfu_update_mail_tags();
}

function cts_cfu_update_mail_tags() {
    if (typeof ets_cfu_short_codes !== "undefined") {
        var mail_tags = $('ul.ets_cfu_ul.mail-tag');
        if (mail_tags.length > 0) {
            mail_tags.each(function () {
                var form_inputs = $(this).find('li.ets_cfu_li:first input'),
                    parent_ul = $(this).data('ul');
                if (form_inputs.length > 0) {
                    form_inputs.each(function () {
                        var key = $(this).data('type');
                        if (typeof ets_cfu_short_codes[key] !== "undefined" && ets_cfu_short_codes[key] && typeof ets_cfu_mail_tagged[parent_ul + key] === "undefined") {
                            $(this).val(ets_cfu_short_codes[key]);
                        } else if (typeof ets_cfu_mail_tagged[parent_ul + key] === "undefined") {
                            $(this).val('');
                        }
                    });
                    $('#' + parent_ul).val(ets_cfu_email_generator($(this), false));
                }
            });
        }
    }
}


function ets_cfu_build_input(input, input_form) {
    var input_generator = input_form.find('.ets_cfu_input');
    if (input_form.length <= 0 || input_generator.length <= 0)
        return;
    var form_data = input_generator.find('.ets_cfu_form_data');
    if (input_form.find('.ets_cfu_input').length > 0 && input.length && !input.is(':button')) {
        var input_val = '';
        if (input.is(':checkbox')) {
            input_val = input.is(':checked') ? 1 : 0;
        } else {
            input_val = input.val();
        }
        //new.
        if (!input.is('.is-multi-lang')) {
            input_form.find('.ets_cfu_input').attr('data-' + input.attr('name'), input_val);
        } else {
            //multilang.
            var id_language = input.parents('.translatable-field').length > 0 ? input.parents('.translatable-field').data('lang') : 0;
            var key = input.data('unique');
            if (ets_cfu_multi_lang && parseInt(id_language) > 0) {
                if (parseInt(id_language) == parseInt(ets_cfu_default_lang)) {
                    input_form.find(':input[name^=' + key + '_]').each(function () {
                        var idLang = $(this).parents('.translatable-field').data('lang');
                        if ((!$(this).val() || $(this).val() == ets_cfu_default[key]) && idLang != id_language) {
                            $(this).val(input_val);
                            if (form_data.find('.ets_cfu_' + key + '_' + idLang).length <= 0) {
                                form_data.append('<span class="ets_cfu_' + key + '_' + idLang + '" style="display:none;">' + input_val + '</span>');
                            } else
                                form_data.find('.ets_cfu_' + key + '_' + idLang).html(input_val);
                        }
                    });
                    ets_cfu_default[key] = input_val;
                }
                if (form_data.find('.ets_cfu_' + key + '_' + id_language).length <= 0) {
                    form_data.append('<span class="ets_cfu_' + key + '_' + id_language + '" style="display:none;">' + input_val + '</span>');
                } else
                    form_data.find('.ets_cfu_' + key + '_' + id_language).html(input_val);
            } else {
                if (form_data.find('.ets_cfu_' + key + '_' + ets_cfu_default_lang).length <= 0) {
                    form_data.append('<span class="ets_cfu_' + key + '_' + ets_cfu_default_lang + '" style="display:none;">' + input_val + '</span>');
                } else
                    form_data.find('.ets_cfu_' + key + '_' + ets_cfu_default_lang).html(input_val);
            }
        }
    }
}

function ets_cfu_close_form_input() {
    if ($('#TB_window').length > 0) {
        $('#TB_window').remove();
        $('.ets_cfu_add_input.adding').removeClass('adding');
        $('.ets_cfu_add_form .ets_cfu_input.editing').removeClass('editing');
        $('.ets_cfu_add_form .ets_cfu_input.locked').removeClass('locked');
    }
}

function ets_cfu_popup_generator(input_field) {
    if ($('body #TB_window').length <= 0) {
        var $html_content = '<div id="TB_window" class="thickbox-loading">';
        $html_content += '<div id="TB_table"><div id="TB_table_cell"><div id="TB_content">';
        $html_content += '<div id="TB_title">';
        if ($('.ets_cfu_add_form_contact .ets_cfu_input.editing').length <= 0) {
            $html_content += '<div id="TB_backAjaxWindow"><button id="TB_backWindowButton" class="ets_cfu_back_popup" type="button" title="' + ets_cfu_btn_back_label + '"><span class="screen-reader-text">' + ets_cfu_btn_back_label + '</span><span class="tb-back-icon"></span></button></div>';
        }
        $html_content += '<div id="TB_ajaxWindowTitle">' + ($('.ets_cfu_input.editing').length > 0 ? ets_cfu_edit_input_field : ets_cfu_add_input_field) + ' ' + input_field.html() + '</div>';
        $html_content += '<div id="TB_closeAjaxWindow"><button id="TB_closeWindowButton" type="button" title="' + ets_cfu_btn_close_label + '"><span class="screen-reader-text">' + ets_cfu_btn_close_label + '</span><span class="tb-close-icon"></span></button></div>';
        $html_content += '</div>';
        $html_content += '<div id="TB_ajaxContent">';
        $html_content += $(input_field.attr('href')).html();
        $html_content += '</div>';
        $html_content += '</div></div></div>';
        $html_content += '</div>';
        $('body').append($html_content);
    }
}

function ets_cfu_add_input_rows(input_form) {
    var short_code = input_form.find('input.tag').val(),
        add_form = $('.ets_cfu_add_form'),
        action_form = $('.ets_cfu_action');
    if (input_form.find('.ets_cfu_input').length > 0) {
        if (add_form.find('.ets_cfu_add_input.adding').length > 0 && !add_form.find('.ets_cfu_add_input.adding').is('.first_item')) {
            add_form.find('.ets_cfu_add_input.adding')
                .parents('.ets_cfu_col')
                .find('.ets_cfu_col_box')
                .append(input_form.find('.ets_cfu_input'));
        } else if (add_form.find('.ets_cfu_input.editing').length > 0 && !add_form.find('.ets_cfu_input.adding').is('.first_item')) {
            add_form.find('.ets_cfu_input.editing')
                .before(input_form.find('.ets_cfu_input'))
                .remove();
        } else if ($('.ets_cfu_add_input.first_item.adding').length > 0 || (action_form.find('.ets_cfu_add_input.adding').length > 0 && $('.ets_cfu_form_group .ets_cfu_box').length > 0)) {
            var box_form;
            $('.ets_cfu_form_group .ets_cfu_box').each(function () {
                if ($(this).find('.ets_cfu_col').length <= 1) {
                    box_form = $(this).clone();
                    return false;
                }
            });
            if (box_form.length > 0) {
                box_form.find('.ets_cfu_col_box').append(input_form.find('.ets_cfu_input'));
                ets_cfu_add_rows(add_form, box_form);
            }
        }
    }
    ets_cfu_col_sortable();
    ets_cfu_form_editor();
    ets_cfu_render_form();
}

function ets_cfu_render_form() {
    if ($('.ets_cfu_add_form_contact').length > 0 && $('.ets_cfu_add_form').length > 0 && $('#render_form').length > 0) {
        $('#render_form').val($('.ets_cfu_add_form').html());
        ets_cfu_form_changed(true);
        ets_cfu_init_condition(true);
    }
}

function ets_cfu_form_editor() {
    if ($('.ets_cfu_add_form_contact').length > 0 && $('.ets_cfu_add_form').length > 0) {
        if (ets_cfu_multi_lang) {
            $('textarea[name^=short_code_]').each(function () {
                var id_language = $(this).clone().attr('name').replace('short_code_', '').trim();
                $(this).val(ets_cfu_generator_form(id_language));
            })
        } else {
            $('textarea[name=short_code_' + ets_cfu_default_lang + ']').val(ets_cfu_generator_form(ets_cfu_default_lang));
        }
        ets_cfu_form_changed(true);
    }
}

function ets_cfu_generator_form(id_language) {
    if ($('.ets_cfu_add_form .ets_cfu_input').length <= 0) {
        return;
    }
    var row_index = 0,
        form_editor = $('.ets_cfu_add_form').clone().html('');
    form_editor.append('<div class="ets_cfu_wrapper">');
    var form_wrapper = form_editor.find('.ets_cfu_wrapper');
    $('.ets_cfu_add_form .ets_cfu_box').each(function () {
        var element = $(this);
        form_wrapper.append('<div class="ets_cfu_box ' + element.data('type') + ' ets_cfu_box_render_' + row_index + '">');
        if (element.find('.ets_cfu_col').length > 0) {
            var col_index = 0,
                element_edit = form_wrapper.find('.ets_cfu_box_render_' + row_index);
            element.find('.ets_cfu_col').each(function () {
                var column = $(this);
                element_edit.append('<div class="ets_cfu_col ' + column.data('col') + ' ets_cfu_col_render_' + col_index + '">');
                if (column.find('.ets_cfu_input').length > 0) {
                    var input_index = 0,
                        column_edit = element_edit.find('.ets_cfu_col_render_' + col_index);
                    column.find('.ets_cfu_input').each(function () {
                        var input_generate = $(this),
                            idLanguage = (id_language ? '_' + id_language : '');
                        column_edit.append('<div class="' + input_generate.attr('class') + ' ets_cfu_input_render_' + input_index + '">');
                        var html_building = '',
                            is_btn_submit = $(this).data('type') != 'submit' ? false : true;

                        //ets_cfu_default_lang
                        if (input_generate.find('.ets_cfu_short_code' + idLanguage).length > 0) {
                            html_building += ((is_btn_submit ? '' : '<label>') + input_generate.find('.ets_cfu_short_code' + idLanguage).html() + (is_btn_submit ? '' : '</label>'));
                        }
                        if (input_generate.find('.ets_cfu_desc' + idLanguage).length > 0 && input_generate.find('.ets_cfu_desc' + idLanguage).html()) {
                            html_building += '<p class="ets_cfu_help_block">' + input_generate.find('.ets_cfu_desc' + idLanguage).html() + '</p>';
                        }


                        column_edit.find('.ets_cfu_input_render_' + input_index).html(html_building);
                        input_index++;
                    });
                    //input.
                    ets_cfu_remove_mark(column_edit, 'input');
                }
                col_index++;
            });
            //col.
            ets_cfu_remove_mark(element_edit, 'col');
        }
        row_index++;
    });
    //row.
    ets_cfu_remove_mark(form_wrapper, 'box');
    return form_editor.html();
}

function ets_cfu_remove_mark(element, input) {
    if (element.find('.ets_cfu_' + input).length > 0) {
        var ik = 0;
        element.find('.ets_cfu_' + input).each(function () {
            $(this).removeClass('ets_cfu_' + input + '_render_' + ik);
            ik++;
        });
    }
}

function ets_cfu_normalize($input) {
    var val = $input.val();
    if ($input.is('input[name="name"]')) {
        val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
    }

    if ($input.is('.numeric')) {
        val = val.replace(/[^0-9.-]/g, '');
    }

    if ($input.is('.idvalue')) {
        val = val.replace(/[^-0-9a-zA-Z_]/g, '');
    }

    if ($input.is('.classvalue')) {
        val = $.map(val.split(' '), function (n) {
            return n.replace(/[^-0-9a-zA-Z_]/g, '');
        }).join(' ');

        val = $.trim(val.replace(/\s+/g, ' '));
    }

    if ($input.is('.color')) {
        val = val.replace(/[^0-9a-fA-F]/g, '');
    }

    if ($input.is('.filesize')) {
        val = val.replace(/[^0-9kKmMbB]/g, '');
    }

    if ($input.is('.filetype')) {
        val = val.replace(/[^0-9a-zA-Z.,|\s]/g, '');
    }

    if ($input.is('.date')) {
        // 'yyyy-mm-dd' ISO 8601 format
        if (!val.match(/^\d{4}-\d{2}-\d{2}$/)) {
            val = '';
        }
    }

    if ($input.is(':input[name="values"]')) {
        val = $.trim(val);
    }

    $input.val(val);

    if ($input.is(':checkbox.exclusive')) {
        ctf_exclusiveCheckbox($input);
    }
}

function ctf_exclusiveCheckbox($cb) {
    if ($cb.is(':checked')) {
        $cb.siblings(':checkbox.exclusive').prop('checked', false);
    }
}

function ets_cfu_update(input_form) {
    var id = input_form.attr('data-id'),
        name = '',
        name_fields = input_form.find('input[name="name"]'),
        field_edit = $('.ets_cfu_add_form .ets_cfu_input.editing');

    //is add new or edit.
    if (name_fields.length && field_edit.length <= 0) {
        name = name_fields.val();
        if (name === '') {
            name = id + '-' + Math.floor(Math.random() * 1000);
            name_fields.val(name);
        }
        if (name_fields.val()) {
            input_form.find('.ets_cfu_input').attr('data-name', name);
        }
    } else if (field_edit.length > 0 && !field_edit.hasClass('locked')) {
        //edit input.
        input_form.find(':input').each(function () {
            if ($(this).is(':checkbox')) {
                $(this).prop('checked', (parseInt(field_edit.attr('data-' + $(this).attr('name'))) > 0));
            } else if ($(this).is(':radio')) {
                if ($(this).val() === field_edit.attr('data-' + $(this).attr('name'))) {
                    $(this).prop('checked', true);
                }
            } else if ($(this).attr('name') === 'mod_reference') {
                $(this).attr('value', '1');
            } else if ($(this).attr('type') !== 'button' && !$(this).hasClass('tag')) {
                if ($(this).is('.is-multi-lang')) {
                    var id_language = $(this).parents('.translatable-field').length > 0 ? $(this).parents('.translatable-field').data('lang') : 0;
                    var key = $(this).data('unique');
                    var node = document.createElement('textarea');
                    node.innerHTML = field_edit.find('.ets_cfu_' + key + '_' + (id_language ? id_language : ets_cfu_default_lang)).html();
                    $(this).val(node.value);
                } else {
                    $(this).val(field_edit.attr('data-' + $(this).attr('name')));
                }
            }
        });
        input_form.find('.ets_cfu_input').after(field_edit.clone()).remove();
        name = name_fields.val();
        field_edit.addClass('locked');
        $('#TB_content').addClass('are_edit');
    }
    if (!name && name_fields.length > 0) {
        name = name_fields.val();
    }

    //building tag.
    input_form.find('input.tag').each(function () {
        var tag_type = $(this).data('type');
        if (input_form.find(':input[name="tagtype"]').length > 0) {
            tag_type = input_form.find(':input[name="tagtype"]').val();
        }
        /*[input*?]*/
        if (input_form.find(':input[name="required"]').is(':checked')) {
            tag_type += '*';
        }
        var idLang = false;
        if (ets_cfu_multi_lang && $(this).data('lang')) {
            idLang = $(this).data('lang');
        } else {
            idLang = ets_cfu_default_lang;
        }
        var components = ets_cfu_compose(tag_type, input_form, idLang);
        $(this).val(components);
        input_form.find('.ets_cfu_short_code' + (idLang ? '_' + idLang : '')).html(components);
    });

    //mail-tag.
    var tag_name = '[' + name + ']';
    input_form.find('span.mail-tag').text(tag_name);
    input_form.find('input.mail-tag').each(function () {
        $(this).val(tag_name);
    });
}

function ets_cfu_compose(tagType, input_form, idLang) {
    var name = input_form.find('input[name="name"]').val();
    var scope = input_form.find('.scope.' + tagType);
    if (!scope.length) {
        scope = input_form;
    }
    var options = [];

    //input option.
    scope.find('input.option').not(':checkbox,:radio').each(function (i) {
        var val = $(this).val();
        if (!val) {
            return;
        }
        if ($(this).hasClass('filetype')) {
            val = val.split(/[,|\s]+/).join('|');
        }
        if ($(this).hasClass('color')) {
            val = '#' + val;
        }
        if ('class' === $(this).attr('name')) {
            $.each(val.split(' '), function (i, n) {
                options.push('class:' + n);
            });
        } else {
            options.push($(this).attr('name') + ':' + val);
        }
    });

    //each checkbox default.
    scope.find('input:checkbox.default').not('.option').each(function () {
        if ($(this).is(':checked')) {
            options.push($(this).attr('name') + ':' + $(this).val());
        }
    });

    //each select default.
    scope.find('select.default').each(function () {
        if ($(this).val()) {
            options.push($(this).attr('name') + ':' + $(this).val());
        }
    });

    //each checkbox option.
    scope.find('input:checkbox.option').each(function () {
        if ($(this).is(':checked')) {
            options.push($(this).attr('name') + ($(this).is('.default') ? ':on' : ''));
        }
    });

    //each radio has class option.
    scope.find('input:radio.option').each(function () {
        if ($(this).is(':checked') && !$(this).hasClass('default')) {
            options.push($(this).attr('name') + ':' + $(this).val());
        }
    });
    if ('radio' === tagType) {
        options.push('default:1');
    }
    options = (options.length > 0) ? options.join(' ') : '';

    var value = '';
    //values multilang.
    if (scope.find(':input[name="values' + (idLang ? '_' + idLang : '') + '"]').val()) {
        $.each(scope.find(':input[name="values' + (idLang ? '_' + idLang : '') + '"]').val().split("\n"), function (i, n) {
            value += ' "' + n.replace(/["]/g, '&quot;') + '"';
        });
    }

    var components = [];
    $.each([tagType, name, options, value], function (i, v) {
        v = $.trim(v);
        if ('' !== v) {
            components.push(v);
        }
    });
    components = $.trim(components.join(' '));
    components = '[' + components + ']';

    //label input multilang.
    var scope_label = scope.find(':input[name="label' + (idLang ? '_' + idLang : '') + '"]').length > 0 ? scope.find(':input[name="label' + (idLang ? '_' + idLang : '') + '"]').val() : '';
    scope_label = $.trim(scope_label);
    if (scope_label) {
        components = '<span class="ets_cfu_span">' + scope_label + (input_form.find(':input[name="required"]').is(':checked') ? '*' : '') + '</span>' + components;
    }

    //content multilang.
    var content = scope.find(':input[name="content' + (idLang ? '_' + idLang : '') + '"]').val();
    content = $.trim(content);
    if (content) {
        components += ' ' + content + ' [/' + tagType + ']';
    }

    return components;
}

function displayBulkAction() {
    if ($('.message_readed:checked').length) {
        $('#bulk_action_message').show();
    } else {
        $('#bulk_action_message').hide();
    }
    if ($('.message_readed:checked').length == $('.message_readed[data="1"]:checked').length)
        $('#bulk_action_message option[value="mark_as_read"]').hide();
    else
        $('#bulk_action_message option[value="mark_as_read"]').show();
    if ($('.message_readed:checked').length == $('.message_readed[data="0"]:checked').length)
        $('#bulk_action_message option[value="mark_as_unread"]').hide();
    else
        $('#bulk_action_message option[value="mark_as_unread"]').show();
}

function ets_cfu_close_popup() {
    $('.ets_cfu_form_popup.active').removeClass('active');
    $('.ets_cfu_form_popup .ets_cfu_box.editing, .ets_cfu_add_form .ets_cfu_box.editing').removeClass('editing');
    if ($('#TB_window').length <= 0) {
        $('.ets_cfu_add_input.adding').removeClass('adding');
        $('.ets_cfu_input.editing').removeClass('editing');
    }
}

function ets_cfu_add_rows(add_form, item) {
    var addItem = add_form.find('.ets_cfu_box.editing');
    if (addItem.length > 0) {
        addItem.find('.ets_cfu_col').each(function () {
            if ($(this).html()) {
                item.find('.ets_cfu_col.' + $(this).data('col')).html($(this).html());
            }
        });
        addItem.before(item).remove();
    } else {
        if ($('.ets_cfu_add_form .ets_cfu_box').length <= 0) {
            $('.ets_cfu_form_empty.contact-form').hide();
        }
        add_form.append(item);
    }
    ets_cfu_row_sortable();
    ets_cfu_col_sortable();
    ets_cfu_form_editor();
    ets_cfu_render_form();
}

function ets_cfu_edit_rows(button) {
    var editItem = button.parents('.ets_cfu_box');
    if (editItem.length > 0 && !editItem.hasClass('editing')) {
        editItem.addClass('editing');
        if ($('.ets_cfu_form_load .ets_cfu_row_group').length <= 0) {
            $('.ets_cfu_form_load').html($('.ets_cfu_row_group').html());
        }
        $('.ets_cfu_form_popup .ets_cfu_box[data-type=' + editItem.data('type') + ']').addClass('editing');
        $('.ets_cfu_form_load .ets_cfu_title').html(ets_cfu_edit_row_title);
        $('.ets_cfu_form_popup').addClass('active');
    }
}

function ets_cfu_delete_rows(button) {
    var deleteItem = button.parents('.ets_cfu_box');
    if (deleteItem.length > 0) {
        deleteItem.remove();
        if ($('.ets_cfu_add_form .ets_cfu_box').length <= 0) {
            $('.ets_cfu_form_empty.contact-form').show();
        }
        ets_cfu_row_sortable();
        ets_cfu_form_editor();
        ets_cfu_render_form();
        ets_cfu_autoload_sc();
    }
}

function ets_cfu_clone_rows(button) {
    var element = button.parents('.ets_cfu_box');
    //change id.
    if (element.length > 0) {
        var duplicate = element.clone();
        if (duplicate.find('.ets_cfu_input').length > 0) {
            duplicate.find('.ets_cfu_input').each(function () {
                var $this = $(this),
                    new_name = $this.attr('data-type') + '-' + Math.floor(Math.random() * 1000),
                    old_name = $this.attr('data-name');
                $this.attr('data-name', new_name);
                if ($this.is('[data-mailtag]') && $this.attr('data-mailtag')) {
                    $this.attr('data-mailtag', 0);
                }
                if (ets_cfu_multi_lang) {
                    $this.find('span[class*=ets_cfu_short_code]').each(function () {
                        if ($(this).html()) {
                            $(this).html($(this).html().replace(old_name, new_name));
                        }
                    });
                } else {
                    $this.find('.ets_cfu_short_code_' + ets_cfu_default_lang).html($this.find('.ets_cfu_short_code_' + ets_cfu_default_lang).text().replace(old_name, new_name));
                }
            });
        }
        element.after(duplicate);
        ets_cfu_col_sortable();
        ets_cfu_form_editor();
        ets_cfu_render_form();
    }
}

function ets_cfu_col_sortable() {
    $('.ets_cfu_col_box').sortable({
        connectWith: ".ets_cfu_col_box",
        items: '.ets_cfu_input',
        cursorAt: {left: 30, top: 30},
        update: function () {
            ets_cfu_form_editor();
            ets_cfu_render_form();
        },
        start: function (event, ui) {
            ui.item.width(360);
        }
    }).disableSelection();
}


function ets_cfu_row_sortable() {
    $('.ets_cfu_add_form').sortable({
        connectWith: ".ets_cfu_add_form",
        items: '.ets_cfu_box',
        handle: ".ets_cfu_btn_drag_drop",
        update: function () {
            ets_cfu_form_editor();
            ets_cfu_render_form();
        },
    }).disableSelection();
}

function handle_switch_thank_page() {
    if ($('.thank_you_active').is(':hidden')) {
        return;
    }
    if ($.trim($('#thank_you_page').children("option:selected").val()) === 'thank_page_default') {
        $('.form_group_contact.thank_you_message').show();
        $('.form_group_contact.thank_you_url').hide();
    } else {
        $('.form_group_contact.thank_you_message').hide();
        $('.form_group_contact.thank_you_url').show();
    }
}

$(document).ready(function () {
    if ($('#thank_you_page').length > 0) {
        handle_switch_thank_page();
    }
    $(document).on('change', $('#thank_you_page'), function () {
        handle_switch_thank_page();
    });
});


var ETS_CTF_JS = {
    init: function () {
        this.change_select_v2_v3();
        this.check_tick_all();
        this.tick_check_group();
        this.cacheLifeTime();
    },
    select_v2_v3: function () {
        if (!$('#id_recaptcha_v2').is(':visible')) return;
        if ($('#id_recaptcha_v2').is(':checked')) {
            $('.form_group_contact.google.google2.capv2').show();
            $('.form_group_contact.google.google3.capv3').hide();
        } else {
            $('.form_group_contact.google.google2.capv2').hide();
            $('.form_group_contact.google.google3.capv3').show();
        }
    },
    check_tick_all: function () {
        var input_group = $('input[name="group_access[]"]');
        if (input_group.length > 0) {
            var check = true;
            input_group.each(function (index, value) {
                if (!$(this).is(":checked")) {
                    check = false;
                }
            });

            if (check) {
                $('input[name="checkme"]').prop('checked', true);
            } else {
                $('input[name="checkme"]').prop('checked', false);
            }
        }
    },
    tick_check_group: function () {
        var input_group = $('input[name="group_access[]"]');
        if (input_group.length > 0) {
            $(document).on('change', input_group, function () {
                ETS_CTF_JS.check_tick_all();
            });
        }
    },
    change_select_v2_v3: function () {
        $(document).on('change', 'input[name="ETS_CFU_RECAPTCHA_TYPE"]', function () {
            ETS_CTF_JS.select_v2_v3();
        });
    },
    cacheLifeTime: function () {
        if ($('input[name=ETS_CFU_CACHE_ENABLED]:checked').val() == '1') {
            $('#ETS_CFU_CACHE_LIFETIME').closest('.form_group_contact').show();
        } else {
            $('#ETS_CFU_CACHE_LIFETIME').closest('.form_group_contact').hide();
        }
    }
};
$(document).ready(function () {
    ETS_CTF_JS.init();
    $(document).on('change', '[name=paginator_message_select_limit]', function (e) {
        e.preventDefault();
        let form = $(this).closest('form'), href = form.attr('action'), per_page = $(this).val();
        if (href.indexOf('paginator_message_select_limit') !== -1) {
            href = href.replace('/paginator_message_select_limit/', `paginator_message_select_limit=${per_page}`);
        } else
            href += `&paginator_message_select_limit=${per_page}`;
        window.location.href = href;
    });
    $(document).on('change', 'input[name=ETS_CFU_CACHE_ENABLED]', function (e) {
        e.preventDefault();
        ETS_CTF_JS.cacheLifeTime();
    });
    $(document).on('click', '.ets_cfu_clear_cache', function (e) {
        e.preventDefault();
        let _this = $(this);
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            $.ajax({
                url: '',
                type: 'POST',
                data: 'ajax=1&action=clearCache',
                dataType: 'json',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            showErrorMessage(json.errors);
                        } else {
                            if (json.msg) {
                                showSuccessMessage(json.msg);
                            }
                        }
                    }
                },
                error: function () {
                    _this.removeClass('active');
                }
            });
        }
    });
    setMore_tab();
    $(window).load(function (e) {
        setMore_tab();
    });
    $(window).resize(function(e){
        $(".ets_form_tab_header .hide_more").removeClass('show_hover').removeClass('hide_more');
        setMore_tab();
    });
    $('.ets_form_tab_header .more_tab').on('click', function (e) {
        $(".ets_form_tab_header .hide_more").toggleClass('show_hover');
    });
    $(document).mouseup(function (e) {
        var confirm_popup = $('.ets_form_tab_header .hide_more');
        if (!confirm_popup.is(e.target) && confirm_popup.has(e.target).length === 0) {
            $(".ets_form_tab_header .hide_more").removeClass('show_hover');
            $('span.more_tab.active').removeClass('active');
        }
    });
});
function setMore_tab() {
    var menu_width_box = $('.ets_form_tab_header').parents('.form-wrapper').outerWidth();
    var itemwidthtab = $('.edit_contact_form .panel-heading').outerWidth();
    $(".ets_form_tab_header > span:not(.more_tab)").each(function () {
        var itemwidth = $(this).outerWidth();
        itemwidthtab = itemwidthtab + itemwidth;
        if (itemwidthtab > menu_width_box - 70 && itemwidthtab > 500) {
            $(this).addClass('hide_more');
        } else {
            $(this).removeClass('hide_more');
        }
    });
}
$(window).on('load', function () {
    ETS_CTF_JS.select_v2_v3();
});

