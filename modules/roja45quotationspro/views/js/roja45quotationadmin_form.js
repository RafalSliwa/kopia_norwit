/**
 * roja45moduleadmin.js.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

$(document).ready(function() {
    $( '.sortable' ).sortable({
        connectWith: '.sortable',
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
        dropOnEmpty: true,
        opacity: 0.8,
        tolerance: 'pointer'
    }).disableSelection();

    $('.roja45quotations_submitFormDesign').click(function (e) {
        roja45quotationspro.saveForm($(this));
    });

    $( ".sortable" ).on( "dragend.h5s", function( event ) {
        $('#'+event.target.id);
    });

    $(document).on('click', '.addFieldToColumn', function (e) {
        e.preventDefault();
        var col = $(this).data('col');
        //var form_panel = $(this).closest('.field_elements_form');
        //var form_panel = $(this).closest('form');
        //var forms = $(this).closest('.field_elements_form').find('form');
        var parent = $(this).closest('form');
        let component_name = null;
        var forms = parent.find('form');
        if (forms.length == 0) {
            component_name = parent.find('input[name=form_element_name]').val();
            forms = [
                parent
            ]
        }
        $.each(forms, function(key, value) {
            var form = $(value);
            var form_id = '#' + form.attr('id');
            var form_name = '#' + form.attr('name');

            form.find('.alert.alert-danger').fadeOut('fast', function () {
                form.find('.alert.alert-danger').empty();
                if (roja45quotationspro.validateElementForm(form_name)) {
                    var fields = form.serialize();
                    var element = form.attr('data-form-type');
                    var default_component = form.attr('data-form-default-component');
                    if (!default_component) {
                        var default_component = 0;
                    }
                    if (!component_name) {
                        component_name = form.find('input[name=form_element_name]').val();
                    }
                    roja45quotationspro.addElementToColumn(
                        col,
                        element,
                        component_name, //form.find('input[name=form_element_name]').val(),
                        fields,
                        default_component
                    );
                    component_name = null;
                    parent.parent().fadeOut('fast', function (e) {
                        form.find('select[name=form_element_required]').val('NO');
                        form.find('select[name=form_element_validation]').val('none');
                        //form.find('form')[0].reset();
                        $(form_name)[0].reset();
                    });
                    $(this).closest('form').find("input[type=text], textarea").val("");
                    $(this).closest('form').find('.form-ok').removeClass('form-ok');
                    $(this).closest('form').find('.form-error').removeClass('form-error');
                    $('body').scrollTo('#QUOTATION_FORM_ELEMENTS', 500);
                }
            });
        });
        $('#form_element_buttons').empty();
        $('#ROJA45_QUOTATIONSPRO_FORM_ELEMENT').val('DEFAULT');
    });

    $(document).on('click', '.panel button.close', function (e) {
        $(this).closest('.panel').remove();
    });

    $(document).on('click', '.cancelAdd', function (e) {
        var form_name = '#'+$(this).closest('form').attr('id');
        $(form_name).parent().fadeOut( 'fast', function(e) {
            $('#form_element_buttons').empty();
            $( 'select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT]').val('DEFAULT');
            $( 'select[name=form_element_required]').val('0');
            $( 'select[name=form_element_validation]').val('none');
            $(form_name)[0].reset();
        });
        $(this).closest('form').find("input[type=text], textarea").val("");
        $(this).closest('form').find('.form-ok').removeClass('form-ok');
        $(this).closest('form').find('.form-error').removeClass('form-error');
        $('body').scrollTo('#QUOTATION_FORM_ELEMENTS', 500);
    });

    $(document).on('click', '.updateField', function (e) {
        e.preventDefault();
        var field = $(this).data('field');
        var form_name = '#'+$(this).closest('.field_elements_form').attr('id');
        $(form_name+' .alert.alert-danger').fadeOut('fast', function() {
            $(form_name+' .alert.alert-danger').empty();

            if (roja45quotationspro.validateElementForm( form_name )) {
                var id = $(form_name).attr('data-field-id');

                var fields = $(form_name).find('form').serialize();
                $('#'+id+' input[name=configuration]').val(fields);

                $('#'+id+' .panel-heading-name').html($(form_name+' input[name=form_element_name]').val());
                $('#'+id+' input[name=name]').val($(form_name+' input[name=form_element_name]').val());

                $(form_name).fadeOut( 'fast', function(e) {
                    $(form_name+' #form_element_buttons').empty();
                    $(form_name+' select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT]').val('DEFAULT');
                    $(form_name+' select[name=form_element_required]').val('NO');
                    $(form_name+' select[name=form_element_validation]').val('none');
                });
                $(this).closest('form').find("input[type=text], textarea").val("");
                $(this).closest('form').find('.form-ok').removeClass('form-ok');
                $(this).closest('form').find('.form-error').removeClass('form-error');
            }
        });
    });

    $(document).on('click', '.cancelEdit', function (e) {
        var form_name = '#'+$(this).closest('.field_elements_form').attr('id');
        $(form_name).fadeOut( 'fast', function(e) {
            $('#form_element_buttons').empty();
            $( 'select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT]').val('DEFAULT');
            $( 'select[name=form_element_required]').val('NO');
            $( 'select[name=form_element_validation]').val('none');
            $(form_name).find('form')[0].reset();
        });
        $(this).closest('form').find("input[type=text], textarea").val("");
        $(this).closest('form').find('.form-ok').removeClass('form-ok');
        $(this).closest('form').find('.form-error').removeClass('form-error');
        $('body').scrollTo('#QUOTATION_FORM_ELEMENTS', 500);
    });

    $(document).on('click', '.edit-configuration', function (e) {
        e.preventDefault();
        roja45quotationspro.editConfiguration($(this));
    });

    $(document).on('click', '.delete-configuration', function (e) {
        e.preventDefault();
        roja45quotationspro.deleteElement($(this));
    });

    var num_col_prev = $( "select[name=ROJA45_QUOTATIONSPRO_NUM_COL] option:selected").val();
    $("#ROJA45_QUOTATIONSPRO_NUM_COL").change( function(e) {
        e.preventDefault();
        var cols = $( "select[name=ROJA45_QUOTATIONSPRO_NUM_COL] option:selected").val();
        var error_flag = false;
        // TODO - if required cols less than # cols
        // if cols = 2
        var i=0;
        for (i=(parseInt(cols)+1); i <= 3; i++ ) {
            if ($('*[data-column="'+i+'"] li').length > 0) {
                // SHOW WARNING
                error_flag = true;
                $( "select[name=ROJA45_QUOTATIONSPRO_NUM_COL]").val(num_col_prev);
                $( "#fields_warning_dialog" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            }
        }

        if (!error_flag) {
            num_col_prev = cols;
            $('input[name=ROJA45_QUOTATIONSPRO_NUM_COL]').val(cols);
            var count = 1;
            var col_width = 12;

            for (i=count; i <= cols; i++ ) {
                if (!$('*[data-column="'+count+'"]').length > 0) {
                    // create column
                    $('#form_design_columns').append('' +
                        '<section id="sortable'+i+'" data-column="'+i+'" class="form-column col-lg-' + col_width + '">' +
                        '<section id="form_design_column_'+i+'" class="filter_panel">' +
                        '<header class="clearfix"><div class="panel-heading-icon"><i class="icon-list-ul"></i></div><div class="panel-heading-name">' +
                        '<input type="text" class="form-column-title" name="form_element_column_title_'+i+'" value="Column '+i+'" data-validate="isText" onfocus="if(this.value == \'Column '+i+'\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Column 1\'; }"></div></header>' +
                        '<section class="filter_list"><ul class="list-unstyled droppable sortable connectedSortable"></ul></section>' +
                        '</section>' +
                        '</section>');
                } else {
                    //$('*[data-column="'+count+'"]').removeClass('col-lg-12 col-lg-6 col-lg-4');
                    //$('*[data-column="'+count+'"]').addClass('col-lg-'+col_width);
                }
                count++;
            }

            for (i=count; i <= 3; i++ ) {
                if ($('*[data-column="'+count+'"]').length > 0) {
                    // delete it
                    $('*[data-column="'+count+'"]').remove();
                }
                count++;
            }
            $( ".sortable" ).sortable( "destroy" );
            $( '.sortable' ).sortable({
                connectWith: '.sortable',
                placeholder: 'ui-state-highlight',
                forcePlaceholderSize: true,
                dropOnEmpty: true,
                opacity: 0.8,
                tolerance: 'pointer'
            }).disableSelection();

            $('#text_field_form').fadeOut( 'fast', function(e) {
                $('#form_element_buttons').empty();
                $( "select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT]").val('DEFAULT');
            });
        }
    });

    $('#ROJA45_QUOTATIONSPRO_FORM_ELEMENT').change( function (e) {
        e.preventDefault();
        var element = $( "select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT] option:selected").val();
        var cols = $( "select[name=ROJA45_QUOTATIONSPRO_NUM_COL] option:selected").val();

        $('.field_elements_form').fadeOut( 'fast', function(e) {
            $('#form_element_buttons').empty();
            $( 'select[name=ROJA45_QUOTATIONSPRO_FORM_ELEMENT]').val('DEFAULT');
            $( 'select[name=form_element_required]').val('0');
            $( 'select[name=form_element_validation]').val('none');
            //$(this)[0].reset();
        });
        switch (element) {
            case 'DEFAULT':
                $('#text_field_form').fadeOut( 'fast', function(e) {
                    $('.form_element_buttons').empty();
                });
                break;
            default:
                roja45quotationspro.selectElement(element);
                break;
            /*case 'TEXT':
                roja45quotationspro.selectElement(element);
                break;
            case 'CHECKBOX':
                roja45quotationspro.selectElement(element);
                break;
            case 'TEXTAREA':
                roja45quotationspro.selectElement(element);
                break;
            case 'SWITCH':
                roja45quotationspro.selectElement(element);
                break;
            case 'SELECT':
                roja45quotationspro.selectElement(element);
                break;
            case 'DATE':
                roja45quotationspro.selectElement(element);
                break;
            case 'DATES':
                roja45quotationspro.selectElement(element);
                break;
            case 'DATEPERIOD':
                roja45quotationspro.selectElement(element);
                break;
            case 'ADDRESS':
                roja45quotationspro.selectElement(element);
                break;
            case 'ADDRESS_SELECTOR':
                roja45quotationspro.selectElement(element);
                break;
            case 'HEADER':
                roja45quotationspro.selectElement(element);
                break;*/
        }
    });

    $('.form_element_contents').on('change', function(e) {
        e.preventDefault();
        var val = $(this).val();
        $('.field-dropdown-custom').hide();
        if (val == "0") {
            // clear options
            $('.field-dropdown-custom textarea').val('');
        } else if (val == "1") {
            $('.field-dropdown-custom').show();
        } else if (val == "2") {
            // countries
        } else if (val == "4") {
            // states
        }
    });

    $('#leave_bprice_on').click(function() {
        if (this.checked)
            $('#price').attr('disabled', 'disabled');
        else
            $('#price').removeAttr('disabled');
    });

    /*
    $('#QUOTATION_FORM_ELEMENTS').on('submit', function(e) {
        var html = '';
        for (i in conditions) {
            html += '<input type="hidden" name="condition_group_'+conditions[i].id_condition_group+'[]" value="'+conditions[i].type+'_'+conditions[i].value+'" />';
        }
        $('#condition_data').append(html);
    });
    */

    $(document).on('click', '#add_condition_category', function (e) {
        var id_condition = roja45quotationspro.add_condition(
            current_id_condition_group,
            'category',
            $('#id_category' + ' option:selected').val()
        );
        if (!id_condition)
            return false;

        var html = '<tr id="'+id_condition+'"><td>'+roja45_category_text+'</td><td>'+$('#id_category option:selected').html() +'</td><td><a href="#" onclick="roja45quotationspro.delete_condition(\''+id_condition+'\');" ' +
        'class="btn btn-default pull-right"><i class="icon-remove"></i> '+roja45_delete_text+'</a></td></tr>';
        roja45quotationspro.appendConditionToGroup(html);

        return false;
    });

    $(document).on('click', '#add_condition_group', function (e) {
        roja45quotationspro.new_condition_group();
        return false;
    });

    $(document).on('click', '.condition_group', function (e) {
        var id = this.id.split('_');
        roja45quotationspro.toggle_condition_group(id[2]);
        return false;
    });

    $(document).on('change', 'input[name=form_element_name]', function(e) {
        let val = $(this).val();
        $(this).closest('.form-group').removeClass('has-error');
        $(this).closest('.form-group').find('.input-group-addon').hide();
        
        let matches = $('#form_design_columns input[name=name][value='+val+']');
        if (matches.length > 0) {
            // error
            $(this).closest('.form-group').addClass('has-error');
            $(this).closest('.form-group').find('.input-group-addon').show();
        }
    });
});

