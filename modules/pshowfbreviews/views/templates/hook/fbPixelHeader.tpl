<!-- Facebook Pixel Code -->
<script>
{literal}
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{/literal}{Configuration::get("PSHOW_FBREVIEWS_FBPIXEL_ID")}{literal}');
{/literal}
</script>
<noscript>
<img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={Configuration::get("PSHOW_FBREVIEWS_FBPIXEL_ID")}&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
{if version_compare(_PS_VERSION_, '1.7.0', '>=') !== false && (int)Configuration::get("PSHOW_FBREVIEWS_FBPIXEL_SEND_METHOD") > 0}
<script>
{literal}
document.addEventListener('DOMContentLoaded', function() {
	var controller_url = "{/literal}{$link->getModuleLink('pshowfbreviews', 'ajaxloader', array())}{literal}";
	function sendAddToCartPixel(data) {
		$.ajax({
			url: controller_url,
	 		method: 'post',
 			async: true,
 			dataType: 'json',
 			data: {
				ajax: true,
				id_product: data.id_product,
				id_product_attribute: data.id_product_attribute,
				id_customization: data.id_customization,
				action: "addToCartPixel"
			},
			success: function (json) {
				if(typeof json !== 'undefined') {
					fbq(
		                'track',
		                json.type,
		                json.content,
		                json.event_data
		            );
				}
 			},
 			error: function (data) {
 			
 			}
		});
	}
	if (typeof prestashop !== 'undefined') {
	  prestashop.on(
	    'updateCart',
	    function (event) {
	      sendAddToCartPixel(event.resp);
	    }
	  );
	}
}, false);
{/literal}
</script>
{/if}