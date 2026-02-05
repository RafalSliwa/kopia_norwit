{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<ul class="header-list quotation-notifier component" style="display:none;">
    <li id="quotation_notification" class="dropdown">
        <a href="javascript:void(0);" class="notification dropdown-toggle notifs" style="padding: 0 !important;">
            <i class="material-icons">view_list</i>
            <span id="total_quotation_notif_number_wrapper" class="notifs_badge hide">
                <span id="total_quotation_notif_value">0</span>
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-right notifs_dropdown">
            <div class="notifications">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" data-toggle="tab" data-type="order" href="#quotation-notifications" role="tab" id="orders-tab">{l s='Quotation Requests' mod='roja45quotationspro'}<span id="quotation_notif_value"></span></a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane empty active" id="quotation-notifications" role="tabpanel">
                        <div class="notification-elements"></div>
                    </div>
                </div>
            </div>
        </div>
    </li><div id="gamification_notif" class="notifs"></div>
</ul>
