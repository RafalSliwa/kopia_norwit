<div id="step{$index|intval}" class="js-step step stepState{$step->state|intval}" data-id="{$step->id|escape:'html':'UTF-8'}" data-state="{$step->state|intval}">
<table>
    <tr>
        <td class="stepState">
            <div class="hide-when-loading">
                {if $step->state == JprestaMigPCU2SPStep::STATE_VALIDATED}
                    <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-validated.png" width="48" height="48">
                {elseif $step->state == JprestaMigPCU2SPStep::STATE_TO_VALIDATE_AGAIN}
                    <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-half-validated.png" width="48" height="48">
                {elseif $step->state == JprestaMigPCU2SPStep::STATE_TO_VALIDATE}
                    <div class="stepIndex">{$index|intval}</div>
                {elseif $step->state == JprestaMigPCU2SPStep::STATE_CANNOT_VALIDATE}
                    <div class="stepIndex">{$index|intval}</div>
                {elseif $step->state == JprestaMigPCU2SPStep::STATE_ERROR}
                    <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/icon-error.png" width="48" height="48">
                {elseif $step->state == JprestaMigPCU2SPStep::STATE_INIT}
                    <div class="stepIndex">{$index|intval}</div>
                {/if}
            </div>
            <div class="show-when-loading">
                <i class="process-icon-loading" style="font-size: 48px; line-height: 48px; width: 48px; height: 48px;"></i>
            </div>
        </td>
        <td class="stepMain">
            <div class="stepTitle"><img src="{$step->icon|escape:'html':'UTF-8'}" width="28" height="28"> {$step->name|escape:'javascript':'UTF-8'}</div>
            <p>{$step->description|escape:'html':'UTF-8'}</p>
            <div class="message">
                {foreach $step->errors as $error}
                    <div class="alert alert-danger">{$error|escape:'html':'UTF-8'}</div>
                {/foreach}
                {foreach $step->confirmations as $confirmation}
                    <div class="alert alert-success">{$confirmation|escape:'html':'UTF-8'}</div>
                {/foreach}
            </div>
        </td>
    </tr>
</table>
</div>
