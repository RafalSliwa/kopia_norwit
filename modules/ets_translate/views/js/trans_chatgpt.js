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

var etsTransChatGpt = {

	xhr_gpt: false,
	cancel_gpt: false,
	id_chat_box_ele: '#ets-trans-chatgpt-box',
	class_chat_box_ele: '.ets-trans-chatgpt-box',
	id_chat_box_container_ele: '#container-chatgpt',
	loading: false,
	id_language: '',

	loading_text: function () {
		return etsTransFunc.trans('loading_text');
	},
	initBtnChatGPT: function () {
		if (etsTransPageType == 'product' && etsTransIsDetailPage && ETS_TRANS_ENABLE_CHATGPT) {
			var fields_chatGPT = ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2 ? etsTranslateDefine.product.fields_chatGPT.ps810 : etsTranslateDefine.product.fields_chatGPT.ps17;
			var classForTransBoundary = 'ets-trans-chatgpt-field-boundary ' + (ETS_TRANS_GTE_810 ? 'ets-trans-810' : ETS_TRANS_IS_1780 ? 'ets-trans-17' : '');
			$.each(fields_chatGPT, function (i, el) {
				$('[id*=' + el + ']').each(function () {
					if (!$(this).closest('.translations').find('.js-ets-trans-btn-trans-chatgpt-field-item').length) {
						$(this).closest('.translations').addClass(classForTransBoundary).append(etsTransChatGpt.renderBtnChatGPTItem(el)).parent().addClass('ets_button_top_chatgpt');
						if ($(this).is('textarea')) {
							$(this).closest('.translations').addClass('form-helper-editor-chatgpt');
						}
					}
				});
			});
		}
		return false
	},

	renderBtnChatGPTItem: function (field, className) {
		className = className || '';
		field = field || '';
		var button = '<button type="button" class="ets-trans-button has_tooltip btn ets-trans-btn-trans-chatgpt-field-item js-ets-trans-btn-trans-chatgpt-field-item '+className+'" title="'+ etsTransFunc.trans('chatgpt') +'" data-field="' + field + '">';
		button += '<span class="ets_tooltip">'+etsTransFunc.trans('chatgpt')+'</span>';
		button += '<svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>';
		button += '</button>';

		return button;
	},

	renderFormChatGPT: function (field, className) {
		className = className || '';
		field = field || '';
		var content = '<div id="ets-trans-form-chatgpt-' + field + '" class="ets-trans-form-chatgpt js-ets-trans-form-chatgpt '+className+' hide" data-field="'+field+'">';
		content += '<textarea name="ets-trans-chatgpt-prompt-' + field + '" class="ets-trans-textarea-chatgpt-prompt ets-trans-textarea-chatgpt js-ets-trans-textarea-chatgpt" id="ets-trans-chatgpt-prompt-' + field + '"></textarea>';
		content += '<button class="ets-trans-button btn ets-trans-btn-submit-chatgpt js-ets-trans-btn-submit-chatgpt">';
		content += etsTransFunc.trans('submit_chatgpt');
		content += '</button>';
		content += '<button class="ets-trans-button btn ets-trans-btn-cancel-chatgpt js-ets-trans-btn-cancel-chatgpt">' + etsTransFunc.trans('cancel_chatgpt') +'</button>'
		content += '</div>';
		return content;
	},

	clearDataForm: function (clearPrompt = true, clearDataChat = false) {
		if (clearPrompt) {
			$(this.id_chat_box_ele).find('.chatgpt-box-send textarea').val('');
		}
		if (clearDataChat) {
			// do remove all data chat
		}
	},

	validateBeforeSend: function (prompt) {
		var res = {error: false, message: ''};
		if (!prompt)
			res = {error: true, message: etsTransFunc.trans('empty_prompt_chatgpt')};
		return res;
	},

	closeForm: function () {
		this.clearDataForm();
		if (this.xhr_gpt)
			this.xhr_gpt.abort();
		this.cancel_gpt = true;
		$(this.id_chat_box_ele).find('.js-btn-send-gpt').removeClass('loading');
		$(this.id_chat_box_ele).hide();
		$('#container-chatgpt').removeClass('show');
		$('body').removeClass('no_scroll');
	},

	openForm: function (btn) {
		this.cancel_gpt = false;
		$(this.id_chat_box_ele).show();
		$(this.id_chat_box_ele)
		$('#container-chatgpt').addClass('show');
		$(this.id_chat_box_ele).attr('data-apply-for', $(btn).attr('data-field'));
		this.scrollBottom(this.id_chat_box_ele  + ' .form-wrapper');
		this.focusMessageBox();
		this.resizeable();
	},

	focusMessageBox: function () {
		setTimeout(function () {
			$(etsTransChatGpt.id_chat_box_ele + ' .chatgpt-box-send textarea').trigger('focus');
		}, 100);
	},

	appendContentChat: function (message, idMessage = 0, is_customer = true, is_loading = false, classContentMessage = '') {
		var chat_history_box = this.id_chat_box_ele + ' #chatgpt-history-list';
		var is_bot_class = is_customer ? 'is_customer' : 'is_chatgpt';
		var icon_custommer = '<i class="svg_icon" title="' + etsTransFunc.trans("you") + '"><svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 0q182 0 348 71t286 191 191 286 71 348q0 181-70.5 347t-190.5 286-286 191.5-349 71.5-349-71-285.5-191.5-190.5-286-71-347.5 71-348 191-286 286-191 348-71zm619 1351q149-205 149-455 0-156-61-298t-164-245-245-164-298-61-298 61-245 164-164 245-61 298q0 250 149 455 66-327 306-327 131 128 313 128t313-128q240 0 306 327zm-235-647q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5z"></path></svg> </i>';
		if (!is_customer)
			icon_custommer = '<i class="svg_icon" title="' + etsTransFunc.trans("chatgpt") + '"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512"> <rect ry="105.042" fill="#10A37F" rx="105.187" width="512" height="512"></rect> <path fill="#fff" fill-rule="nonzero" d="M378.68 230.011a71.432 71.432 0 003.654-22.541 71.383 71.383 0 00-9.783-36.064c-12.871-22.404-36.747-36.236-62.587-36.236a72.31 72.31 0 00-15.145 1.604 71.362 71.362 0 00-53.37-23.991h-.453l-.17.001c-31.297 0-59.052 20.195-68.673 49.967a71.372 71.372 0 00-47.709 34.618 72.224 72.224 0 00-9.755 36.226 72.204 72.204 0 0018.628 48.395 71.395 71.395 0 00-3.655 22.541 71.388 71.388 0 009.783 36.064 72.187 72.187 0 0077.728 34.631 71.375 71.375 0 0053.374 23.992H271l.184-.001c31.314 0 59.06-20.196 68.681-49.995a71.384 71.384 0 0047.71-34.619 72.107 72.107 0 009.736-36.194 72.201 72.201 0 00-18.628-48.394l-.003-.004zM271.018 380.492h-.074a53.576 53.576 0 01-34.287-12.423 44.928 44.928 0 001.694-.96l57.032-32.943a9.278 9.278 0 004.688-8.06v-80.459l24.106 13.919a.859.859 0 01.469.661v66.586c-.033 29.604-24.022 53.619-53.628 53.679zm-115.329-49.257a53.563 53.563 0 01-7.196-26.798c0-3.069.268-6.146.79-9.17.424.254 1.164.706 1.695 1.011l57.032 32.943a9.289 9.289 0 009.37-.002l69.63-40.205v27.839l.001.048a.864.864 0 01-.345.691l-57.654 33.288a53.791 53.791 0 01-26.817 7.17 53.746 53.746 0 01-46.506-26.818v.003zm-15.004-124.506a53.5 53.5 0 0127.941-23.534c0 .491-.028 1.361-.028 1.965v65.887l-.001.054a9.27 9.27 0 004.681 8.053l69.63 40.199-24.105 13.919a.864.864 0 01-.813.074l-57.66-33.316a53.746 53.746 0 01-26.805-46.5 53.787 53.787 0 017.163-26.798l-.003-.003zm198.055 46.089l-69.63-40.204 24.106-13.914a.863.863 0 01.813-.074l57.659 33.288a53.71 53.71 0 0126.835 46.491c0 22.489-14.033 42.612-35.133 50.379v-67.857c.003-.025.003-.051.003-.076a9.265 9.265 0 00-4.653-8.033zm23.993-36.111a81.919 81.919 0 00-1.694-1.01l-57.032-32.944a9.31 9.31 0 00-4.684-1.266 9.31 9.31 0 00-4.684 1.266l-69.631 40.205v-27.839l-.001-.048c0-.272.129-.528.346-.691l57.654-33.26a53.696 53.696 0 0126.816-7.177c29.644 0 53.684 24.04 53.684 53.684a53.91 53.91 0 01-.774 9.077v.003zm-150.831 49.618l-24.111-13.919a.859.859 0 01-.469-.661v-66.587c.013-29.628 24.053-53.648 53.684-53.648a53.719 53.719 0 0134.349 12.426c-.434.237-1.191.655-1.694.96l-57.032 32.943a9.272 9.272 0 00-4.687 8.057v.053l-.04 80.376zm13.095-28.233l31.012-17.912 31.012 17.9v35.812l-31.012 17.901-31.012-17.901v-35.8z"></path> </svg></i>';

		classContentMessage += is_loading ? ' chatgpt-loading' : '';

		var id = '';
		if (idMessage) {
			id = 'id="chatgpt-message-' + idMessage + '"';
		}
		if (!message && is_loading)
			$(chat_history_box).append('<li ' + id + ' class="chatgpt-message ' + is_bot_class + '"><div class="chatgpt-content">'+icon_custommer+'<p class="chatgpt-content chatgpt-loading"></p></div></li>');
		else
			$(chat_history_box).append('<li ' + id + ' class="chatgpt-message ' + is_bot_class + '"><div class="chatgpt-content">'+icon_custommer+'<p class="chatgpt-content ' + classContentMessage + '">'+message+'</p></div></li>');
		this.scrollBottom(this.id_chat_box_ele + ' .form-wrapper');
		this.fixedTopFormChat(30);
	},

	submitChat: function (prompt, btn, idChatgpt, cb) {
		var formData = {
			prompt,
			model: '',
			page_id: etsTransPageId || 0,
			col_data: etsTranslateDefine.getColData('product'),
			id_language: etsTransChatGpt.id_language,
			apply_for: $(this.id_chat_box_ele).attr('data-apply-for')
		};
		this.cancel_gpt = false;
		etsTransChatGpt.xhr_gpt = $.ajax({
			url: ETS_TRANS_LINK_AJAX,
			type: 'POST',
			data: {
				etsTransSubmitChatGPT: 1,
				formData
			},
			dataType: 'json',
			beforeSend: function () {
				$(btn).addClass('loading');
				$(btn).prop('disabled', true);
			},
			success: function (res) {
				cb(res)
			},
			complete: function () {
				etsTransChatGpt.loading = false;
				$(btn).removeClass('loading');
				$(btn).prop('disabled', false);
				if (!etsTransChatGpt.cancel_gpt) {
					$('#chatgpt-message-'+idChatgpt).html('<div class="chatgpt-error">'+etsTransFunc.trans('ChatGPT_API_request_error_text')+'</div>');
				} else
					$('#chatgpt-message-'+idChatgpt).remove();
			}
		});
	},

	clearDataChat: function (btn, cb) {
		$.ajax({
			url: ETS_TRANS_LINK_AJAX,
			type: 'POST',
			data: {
				etsTransClearDataChat: 1,
			},
			dataType: 'json',
			beforeSend: function () {
				$(btn).addClass('loading');
				$(btn).prop('disabled', true);
			},
			success: function (res) {
				cb(res)
			},
			complete: function () {
				etsTransChatGpt.loading = false;
				$(btn).removeClass('loading');
				$(btn).prop('disabled', false);
			}
		});
	},
	fixedTopFormChat: function (top) {
		var chatgpt_box = $(this.id_chat_box_ele);
		top = top ? top + 'px' : '30px';
		if(chatgpt_box.outerHeight() + 100 > $(window).height())
		{
			chatgpt_box.css('top',top);
		}
	},
	resizeable: function () {
		this.fixedTopFormChat(30);
		$(this.class_chat_box_ele + '.resize').resizable();
	},
	draggable: function () {
		if ($(this.id_chat_box_ele).length && ETS_TRANS_ENABLE_CHATGPT) {
			var click = {
				x: 0,
				y: 0
			};

			$(this.id_chat_box_ele).draggable({
				cursor: "grabbing",
				connectToSortable: "body",
				containment: "body",
				handle: ".panel-heading",
				scroll: false,
				start: function( event, ui ) {
					click.x = event.clientX;
					click.y = event.clientY;
				},
				drag: function(event, ui) {
					var original = ui.originalPosition;
					var left = event.clientX - click.x + original.left;
					var top=event. clientY - click.y + original.top;
					var max_left = $(window).width()- $(etsTransChatGpt.id_chat_box_ele).outerWidth();
					var max_top = $(window).height() - $(etsTransChatGpt.id_chat_box_ele).outerHeight();
					if(left>max_left)
						left=max_left;
					if(top>max_top)
						top=max_top;
					ui.position = {
						left: left >0 ? left :0,
						top:  top >0 ? top :0,
					};
				},
				stop: function(event,ui){
					var original = ui.originalPosition;
					var left = event.clientX - click.x + original.left;
					var top=event. clientY - click.y + original.top;
					var max_left = $(window).width()-$(etsTransChatGpt.id_chat_box_ele).outerWidth();
					var max_top = $(window).height()-$(etsTransChatGpt.id_chat_box_ele).outerHeight();
					if(left>max_left)
						left=max_left;
					if(top>max_top)
						top=max_top;
					$(etsTransChatGpt.id_chat_box_ele).attr('data-left',left >0 ? left : 0);
					$(etsTransChatGpt.id_chat_box_ele).attr('data-top',top > 0 ? top :0);
					$(etsTransChatGpt.id_chat_box_ele).css('left',(left> 0 ? left :0)+'px');
					$(etsTransChatGpt.id_chat_box_ele).css('top',(top> 0 ? top :0)+'px');
				}
			});
		}
	},
	getForm: function (btn, cb) {
		var formData = {
			id_language,
			apply_for: $(btn).attr('data-field')
		};
		$.ajax({
			url: ETS_TRANS_LINK_AJAX,
			type: 'POST',
			data: {
				etsTransOpenChatGPT: 1,
				formData
			},
			dataType: 'json',
			beforeSend: function () {
				$(btn).addClass('loading');
				$(btn).prop('disabled', true);
			},
			success: function (res) {
				cb(res);
			},
			complete: function () {
				$(btn).removeClass('loading');
				$(btn).prop('disabled', false);
			}
		});
	},
	refreshHeightBoxChatGPT: function (btn, cb) {
		if ( $('.box-actions').length > 0){
			var textbox_height = $('.chatgpt-box-send').outerHeight() + $('.ybc-chatgpt-box .panel-heading').outerHeight() + 40 + $('.box-actions').outerHeight();
		} else {
			var textbox_height = $('.chatgpt-box-send').outerHeight() + $('.ets-trans-chatgpt-box .panel-heading').outerHeight() + 40;
		}
		$('.ets-trans-chatgpt-box > .form-wrapper').css('max-height','calc(100% - '+ textbox_height +'px)');
		if ( $('#chatgpt-history-list li').length > 0 ){
			$('.ets-trans-chatgpt-box').css('min-height','calc('+ textbox_height +'px + 140px)');
		} else {
			$('.ets-trans-chatgpt-box').css('min-height','calc('+ textbox_height +'px + 40px)');
		}
	},
	hideOtherLanguage: function (id) {
		$(this.id_chat_box_ele + ' .translatable-field').hide();
		$(this.id_chat_box_ele + ' .lang-' + id).show();
		etsTransChatGpt.id_language = id;
		if (id != 'all') {
			var id_old_language = id_language;
			id_language = id;
			if (id_old_language != id) {
				for (var key in ETS_TRANS_ALL_LANGUAGES) {
					if (id == ETS_TRANS_ALL_LANGUAGES[key]['id_lang']) {
						var isoCode = ETS_TRANS_ALL_LANGUAGES[key]['iso_code'];
						$('#form_switch_language option[value="' + isoCode + '"]').prop('selected', true);
						$('#form_switch_language').change();
						break;
					}
				}
			}
			updateCurrentText();
		}
	},
	formSwitchLang: function (isoCode) {
		for (var key in ETS_TRANS_ALL_LANGUAGES) {
			if (isoCode == ETS_TRANS_ALL_LANGUAGES[key]['iso_code']) {
				var id = ETS_TRANS_ALL_LANGUAGES[key]['id_lang'];
				id_language = id;
				$(this.id_chat_box_ele + ' .translatable-field').hide();
				$(this.id_chat_box_ele + ' .lang-' + id).show();
				break;
			}
		}
	},
	applyContent: function (btn, field, content, id_lang) {
		if ($('#' + field + id_lang).hasClass('js-taggable-field') || ($('#' + field + id_lang).length && $('#' + field + id_lang).attr('name').indexOf('keywords') !== -1)) {
			$('#' + field + id_lang).val(content.replace(/\|/g, ','));
			if(!$('#' + field + id_lang).parent().hasClass('ets-trans-field-apply-chatgpt-success')){
				$('#' + field + id_lang).parent().addClass('ets-trans-field-apply-chatgpt-success');
			}
			$('#' + field + id_lang).change();
			if ($('#' + field + id_lang).hasClass('tagify')) {
				etsTranslateDefine.addKeywords('#' + field + id_lang);
			}
		} else {
			$('#' + field + id_lang).val(content);
			if(!$('#' + field + id_lang).hasClass('ets-trans-field-apply-chatgpt-success')){
				$('#' + field + id_lang).addClass('ets-trans-field-apply-chatgpt-success');
			}
			if (ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2 && $('#product_footer_save').length) {
				$('#product_footer_save').prop("disabled", false);
			}
		}
		if (typeof tinyMCE !== "undefined" && $('#' + field + id_lang).hasClass('autoload_rte')) {
			$('#' + field + id_lang).prev().find('iframe').next('div').hide();
			tinyMCE.get(field + id_lang).setContent(content);
			if(!$('#' + field + id_lang).parent().hasClass('ets-trans-field-apply-chatgpt-success')){
				$('#' + field + id_lang).parent().addClass('ets-trans-field-apply-chatgpt-success');
			}
		}
	},
	clickSendBtn: function (btn) {
		if ($(btn).hasClass('loading'))
			return false;
		if (this.loading)
			return false;
		var id_lang = $(btn).attr('data-lang');
		var prompt = $(btn).parents(this.id_chat_box_ele).find('.chatgpt-box-send textarea').val().trim();
		if (id_lang) {
			prompt = etsTransFunc.getContentByShortCode(prompt, id_lang);
		}
		var valid = etsTransChatGpt.validateBeforeSend(prompt);
		if (valid.error) {
			etsTransFunc.showErrorMessage(valid.message);
			return false;
		}
		this.loading = true;
		if (etsTransChatGpt.xhr_gpt)
			etsTransChatGpt.xhr_gpt.abort();

		// do submit chatgpt
		etsTransChatGpt.appendContentChat(prompt);
		var idChatgpt = Math.round(Math.random()*1000000000);
		etsTransChatGpt.appendContentChat('', idChatgpt, false, true);
		etsTransChatGpt.clearDataForm();
		etsTransChatGpt.submitChat(prompt, btn, idChatgpt,function (res) {
			if (!res.errors && res.data) {
				$('#chatgpt-message-'+idChatgpt).replaceWith(res.data);
			} else {
				etsTransFunc.showErrorMessage(res.message);
				$('#chatgpt-message-'+idChatgpt).html('<div class="chatgpt-error">'+res.message+'</div>');
			}
			etsTransChatGpt.loading = false;
			if ($(etsTransChatGpt.id_chat_box_ele +' .js-clear-content-chatgpt').hasClass('hide')) {
				$(etsTransChatGpt.id_chat_box_ele +' .js-clear-content-chatgpt').removeClass('hide');
			}
			etsTransChatGpt.fixedTopFormChat(30);
		})
	},
	openTemplateModal: function (btn, content) {
		if(!$('#ets-trans-form-popup').length)
		{
			var html ='<div class="ets_trans_popup show">';
			html += '<div class="popup_content table">';
			html +='<div class="popup_content_tablecell">';
			html +='<div class="popup_content_wrap" style="position: relative">';
			html +='<span class="close_popup" title="Close">+</span>';
			html +='<div id="ets-trans-form-popup"></div>';
			html +='</div>';
			html +='</div>';
			html += '</div>';
			html +='</div>';
			$(btn).closest('form').parent().append(html);
		}
		else
			$('.ets_trans_popup').addClass('show');
		$('#ets-trans-form-popup').html(content);
	},
	scrollBottom: function (ele, speed = 500) {
		$(ele).animate({ scrollTop: $(ele).prop("scrollHeight")}, speed);
	}

}

