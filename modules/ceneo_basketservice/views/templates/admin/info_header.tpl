{**
 * @author    Ceneo.
 * @copyright 2024 Ceneo.
 *}
<div class="alert alert-info">
	<p>{l s='For the Buy It Now service to be supported by the plugin, please enter your API key below.' mod='ceneo_basketservice'}
		{l s='You will find it in the Ceneo\'s Panel' mod='ceneo_basketservice'}
		<a target="_blank" href="https://shops.ceneo.pl/WebApi/WebApiAccount#tag=ps">
			 {l s='API Tab' mod='ceneo_basketservice'}
		</a>.
	</p>
	<p>
		<a target="_blank" href="{$moduleLink|escape:'htmlall':'UTF-8'}?token={$secureKey|escape:'htmlall':'UTF-8'}&show_output=1"
		   class="btn btn-success">
          {l s='Download orders' mod='ceneo_basketservice'}
		</a>
	</p>
</div>

<div class="alert alert-warning">
	<p>
       {l s='Remember! Orders from Ceneo will not be downloaded automatically,' mod='ceneo_basketservice'}
       {l s='for this to happen you have to change the cron settings in the server panel.' mod='ceneo_basketservice'}
	</p>
	<p>
       {l s='paste this link' mod='ceneo_basketservice'}
	<code>{$moduleLink|escape:'htmlall':'UTF-8'}?token={$secureKey|escape:'htmlall':'UTF-8'}&show_output=0</code>
       {l s='into the cron job' mod='ceneo_basketservice'}
	</p>
</div>
