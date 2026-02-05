<style>
	p.payment_module a.leasenow_payment {
		background:no-repeat scroll 18px 33px #FBFBFB url('{$leasenow_img|escape:'html':'UTF-8'}');
		background-size:70px 20px;
	}

	p.payment_module a.leasenow_payment:hover {
		background-color:#f6f6f6;
	}

	p.payment_module a.leasenow_payment:after {
		display:block;
		content:"\f054";
		position:absolute;
		right:15px;
		margin-top:-11px;
		top:50%;
		font-family:"FontAwesome";
		font-size:25px;
		height:22px;
		width:14px;
		color:#777777;
	}

	#loading-gif {
		display:block;
		margin-left:auto;
		margin-right:auto;
	}
</style>

<div id="leasenow_loading" class="hidden"
     style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:50000; background: rgba(0,0,0,.5)">
	<div style="background: #fbfbfb none; border: 1px solid #d6d4d4; border-radius: 4px; transform:translate(-50%, -50%); position:absolute; top:50%; left:50%; color:black;padding:20px;">
		<img id="loading-gif"
		     src="{$leasenow_loading_gif}" alt="Loading"/><br/>
        {$leasenow_redirect_hint}
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('.leasenow_payment').on('click', function (e) {
			document.getElementById('leasenow_loading').classList.remove("hidden");
		})
	});
</script>

<p class="payment_module">
	<a class="leasenow_payment"
	   href="{$leasenow_payment_url}"
	   title="{$leasenow_payment_title}">
		<img style="display:none"
		     src="{$leasenow_img|escape:'html':'UTF-8'}"
		     alt="{$leasenow_payment_title}"/>
        {$leasenow_payment_title}
        {if $leasenow_sandbox == 1}
			<span style="color:red">
				{l s='Sandbox mode is enabled.' mod='leasenow'}
			</span>
        {/if}
	</a>
</p>
