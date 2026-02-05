{*
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
*}
{literal}
	<table class="table table-mail" style="width:100%;margin-left: auto;margin-right:auto;margin-top:10px;background-color: #ffffff;">
		<tr>
			<td align="left" style="padding:7px 0">
				<table class="table" style="width:100%">
					<tr>
						<td align="center" class="logo" style="padding:7px 0">
							<a title="{shop_name}" href="{shop_url}" style="color: #337ff1;">
								<span>{shop_logo}</span>
							</a>
						</td>
					</tr>
					<tr>
						<td class="space_footer" style="padding:0!important">&nbsp;</td>
					</tr>
					<tr>
						<td class="box" style="background-color:#fff;padding:7px 0;border-radius:3px;">
							<table class="table" style="width:100%">
								<tr>
									<td style="padding:7px 0;text-align: left;">
										{message_content}
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr>
						<td class="space_footer" style="padding:0!important">&nbsp;</td>
					</tr>
					<tr>
						<td class="footer" style="padding:7px 0;text-align: center">
							<span>Copyright <a href="{shop_url}" >{shop_name}</a> All rights reserved</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
{/literal}