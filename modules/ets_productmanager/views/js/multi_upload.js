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
var ets_pmn_images = "";
var ets_pmn_browsed_images = [];
var id_images = [];
var ets_pmn_id_images_uploading_cusrrent='';
$(document).ready(function(){
    if (window.File && window.FileReader && window.FileList && window.Blob && $('#ets_pmn_multiple_images').length >0) {
		 document.getElementById('ets_pmn_multiple_images').addEventListener("change", ets_pmn_read_file, false);
	}
    ets_pmnSortImageProduct();
});
function ets_pmnSortImageProduct()
{
    if($('#list-images-product').length)
    {
        var $myimage = $("#list-images-product");
    	$myimage.sortable({
    		opacity: 0.6,
            handle: ".dz-image",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updateImageOrdering&id_product="+$('#ets_pmn_id_product').val();						
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(json)
        			{ 
                        if(json.success)
                            $.growl.notice({ message: json.success });
                        if(json.errors)
                        {
                            $.growl.error({message:json.errors});
                            $myimage.sortable("cancel");
                        }
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }
}
function ets_pmn_show_added_files(file_images)
{
    var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];  
    ets_pmn_images = file_images;
	if(ets_pmn_images.length > 0)
	{
		for(var i = 0; i<ets_pmn_images.length; i++)
		{
            var html_image = "";
            console.log(ets_pmn_images[i]);
			var files_name_without_extensions = ets_pmn_images[i].name.substr(0, ets_pmn_images[i].name.lastIndexOf('.')) || ets_pmn_images[i].name;
			image_id = files_name_without_extensions.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
			if(!image_id)
            {
                image_id = ets_hashCode(files_name_without_extensions);
            }
            if(typeof ets_pmn_images[i] != undefined && ets_pmn_images[i].name != "")
			{
			    if($.inArray(ets_pmn_images[i].name.split('.').pop().toLowerCase(), fileExtension) != -1)
                {
                    html_image += '<div id="'+image_id+'" class="dz-preview dz-waiting dz-image-preview ui-sortable-handle">';
                        html_image += '<div class="dz-image bg"></div>';
                        html_image += '<div class="dz-progress"><span class="dz-upload" style="width: 0%;"></span></div>';
                        html_image += '<div class="dz-error-message"><span data-dz-errormessage=""></span></div>';
                        html_image += '<div class="dz-success-mark"></div>';
                        html_image += '<div class="dz-error-mark"></div>';
                    html_image +='</div>';
                    if($("#list-images-product .dz-image-preview").length)
                        $("#list-images-product .dz-image-preview:last").after(html_image);
                    else{
                        
                        $("#list-images-product").find('#form-images').before(html_image);
                    }   
                    ets_pmn_readURL(ets_pmn_images[i],image_id);
                    $('#product-images-dropzone').addClass('dz-started');
                }
				
			}
		}
	}
}
function ets_pmn_readURL(image,id_image) {
    $('.ets_pmn_errors').html('');
    if (image) {
        var reader = new FileReader();
        reader.onload = function (e) {
             $('#'+id_image+' .dz-image').html('<img src="'+e.target.result+'" style="width:140px;height:140px">');                     
        }
        reader.readAsDataURL(image);
    }
}
function ets_pmn_read_file(vpb_e)
{
    $('.ets_pmn_errors').html('');
    if(vpb_e.target.files) {

        ets_pmn_show_added_files(vpb_e.target.files);
		ets_pmn_browsed_images.push(vpb_e.target.files);
        vpb_submit_added_files();
	} else {
		alert('Sorry, a file you have specified could not be read at the moment. Thank You!');
	}
}
function vpb_submit_added_files()
{
    if(ets_pmn_browsed_images.length > 0) {
        ets_pmn_ajaxuploadmultipleimage(ets_pmn_browsed_images[ets_pmn_browsed_images.length-1],0);
	}
}
function ets_pmn_ajaxuploadmultipleimage(file,file_counter)
{
    
    var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp']; 
    if(typeof file[file_counter] != undefined && file[file_counter] != '' && $.inArray(file[file_counter].name.split('.').pop().toLowerCase(), fileExtension) != -1 )
	{
		//Use the file names without their extensions as their ids
        
		var files_name_without_extensions = file[file_counter].name.substr(0, file[file_counter].name.lastIndexOf('.')) || file[file_counter].name;
		ets_pmn_id_images_uploading_cusrrent =files_name_without_extensions.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
        if(!ets_pmn_id_images_uploading_cusrrent)
        {
            ets_pmn_id_images_uploading_cusrrent = ets_hashCode(files_name_without_extensions);
        }
        if($('#'+ets_pmn_id_images_uploading_cusrrent+'.dz-waiting').length >0)
        {
            $('#'+ets_pmn_id_images_uploading_cusrrent+'.dz-waiting').removeClass('dz-waiting').addClass('dz-processing'); 
            var dataString = new FormData();
    		dataString.append('upload_image',file[file_counter]);
    		dataString.append('submitUploadImageSave',1);
            dataString.append('id_product',$('#ets_pmn_id_product').val());
    		$.ajax({
    			type:"POST",
    			url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
    			data:dataString,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){
                        myXhr.upload.addEventListener('progress',progress, false);
                    }
                    return myXhr;
                },
                dataType: 'json',
    			cache: false,
    			contentType: false,
    			processData: false,
    			success:function(json) 
    			{
                    if(json.success)
                    {
                        var svg_trash_icon = '<svg class="w_14 h_14" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>';
                        $('#'+ets_pmn_id_images_uploading_cusrrent+' .dz-upload').css('width','100%');
                        $('#'+ets_pmn_id_images_uploading_cusrrent).removeClass('dz-processing').addClass('dz-complete');
                        $('#'+ets_pmn_id_images_uploading_cusrrent).addClass('ets_pmn_edit_image').attr('data-id',json.id_image).attr('url-delete',json.link_delete).attr('url-update',json.link_update);
                        $('#'+ets_pmn_id_images_uploading_cusrrent).attr('id','images-'+json.id_image);
                        $('#images-'+json.id_image+' .dz-image').attr('data-url',json.link).css('background-image',' url("'+json.link+'")');
                        $('#images-'+json.id_image+' .dz-image').html('<button class="btn btn-sm btn-link ets_pmn_delete_image" type="button" title="Delete" data-id="'+json.id_image+'"><i class="ets_svg_icon">'+svg_trash_icon+'</i></button>');
                        $('#product-images-dropzone').addClass('dz-started');
                        if(json.iscover)
                        {
                            $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product >a:first').html('<img class="imgm img-thumbnail" src="'+json.link+'">')
                        }
                    }
                    if(json.errors)
                    {
                        $('.ets_pmn_errors').html(json.errors);
                        
                        $('#'+ets_pmn_id_images_uploading_cusrrent).remove();
                        if ( $('#list-images-product > .dz-preview').length > 0 ){
                            return;
                        } else {
                            $('#product-images-dropzone').removeClass('dz-started');
                        }
                    }
                    if (file_counter+1 < file.length ) {
    					ets_pmn_ajaxuploadmultipleimage(file,file_counter+1); 
    				}
                    else
                    {
                        $('#ets_pmn_multiple_images').removeAttr('disabled');
                        $('#ets_pmn_multiple_images').val('');
                        $('.dz-processing').removeClass('dz-processing').addClass('dz-complete');
                    }
    			},
                error: function(xhr, status, error)
                {
                    
                }
    		});
        }
        else
        {
            setTimeout(function() {
				if (file_counter+1 < file.length ) {
					ets_pmn_ajaxuploadmultipleimage(file,file_counter+1); 
				}
                else
                {
                    $('#ets_pmn_multiple_images').removeAttr('disabled');
                    $('#ets_pmn_multiple_images').val('');
                    $('.dz-processing').removeClass('dz-processing').addClass('dz-complete');
                }
			},1000);
        }
	}
    else
    {
        if($.inArray(file[file_counter].name.split('.').pop().toLowerCase(), fileExtension) == -1)
            $.growl.error({message:'You can\'t upload files of this type'});
        setTimeout(function() {
			if (file_counter+1 < file.length ) {
				ets_pmn_ajaxuploadmultipleimage(file,file_counter+1); 
			}
            else
            {
                $('#ets_pmn_multiple_images').removeAttr('disabled');
                $('#ets_pmn_multiple_images').val('');
                $('.dz-processing').removeClass('dz-processing').addClass('dz-complete');
            }
		},1000);
    }
}
function progress(e){
    if(e.lengthComputable){
        var max = e.total;
        var current = e.loaded;
        var Percentage = (current * 100)/max;
        if(Percentage < 100)
        {
           $('#'+ets_pmn_id_images_uploading_cusrrent+' .dz-upload').css('width',Percentage+'%'); 
        }
    }  
 }
 function ets_pmn_file_ext(file) {
	return (/[.]/.exec(file)) ? /[^.]+$/.exec(file.toLowerCase()) : '';
}
function ets_hashCode(str) {
    var hash = '', i, chr;
    for (i = 0; i < str.length; i++) {
      chr   = str.charCodeAt(i);
      hash  += chr;
    }
    return hash;
  }