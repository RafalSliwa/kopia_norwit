<div class="mt-2">
    <!-- Nav tabs -->
    <ul class="nav nav nav-tabs d-print-none " role="tablist">
        <li class="nav-item">
            <a class="nav-link active show" href="#details" role="tab" data-toggle="tab">eHP - status wniosku kredytowego</a>
        </li>
        <li class="nav-item">
            <a class="nav-link show" href="#ehplog" role="tab" data-toggle="tab">eHP log</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="details">{include file='./ehpDisplayDetails.tpl'}</div>
        <div class="tab-pane" id="ehplog">{include file='./ehpDisplayLog.tpl'}</div>        
    </div>
</div>