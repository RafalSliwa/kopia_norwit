{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    ING Lease Now
 *  @copyright 2022-now ING Lease Now
 *  @license   GNU General Public License
 */
*}

<script type="text/javascript">
	(function (i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r;
		i[r] = i[r] || function () {
			(i[r].q = i[r].q || []).push(arguments)
		}, i[r].l = 1 * new Date();
		a = s.createElement(o),
			m = s.getElementsByTagName(o)[0];
		a.async = 1;
		a.src = g;
		m.parentNode.insertBefore(a, m)
	})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

	ga('create', '{$ga_key|escape:'htmlall':'UTF-8'}', 'auto');
	ga('send', 'pageview');

    {if isset($ga_conversion) && $ga_conversion}

	ga('require', 'ecommerce');

    {foreach $ga_conversion as $ga_item}
	ga('{$ga_item.command|escape:'htmlall':'UTF-8'}', {
        {foreach $ga_item.properties as $key => $prop}
		'{$key|escape:'htmlall':'UTF-8'}': '{$prop|escape:'htmlall':'UTF-8'}',
        {/foreach}
	});
    {/foreach}

	ga('ecommerce:send');
    {/if}
</script>