$(document).ready(function () {

	if (typeof ETS_TRANS_CURRENT_LANGUAGE !== "undefined" && typeof ETS_TRANS_CURRENT_LANGUAGE.id !== "undefined" && typeof id_language !== "undefined") {
		id_language = ETS_TRANS_CURRENT_LANGUAGE.id;
		etsTransChatGpt.id_language = ETS_TRANS_CURRENT_LANGUAGE.id;
	}
	etsTransChatGpt.initBtnChatGPT();
	etsTransChatGpt.draggable();

	$(document).on('click', etsTransChatGpt.id_chat_box_ele + ' .maximize-chat-gpt', function () {
		$(etsTransChatGpt.id_chat_box_ele).addClass('maximize');
		$(etsTransChatGpt.id_chat_box_ele).removeClass('minimize');
	});
	$(document).on('click',etsTransChatGpt.id_chat_box_ele + ' .maximize-chat-gpt',function(){
		$(etsTransChatGpt.id_chat_box_ele).addClass('maximize');
		$(etsTransChatGpt.id_chat_box_ele).removeClass('minimize');
	});
	$(document).on('click',etsTransChatGpt.id_chat_box_ele +' .minimize-chat-gpt',function(){
		$(etsTransChatGpt.id_chat_box_ele).addClass('minimize');
		$(etsTransChatGpt.id_chat_box_ele).removeClass('maximize');
	});

	$(document).on('click', '#ets-trans-chatgpt-box .js-btn-cancel-gpt, #ets-trans-chatgpt-box .close-chatgpt-box', function (e) {
		e.preventDefault();
		etsTransChatGpt.closeForm();
	})

	$(document).on('click', etsTransChatGpt.id_chat_box_ele +' .js-btn-send-gpt', function (e) {
		e.preventDefault();
		etsTransChatGpt.clickSendBtn(this);
		$('.ets-trans-chatgpt-box .form-wrapper').css('max-height','');
		etsTransChatGpt.refreshHeightBoxChatGPT();
		return false;
	});

	$(document).on('click', etsTransChatGpt.id_chat_box_ele +' .btn-apply-chatgpt', function (e) {
		e.preventDefault();
		var field = $(this).parents('.chatgpt-button-append').find('select[name="content-apply-chatgpt"]').val();
		var chatgpt_content = $(this).parents('li').find('.chatgpt-content p').html().trim();
		if (etsTransChatGpt.id_language == 'all') {
			if (typeof ETS_TRANS_ALL_LANGUAGES !== "undefined") {
				for (var key in ETS_TRANS_ALL_LANGUAGES) {
					etsTransChatGpt.applyContent(this, field, chatgpt_content, ETS_TRANS_ALL_LANGUAGES[key]['id_lang']);
				}
			}
		} else {
			etsTransChatGpt.applyContent(this, field, chatgpt_content, etsTransChatGpt.id_language);
		}
		etsTransFunc.showSuccessMessage(etsTransFunc.trans('apply_content_from_chatgpt_success'));
		return false;
	});
	
	$(document).on('keyup', etsTransChatGpt.id_chat_box_ele +' .chatgpt-box-send textarea', function (e) {
		if ((e.key === 'Enter' || e.keyCode === 13) && !e.shiftKey) {
			etsTransChatGpt.clickSendBtn(etsTransChatGpt.id_chat_box_ele + ' .js-btn-send-gpt');
			return false;
		}
	});

	$(document).on('click','.js-ets-trans-tab-element .chatgpt .btn-new-item',function(e){
		e.preventDefault();
		var html = $(this).closest('.js-ets-trans-tab-element').find('.ets_trans_chatgpt_template_modal .js-ets-trans-box-form-chatgpt').html();
		etsTransChatGpt.openTemplateModal(this, html);
	});

	$(document).on('click','#list-ets_trans_chatgpt .edit',function(e){
		var $this = $(this);
		e.preventDefault();
		if(!$this.hasClass('loading'))
		{
			$this.addClass('loading');
			$.ajax({
				url: $this.attr('href'),
				data: {
					ajax: 1,
					editTempChatGpt: 1
				},
				type: 'post',
				dataType: 'json',
				success: function (json) {
					$this.removeClass('loading');
					if(json.form)
					{
						etsTransChatGpt.openTemplateModal('.js-ets-trans-tab-element .chatgpt .btn-new-item', json.form);
					}
				},
				error: function(xhr, status, error)
				{
					$this.removeClass('loading');
					var err = eval("(" + xhr.responseText + ")");
					alert(err.Message);
				}
			});
		}
	});

	$(document).on('click','#list-ets_trans_chatgpt .delete-gpt-template',function(e) {
		e.preventDefault();
		if(!$(this).hasClass('loading'))
		{
			if(confirm($(this).data('confirm')))
			{
				$(this).addClass('loading');
				var $this = $(this);
				$.ajax({
					url: $this.attr('href'),
					data: {
						ajax: 1
					},
					type: 'post',
					dataType: 'json',
					success: function (json) {
						if(json.success)
						{
							etsTransFunc.showSuccessMessage(json.success);
							$this.closest('tr').remove();
						}
					},
					error: function(xhr, status, error)
					{
						$this.removeClass('loading');
						var err = eval("(" + xhr.responseText + ")");
						alert(err.Message);
					}
				});
			}
		}
		return false;

	});

	$(document).on('click','button[name="etsTransSaveTemplateGPT"]',function(e){
		e.preventDefault();
		if(!$(this).hasClass('loading'))
		{
			$('.module_error.alert-danger').remove();
			$(this).addClass('loading');
			var action = $(this).parents('form').attr('action');
			var $button=  $(this);
			var formData = new FormData($(this).parents('form').get(0));
			formData.append('ajax',1);
			if ( $('.chatgpt_error').length > 0 ){
				$('.chatgpt_error').remove();
			}
			$.ajax({
				url: action,
				data: formData,
				type: 'post',
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(json){
					$button.removeClass('loading');
					if(json.success)
					{
						etsTransFunc.showSuccessMessage(json.success);
						$('.js-ets-trans-tab-element .list-chatgpt').html(json.list);
						$('.ets_trans_popup').removeClass('show');
					}
					if(json.errors || !json.success)
					{
						var mess = json.errors ? json.errors : json.message;
						$('body').append('<div class="chatgpt_error">'+mess+'</div>');
						setTimeout(function(e){
							if ( $('.chatgpt_error').length > 0 ){
								$('.chatgpt_error').remove();
							}
						}, 4000);

						if (json.message) {
							etsTransFunc.showErrorMessage(json.message);
						}
					}
				},
				error: function(xhr, status, error)
				{
					$button.removeClass('loading');
					var err = eval("(" + xhr.responseText + ")");
					alert(err.Message);
				}
			});
		}
	});

	$(document).on('click', etsTransChatGpt.id_chat_box_ele +' .gpt-item-template',function(e){
		var content = $(this).data('content');
		var id_lang = $(this).data('lang');
		$(etsTransChatGpt.id_chat_box_ele + ' .js-btn-send-gpt').attr('data-lang', id_lang);
		$('textarea[name="message-chatgpt"]').val(content);
		$('textarea[name="message-chatgpt"]').focus();
	});

	$(document).on('click', etsTransChatGpt.id_chat_box_ele +' .js-clear-content-chatgpt', function (e) {
		e.preventDefault();
		var _that = this;
		if(!$(this).hasClass('loading'))
		{
			if(confirm($(this).data('confirm')))
			{
				etsTransChatGpt.clearDataChat(this, function (res) {
					if(res.success) {
						etsTransFunc.showSuccessMessage(res.message);
						// chatgpt-history-list
						$(etsTransChatGpt.id_chat_box_ele + ' #chatgpt-history-list').html('');
						$(_that).addClass('hide');
					} else
						etsTransFunc.showErrorMessage(res.message)
				})
			}
		}
		return false;
	});

	$(document).on('click','.ets_trans_popup .close_popup,.ets_trans_popup .cancel_popup',function(e){
		e.preventDefault();
		$('.ets_trans_popup').removeClass('show');
	});

	$(document).on('change', '#form_switch_language', function () {
		var isoCode = $(this).val();
		etsTransChatGpt.formSwitchLang(isoCode)
	});




	// for old ui box chat gpt
	$(document).on('click', '.js-ets-trans-btn-submit-chatgpt', function () {
		var prompt = $(this).closest('.ets-trans-chatgpt-field-boundary').find('.js-ets-trans-textarea-chatgpt').val();
		if (!prompt) {
			etsTransFunc.showErrorMessage(etsTransFunc.trans('empty_prompt_chatgpt'));
			return false
		}
		var _that = this
		var field = $(this).closest('.js-ets-trans-form-chatgpt').attr('data-field');
		var source_lang = $(this).closest('.ets-trans-chatgpt-field-boundary').find('.translationsFields .translation-field.active').attr('data-locale');

		var formData = {
			prompt,
			field,
			source_lang,
			model: '',
			page_id: etsTransPageId || 0,
			col_data: etsTranslateDefine.getColData('product'),
		};
		$.ajax({
			url: ETS_TRANS_LINK_AJAX,
			type: 'POST',
			data: {
				etsTransSubmitChatGPT: 1,
				formData
			},
			dataType: 'json',
			beforeSend: function () {
				$(_that).addClass('loading');
				$(_that).prop('disabled', true);
				$(_that).siblings('.js-ets-trans-btn-cancel-chatgpt').addClass('loading');
				$(_that).siblings('.js-ets-trans-btn-cancel-chatgpt').prop('disabled', true);
			},
			success: function (res) {
				if(res.success){
					//do add data to content
					var idLang = res.id_lang;
					etsTransFunc.showSuccessMessage(res.message);
					if ($('#' + field + idLang).hasClass('js-taggable-field') || ($('#' + field + idLang).length && $('#' + field + idLang).attr('name').indexOf('keywords') !== -1)) {
						$('#' + field + idLang).val(res.data.replace(/\|/g, ','));
						if(!$('#' + field + idLang).parent().hasClass('ets-trans-field-translated-success')){
							$('#' + field + idLang).parent().addClass('ets-trans-field-translated-success');
						}
						$('#' + field + idLang).change();
						if ($('#' + field + idLang).hasClass('tagify')) {
							etsTranslateDefine.addKeywords('#' + field + idLang);
						}
					} else {
						$('#' + field + idLang).val(res.data);
						if(!$('#' + field + idLang).hasClass('ets-trans-field-translated-success')){
							$('#' + field + idLang).addClass('ets-trans-field-translated-success');
						}
					}
					if (typeof tinyMCE !== "undefined" && $('#' + field + idLang).hasClass('autoload_rte')) {
						$('#' + field + idLang).prev().find('iframe').next('div').hide();
						tinyMCE.get(field + idLang).setContent(res.data);
						if(!$('#' + field + idLang).parent().hasClass('ets-trans-field-translated-success')){
							$('#' + field + idLang).parent().addClass('ets-trans-field-translated-success');
						}
					}
				}
				else{
					var errorMessage = res.errors || res.message;
					etsTransFunc.showErrorMessage(errorMessage);
				}
			},
			complete: function () {
				$(_that).removeClass('loading');
				$(_that).prop('disabled', false);
				$(_that).siblings('.js-ets-trans-btn-cancel-chatgpt').removeClass('loading');
				$(_that).siblings('.js-ets-trans-btn-cancel-chatgpt').prop('disabled', false);
			}
		});
		return false;
	});

	$(document).on('click', '.js-ets-trans-btn-cancel-chatgpt', function () {
		etsRefreshFormChatGPT(this)
	});







	// button click open form chat gpt
	$(document).on('click', '.js-ets-trans-btn-trans-chatgpt-field-item', function (e) {
		e.preventDefault();
		if ($(etsTransChatGpt.id_chat_box_ele).length){
			$(etsTransChatGpt.id_chat_box_ele).show();
			$(etsTransChatGpt.id_chat_box_ele).attr('data-apply-for', $(this).attr('data-field'));
			etsTransChatGpt.scrollBottom(etsTransChatGpt.id_chat_box_ele  + ' .form-wrapper');
			etsTransChatGpt.focusMessageBox();
			return false;
		}
		var that = this;
		etsTransChatGpt.getForm(this, function (res) {
			if(res.success){
				if ($(etsTransChatGpt.id_chat_box_ele).length)
					$(etsTransChatGpt.id_chat_box_ele).remove();

				if($('#main-div').length){
					$('#main-div').append(res.form);
				}
				else
					$('body').append(res.form);
				etsTransChatGpt.openForm(that);
				etsTransChatGpt.draggable();
				etsTransChatGpt.refreshHeightBoxChatGPT();
			}
			else{
				var errorMessage = res.errors || res.message;
				etsTransFunc.showErrorMessage(errorMessage);
			}
		})
		return false;
	});
});