var roja45quotationspro = (function (my) {
    my.selectElement = function(element) {
        var form = $('#' + element);
        var cols = $("select[name=ROJA45_QUOTATIONSPRO_NUM_COL] option:selected").val();
        form.find('#form_element_buttons').empty();
        var i = 0;
        for (i = 1; i <= cols; i++) {
            var width = (12 / cols);
            form.find('#form_element_buttons').append('<a href="#" data-col="' + i + '" class="addFieldToColumn btn btn-default btn btn-default" style="margin-right: 10px;"><i class="process-icon-edit"></i>Add To Column ' + i + '</a>');
        }

        let collapse_element = form.find('select[name=form_element_collapse]');
        if (collapse_element.length > 0) {
            let options = $('#form_design_columns input[name=name]');
            $.each(options, function(index, option) {
                collapse_element.append('<option value="'+$(option).val()+'">'+$(option).val()+'</option>');
            });
        }
        form.fadeIn('fast');
    };

    my.addElementToColumn = function (column, type, name, config, default_component) {
        // TODO - Check whether name already exists, add a number to it.
        var id = '';
        if ( $('#form_design_columns #' +name+'_'+column).length > 0 ) {
            id = name+'_'+column+'_' + $('#form_design_columns #'+ name+'_'+column).length +1;
        } else {
            id = name+'_'+column;
        }

        id = id.toLowerCase().replace(/\s+/g, '');

        $('#form_design_column_' + column + ' .sortable' ).append('<li id="' + id + '" class="filter_list_item" draggable="true" data-id="' + id + '" data-type="' + type + '" data-default-component="' + default_component + '" data-name="' + name + '"data-column="'+column+'"> \
            <input type="hidden" name="configuration" value="' + config + '"/> \
            <input type="hidden" name="id" value="' + id + '"/> \
            <input type="hidden" name="name" value="' + name + '"/> \
            <input type="hidden" name="type" value="' + type + '"/> \
            <input type="hidden" name="default_component" value="' + default_component + '"/> \
            <div class="col-lg-1 drag-icon"><h4><i class="icon-bars"></i></h4></div> \
            <div class="col-lg-5"><h4><span class="panel-heading-name">'+name+'</span></div>\
            <div class="col-lg-4"><h4><span class="panel-heading-type">['+ type +']</span></h4></div>\
            <div class="col-lg-2"><span class="panel-heading-action pull-right">\
            <a class="list-toolbar-btn edit-configuration" href="#" title="Configure"><i class="process-icon-configure"></i></a> \
            <a class="list-toolbar-btn delete-configuration" href="#" title="Delete"><i class="process-icon-delete"></i></a></span></div></li>');

        $( ".sortable" ).sortable( "destroy" );
        $( '.sortable' ).sortable({
            connectWith: '.sortable',
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            dropOnEmpty: true,
            opacity: 0.8,
            tolerance: 'pointer'
        }).disableSelection();

        //roja45quotationspro.initFormDesignerEvents();
    };

    my.editConfiguration = function ( ele ) {
        $('.field_elements_form').hide();
        var type = ele.closest('.filter_list_item').attr('data-type');
        var id = ele.closest('.filter_list_item').attr('data-id');
        var name = ele.closest('.filter_list_item').attr('data-name');
        var default_component = parseInt(ele.closest('.filter_list_item').attr('data-default-component'));
        id =  id.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        var serialized = $("#"+id+" input[name=configuration]").val();
        //var form = '#' + $('*[data-form-type="'+type+'"]').attr('id');
        var form = '#'+type;
        $(form).attr('data-field-id', id);
        let config = serialized.split('&');
        let components = {};

        let current_component = '';
        $.each(serialized.split('&'), function (index, elem) {
            var vals = elem.split('=');
            if (vals[0]=='form_element_name') {
                components[vals[1]] = {};
                current_component = vals[1];
            }
            components[current_component][vals[0]] = vals[1];
        });


        let collapse_element = $(form).find('select[name=form_element_collapse]');
        if (collapse_element.length > 0) {
            let options = $('#form_design_columns input[name=name]');
            $.each(options, function(index, option) {
                collapse_element.append('<option value="'+$(option).val()+'">'+$(option).val()+'</option>');
            });
        }

        //let config_json = JSON.stringify(components);

        $.each(components, function (i, component) {
            let form_element_name = component.form_element_name;
            let form_element_id = component.form_element_id;
            let form_element_type = component.form_element_type;

            $.each(component, function (j, element) {
                var current_element = null;
                if (form_element_id) {
                    current_element = $('#'+type+' [name=form_element_id][value=' + form_element_id + ']').closest('.panel').find("[name='" + j + "']");
                } else {
                    current_element = $('#'+type).find("[name='" + j + "']");
                }

                if (type == 'ADDRESS_SELECTOR' && j=='form_element_collapse') {

                    current_element.val(decodeURIComponent(element.replace(/\+/g , " ") ));
                }
                //let ele = $('input[name=form_element_name][value=' + form_element_name + ']').closest('.panel').find("[name='" + j + "']");
                /*if (form_element_type=='SELECT') {
                    current_element.val(decodeURIComponent(element.replace(/\+/g , " ") ));
                } else if (form_element_type=='XXX') {

                } else {
                    current_element.val(decodeURIComponent(element.replace(/\+/g , " ") ));
                }
                */
                current_element.val(decodeURIComponent(element.replace(/\+/g , " ") ));

                //$(form + " #" + id + " [name='" + index + "']").val(decodeURIComponent(element[0].replace(/\+/g , " ") ));
                /*if ($(form + " [name='" + index + "']").is('select')) {
                    $(form + " [name='" + index + "']").trigger('change');
                };*/
            });
            /*var vals = elem.split('=');
            $(form + " [name='" + vals[0] + "']").val(decodeURIComponent( vals[1].replace(/\+/g , " ") ));
            if ($(form + " [name='" + vals[0] + "']").is('select')) {
                $(form + " [name='" + vals[0] + "']").trigger('change');
            };*/
        });

        if (default_component==1) {
            $(form+' #form_element_name').addClass('roja45_disabled');
        } else {
            $(form+' #form_element_name').removeClass('roja45_disabled');
        }

        $(form+' #form_element_buttons').empty();
        $(form+' #form_element_buttons').append('<a href="#" data-field="'+ id + '" class="updateField btn btn-default btn btn-default" style="margin-right: 10px;"><i class="process-icon-edit"></i>Apply</a>');
       // $(form+' #form_element_buttons').append('<a href="#" class="cancelEdit btn btn-default btn btn-default" style="margin-right: 10px;"><i class="process-icon-edit"></i>Cancel</a>');
        //$('*[data-form-type="'+type+'"]').fadeIn( 'fast');
        $('#'+type).fadeIn( 'fast');
    };

    my.deleteElement = function (ele) {
        var column = ele.closest('.filter_list_item').attr('data-column');
        var id = ele.closest('.filter_list_item').attr('data-id');
        id =  id.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        $('#form_design_column_' + column + ' #'+id).fadeOut('fast', function(e) {
            $(this).remove();
        });
    };

    my.saveForm = function (ele) {
        roja45quotationspro.toggleModal();
        var html = '';
        for (i in conditions) {
            html += '<input type="hidden" name="condition_group_'+conditions[i].id_condition_group+'[]" value="'+conditions[i].type+'_'+conditions[i].value+'" />';
        }
        $('#condition_data').append(html);

        //var href = $(ele).attr('href') + '&action=submitForm&ajax=1';
        var config = {};
        config['num_columns'] = $('input[name=ROJA45_QUOTATIONSPRO_NUM_COL]').val();
        config['form_id'] = $('input[name=ROJA45_QUOTATIONSPRO_FORM_ID]').val();
        config['form_name'] = $('input[name=ROJA45_QUOTATIONSPRO_FORM_NAME]').val();
        config.columns = [];
        var column_titles = '';

        $('.form-column-title').each( function (i) {
            var val = $(this).val();
            var id = $(this).attr('name');
            if ( val != ('Column ' + (i+1)) ) {
                column_titles += id + '=' + val + '&';
            } else {
                column_titles += id + '=&';
            }
        });

        config['titles'] = column_titles.substring(0, column_titles.length-1);
        $('.form-column').each( function() {
            var column = {};
            var col = $(this).data('column');
            column.num = col;
            // if column not default value, set name
            var col_name = $('input[name=form_element_column_title_'+col+']').val();
            if (col_name != 'Column '+col) {
                column.name = col_name;
            }

            column.fields = [];
            $(this).find('.filter_list_item').each( function(i) {
                var field = {};
                field.pos = i;

                $(this).find('input').each(function() {
                    field[$(this).attr('name')] = $(this).val();
                });
                column.fields[i] = field;
            });
            config.columns[col] = column;
        });

        if (config) {
            var json = JSON.stringify(config);
            $('input[name=ROJA45_QUOTATIONSPRO_FORM]').val(json);
        }
    };

    my.validateElementForm = function (form) {
        var validate_fields = $(form+ ' .is_required');
        var error_count = 0;
        if (validate_fields.length > 0) {
            $(form+ ' .alert.alert-danger').append('<ol></ol>');
            validate_fields.each(function( index ) {
                var function_name = 'validate_' + $(this).data('validate');
                var string_to_test = $(this).val();

                if (typeof window[function_name] == 'function') {
                    var res = window[function_name](string_to_test);
                    if (!res) {
                        error_count++;
                        var field_name = $(this).closest('.form-group').find('label span').text();

                        if ($(this).attr('data-iso-code')) {
                            field_name = field_name + ' ('+$(this).attr('data-iso-code')+')';
                        }
                        $(form+ ' .alert.alert-danger ol').append('<li><b>'+field_name+'</b></li>');
                    };
                }
            });

            if (validate_fields.length == 1)
                $(form+ ' .alert.alert-danger').prepend('<p>There is 1 error</p>');
            else
                $(form+ ' .alert.alert-danger').prepend('<p>There are ' + error_count + ' errors</p>');
        }

        if (error_count == 0) {
            return true;
        } else {
            $(form+ ' .alert.alert-danger').fadeIn();
            return false;
        }
    };

    my.toggle_condition_group = function(id_condition_group)
    {
        $('.condition_group').removeClass('alert-info');
        $('.condition_group > table').removeClass('alert-info');
        $('#condition_group_'+id_condition_group+' > table').addClass('alert-info');
        $('#condition_group_'+id_condition_group).addClass('alert-info');
        current_id_condition_group = id_condition_group;
    };

    my.isInt = function(value) {
        return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
    };

    my.add_condition = function(id_condition_group, type, value)
    {
        var id_condition = id_condition_group+'_'+type+'_'+value;
        if (typeof conditions[id_condition] != 'undefined') {
            return false;
        }
        var condition = new Array();
        condition.type = type;
        condition.value = value;
        condition.id_condition_group = id_condition_group;
        conditions[id_condition] = condition;
        return id_condition;
    };

    my.delete_condition = function(condition)
    {
        delete conditions[condition];

        to_delete = $('#'+condition).prev();
        if ($(to_delete).children().hasClass('btn_delete_condition'))
            $(to_delete).remove();
        else
            $('#'+condition).next().remove();

        $('#'+condition).remove();
        return false;
    };

    my.new_condition_group = function()
    {
        $('#conditions-panel').show();
        var html = '';

        if (last_condition_group > 0)
            html += '<div class="row condition_separator text-center">OR</div><div class="clearfix">&nbsp;</div>';

        last_condition_group++;
        html += '<div id="condition_group_'+last_condition_group+'" class="panel condition_group alert-info"><h3><i class="icon-tasks"></i> Condition group '+last_condition_group+'</h3>';
        html += '<table class="table alert-info"><thead><tr><th class="fixed-width-md"><span' +
            ' class="title_box">Type</span></th><th><span' +
            ' class="title_box">Value</span></th><th></th></tr></thead><tbody></tbody></table>';
        html += '</div>';
        $('#condition_group_list').append(html);
        roja45quotationspro.toggle_condition_group(last_condition_group);
    };

    my.appendConditionToGroup = function(html)
    {
        if ($('#condition_group_'+current_id_condition_group+' table tbody tr').length > 0)
            $('#condition_group_'+current_id_condition_group+' table tbody').append('<tr><td class="text-center btn_delete_condition" colspan="3"><b>AND</b></td></tr>');
        $('#condition_group_'+current_id_condition_group+' table tbody').append(html);
    };

    my.toggleModal = function ()
    {
        $('#roja45_quotation_modal_dialog').toggle();
    };

    my.displaySuccessMsg = function (msg)
    {
        $.growl.notice({
            duration: 3000,
            location: 'immersive',
            title: 'Success',
            message: msg
        });
    };

    my.displayWarningMsg = function (msg)
    {
        $.growl.warning({
            duration: 6000,
            location: 'immersive',
            title: 'Warning',
            message: msg
        });
    };

    my.displayErrorMsg = function (msg) {
        $.growl.error({
            duration: 10000,
            location: "immersive",
            title: 'Error',
            message: msg
        });
    };

    return my;
}(roja45quotationspro || {}));

