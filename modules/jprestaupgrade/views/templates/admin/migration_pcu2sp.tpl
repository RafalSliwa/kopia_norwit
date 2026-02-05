{*
* Upgrade module powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
*               is permitted for one Prestashop instance only but you can install it on your test instances.
*}
<script type="text/javascript">
    function getNextStep() {
        return $('.js-step[data-state={JprestaMigPCU2SPStep::STATE_TO_VALIDATE|intval}],.js-step[data-state={JprestaMigPCU2SPStep::STATE_TO_VALIDATE_AGAIN|intval}]').first().data('id');
    }
    function runStep(stepId) {
        console.log("Running step '" + stepId + "'");
        $('.js-step[data-id=' + stepId + ']').addClass('loading');
        try {
            $.ajax({ url: '{$stepUrl|escape:'javascript':'UTF-8'}', method: 'post', data: { step: stepId },
                success: function(response) {
                    if (typeof response === "object") {
                        if (typeof response.sp_link === 'string') {
                            $('#successBtn').attr('href', response.sp_link);
                        }
                        if (response.success) {
                            $('.js-step[data-id=' + stepId + ']').replaceWith(response.html);
                        } else {
                            $('.js-step[data-id=' + stepId + ']').attr('data-state', {JprestaMigPCU2SPStep::STATE_ERROR|intval});
                            $('.js-step[data-id=' + stepId + '] .message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                    if (typeof response.html === "string") {
                        $('.js-step[data-id=' + stepId + ']').replaceWith(response.html);
                    }
                    else {
                        console.error('Cannot read response', response);
                    }
                },
                error: function(result, status, error) {
                    console.log(result + ' - ' + status + ' - ' + error);
                },
                complete: function(jqXHR, textStatus) {
                    onStepStop(stepId);
                }
            });
        }
        catch (e) {
            console.log(e);
            onStepStop(stepId);
        }
    }
    function onStepStop(stepId) {
        let $step = $('.js-step[data-id=' + stepId + ']');
        $step.removeClass('loading');
        console.log("End of step '" + stepId + "'");
        let stepState = parseInt($step.data('state'));
        if (stepState === {JprestaMigPCU2SPStep::STATE_VALIDATED|intval}
            || stepState === {JprestaMigPCU2SPStep::STATE_TO_VALIDATE_AGAIN|intval}) {
            let nextStepId = getNextStep();
            if (typeof nextStepId !== 'undefined') {
                runStep(nextStepId);
            }
            else {
                console.log("No more step to run");
                onMigrateStop();
            }
        }
        else {
            console.log("Last step is in error so migration is stopped");
            onMigrateStop();
        }
    }
    function onMigrateStop() {
        $('#jprestaeasyupgrade').removeClass('migrating');
        $('#migrateBtn').prop("disabled", false).removeClass('loading');
        let toValidateCount = $('.js-step[data-state={JprestaMigPCU2SPStep::STATE_TO_VALIDATE|intval}],.js-step[data-state={JprestaMigPCU2SPStep::STATE_TO_VALIDATE_AGAIN|intval}]').length;
        let inErrorCount = $('.js-step[data-state={JprestaMigPCU2SPStep::STATE_ERROR|intval}]').length;
        let cannotValidateCount = $('.js-step[data-state={JprestaMigPCU2SPStep::STATE_CANNOT_VALIDATE|intval}]').length;
        if (toValidateCount === 0 && inErrorCount === 0 && cannotValidateCount === 0) {
            $('#jprestaeasyupgrade').addClass('migrated');
            $('#migrateBtn').hide();
            $('#successBtn').show();
        }
        else if (inErrorCount > 0) {
            $('#jprestaeasyupgrade').addClass('migrate-error');
            $('#migrateBtn').prop('disabled', true);
        }
        else if (cannotValidateCount > 0) {
            $('#jprestaeasyupgrade').addClass('cannot-migrate');
            $('#migrateBtn').prop('disabled', true);
        }
    }
    function migrate() {
        $('#jprestaeasyupgrade').addClass('migrating');
        $('#migrateBtn').prop('disabled', true).addClass('loading');
        try {
            let nextStepId = getNextStep();
            if (typeof nextStepId !== 'undefined') {
                runStep(nextStepId);
            }
            else {
                onMigrateStop();
            }
        }
        catch (e) {
            console.log(e);
            onMigrateStop();
        }
    }
</script>
<style>
    .hide-when-loading {
        display : block;
    }
    .show-when-loading {
        display : none;
    }
    .loading .hide-when-loading {
        display : none;
    }
    .loading .show-when-loading {
        display : block;
    }
    .show-if-migrated {
        display: none;
    }
    .migrated .show-if-migrated {
        display: block;
    }
    .show-if-cannot-migrate {
        display: none;
    }
    .cannot-migrate .show-if-cannot-migrate {
        display: block;
    }
    .show-if-migrate-error {
        display: none;
    }
    .migrate-error .show-if-migrate-error {
        display: block;
    }
    .step {
        margin-bottom: 1rem;
    }
    .stepTitle {
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    .stepIndex {
        font-size: 26px;
        font-weight: bold;
        padding: 4px 5px;
        color: white;
        background-color: gray;
        border-radius: 36px;
        width: 48px;
        height: 48px;
        text-align: center;
    }
    .stepMain {
        padding: 0 1rem;
        border-left: 1px solid #ccc;
    }
    .stepIcon {
        text-align: center;
        vertical-align: top;
    }
    .stepIndex span {
        color: white;
        background-color: #ccc;
        padding: 0 0.5rem;
        font-size: 1.2rem;
        font-weight: bold;
    }
    .stepState {
        padding: 0 1rem;
        vertical-align: top;
    }
    .stepState{JprestaMigPCU2SPStep::STATE_VALIDATED|intval} .stepIndex span {
        background-color: green;
    }
    .stepState{JprestaMigPCU2SPStep::STATE_ERROR|intval} .stepIndex span {
        background-color: red;
    }
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        color: #fff;
        font-size: 1.5em;
    }
    .loading-message {
        text-align: center;
    }
    #jprestaeasyupgrade:not(.migrating) .loading-overlay {
        display: none;
    }
</style>
<div id="jprestaeasyupgrade">
    <div class="row">
        <div class="col">
            <div style="display: none">
                <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-validated.png" width="48" height="48">
                <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-half-validated.png" width="48" height="48">
                <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-error.png" width="48" height="48">
            </div>

            <div class="loading-overlay">
                <div class="loading-message">
                    <p>
                        <i class="process-icon-loading" style="font-size: 48px; line-height: 48px; width: 48px; height: 48px;"></i>
                        <br/>
                        {l s='Migrating, please keep this browser open and wait...' mod='jprestaupgrade'}
                    </p>
                </div>
            </div>
            <div class="panel">
                <h3><img height="28" src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/migration-pcu2sp.png"
                         alt=""/>&nbsp;{l s='Migration steps' mod='jprestaupgrade'}</h3>
                <div class="alert alert-info">
                    <p>{l s='Here are the steps to migrate your Page Cache Ultimate module to the all-in-one Speed Pack module.' mod='jprestaupgrade'}</p>
                    <p>{l s='We recommend enabling maintenance mode during this process.' mod='jprestaupgrade'}</p>
                </div>
                <div class="alert alert-success show-if-migrated">
                    {l s='CONGRATULATIONS! The migration has completed successfully.' mod='jprestaupgrade'}
                </div>
                <div class="alert alert-warning show-if-cannot-migrate">
                    {l s='One of the migration steps cannot be processed. Please contact support.' mod='jprestaupgrade'}
                </div>
                <div class="alert alert-danger show-if-migrate-error">
                    {l s='An error occurred during the migration. Please try to launch it again. If the error persists, please contact support.' mod='jprestaupgrade'}
                </div>
                {foreach from=$steps item=$step key=index}
                    {include file="./_mig_step.tpl" index=$index+1 step=$step}
                {/foreach}
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="{$backToUpgrade|escape:'html':'UTF-8'}" class="btn btn-default">
                        <i class="process-icon-back"></i>
                        {l s='Back' d='Admin.Global'}
                    </a>
                    <a href="{$goToSP|escape:'html':'UTF-8'}" class="btn btn-success" id="successBtn" style="display: none; text-transform: uppercase;">
                        <img style="display: block; margin: auto" src="{$base_url}modules/jprestaupgrade/views/img/logo-jprestaspeedpack.png" width="28" height="28">
                        {l s='Go to Speed Pack!' mod='jprestaupgrade'}
                    </a>
                    <button type="submit" value="1" id="migrateBtn" class="btn btn-primary" onclick="migrate();">
                        <div class="show-when-loading">
                            <i class="process-icon-loading"></i>&nbsp;{l s='Migrating...' mod='jprestaupgrade'}
                        </div>
                        <div class="hide-when-loading">
                            <i class="process-icon-ok"></i>&nbsp;{l s='Launch the migration!' mod='jprestaupgrade'}
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
