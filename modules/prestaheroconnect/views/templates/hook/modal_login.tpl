{*
* 2007-2024 PrestaHero
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
* versions in the future. If you wish to customize PrestaShop for your
* needs please, contact us for extra customization service at an affordable price
*
*  @author PrestaHero <etssoft.jsc@gmail.com>
*  @copyright  2007-2024 PrestaHero
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of PrestaHero
*}

<div class="modal fade" tabindex="-1" role="dialog" id="phConLoginAddons">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        title="{l s='Close' mod='prestaheroconnect'}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{l s='Connect to PrestaHero' mod='prestaheroconnect'}</h4>
            </div>
            <div class="modal-body">
                <div class="errors"></div>
                <div class="alert alert-info">
                    {l s='Login using the account you have registered on the website' mod='prestaheroconnect'}<a
                            href="https://prestahero.com/" target="_blank" style="padding: 0px 5px;">PrestaHero.com</a>
                </div>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>{l s='Email' mod='prestaheroconnect'}</label>
                        <input type="email" name="ph_email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{l s='Password' mod='prestaheroconnect'}</label>
                        <input type="password" name="ph_password" required class="form-control">
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked="checked" name="remember_me" value="1">
                                {l s='Remember me' mod='prestaheroconnect'}
                            </label>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary js-ph-con-submit-account-addons">{l s='Login' mod='prestaheroconnect'}</button>
                        <div class="opc_social_form col-xs-12 col-sm-12">
                            <div class="opc_solo_or">
                                <span>{l s='OR log in with' mod='prestaheroconnect'}</span>
                            </div>
                            <ul class="opc_social">
                                <li class="opc_social_item google active" data-auth="Google" title="{l s='Sign in with Google' mod='prestaheroconnect'}">
                                <span class="opc_social_btn medium rounded custom">
                                    <i class="ets_svg_icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18"
                                             height="18"> <path fill="#FFC107"
                                                                d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path> <path
                                                    fill="#FF3D00"
                                                    d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path> <path
                                                    fill="#4CAF50"
                                                    d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path> <path
                                                    fill="#1976D2"
                                                    d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path> </svg>
                                    </i>
                                    Google
                                </span>
                                </li>
                                <li class="opc_social_item facebook active" data-auth="Facebook"
                                    title="{l s='Sign in with Facebook' mod='prestaheroconnect'}">
                                <span class="opc_social_btn medium rounded custom">
                                    <i class="ets_svg_icon">
                                        <svg width="16" height="16" viewBox="0 0 1792 1792"
                                             xmlns="http://www.w3.org/2000/svg"><path
                                                    d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"></path></svg>
                                    </i>
                                    Facebook
                                </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group form-group_action_pass_acc text-center">
                        <a class="forgot_password" href="https://prestahero.com/en/password-recovery"
                           target="_blank">{l s='Forgot your password?' mod='prestaheroconnect'}</a>
                        <a class="create_account" href="https://prestahero.com/en/login?create_account=1"
                           target="_blank">{l s='Create account' mod='prestaheroconnect'}</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>