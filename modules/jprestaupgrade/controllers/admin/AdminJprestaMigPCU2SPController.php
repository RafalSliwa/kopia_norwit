<?php
/**
 * Upgrade module powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class AdminJprestaMigPCU2SPController extends ModuleAdminController
{
    /**
     * @var JprestaMigPCU2SPStep[]
     */
    var $steps = [];

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->template = 'migration_pcu2sp.tpl';
    }

    public function init() {
        parent::init();

        $infosMigPagecache = JprestaUpgradeApi::getModuleOrThemeInfosByName('pagecache');
        if ($infosMigPagecache && $infosMigPagecache['license']['is_migration_pcu2sp']) {
            foreach ($infosMigPagecache['license']['downloads_pcu2sp'] as $moduleName => $downloadInfos) {
                $step = new JprestaMigPCU2SPStepUpgrade($this->module, $moduleName, $downloadInfos);
                if ($step->isRequired()) {
                    $step->init();
                    $this->steps[] = $step;
                }
            }
        }
        $step = new JprestaMigPCU2SPStepUninstall($this->module, 'pagecache');
        if ($step->isRequired()) {
            $step->init();
            $this->steps[] = $step;
        }
        $step = new JprestaMigPCU2SPStepUninstall($this->module, 'jprestawebp');
        if ($step->isRequired()) {
            $step->init();
            $this->steps[] = $step;
        }
        $step = new JprestaMigPCU2SPStepUninstall($this->module, 'jprestasqlprofiler');
        if ($step->isRequired()) {
            $step->init();
            $this->steps[] = $step;
        }
        $infosMigSpeedpack = JprestaUpgradeApi::getLicenseInfos('jprestaspeedpack');
        if ($infosMigSpeedpack) {
            $step = new JprestaMigPCU2SPStepInstall($this->module, 'jprestaspeedpack', $infosMigSpeedpack['download']);
            if ($step->isRequired()) {
                $step->init();
                $this->steps[] = $step;
            }
        }
    }

    private function getStep($stepId) {
        foreach ($this->steps as $index => $step) {
            if ($step->id === $stepId) {
                return [$index + 1, $step];
            }
        }
        return [-1, null];
    }

    public function postProcess()
    {
        // Some modules (like MyPresta.eu) add some advert messages
        $this->warnings = [];
        $this->errors = [];
        $this->confirmations = [];

        $stepId = Tools::getValue('step');
        if ($stepId) {
            header('Content-Type: application/json');
            $this->ajax = true;
            $this->json = true;
            list($index, $step) = $this->getStep($stepId);
            if ($step instanceof JprestaMigPCU2SPStep) {
                $retry = $step->run($this);
                $infos = array();
                $infos['step'] = $step;
                $infos['index'] = $index;
                $infos['module_name'] = $this->module->name;
                $this->context->smarty->assign($infos);
                $this->ajaxRender(
                    json_encode([
                        'success' => $step->state !== JprestaMigPCU2SPStep::STATE_ERROR,
                        'state' => $step->state,
                        'retry' => $retry,
                        'sp_link' => $this->context->link->getAdminLink('AdminPageCacheConfiguration'),
                        'message' => '',
                        'html' =>$this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_mig_step.tpl')
                    ])
                );
                exit;
            }
            else {
                $this->ajaxRender(
                    json_encode([
                        'success' => false,
                        'message' => 'Step not found: ' . $stepId
                    ])
                );
                exit;
            }
        }

        return true;
    }

    public function renderList()
    {
        // Variable for smarty
        $infos = array();
        $infos['module_name'] = $this->module->name;
        $infos['steps'] = $this->steps;
        $infos['stepUrl'] = $this->context->link->getAdminLink('AdminJprestaMigPCU2SP');
        $infos['backToUpgrade'] = $this->context->link->getAdminLink('AdminJprestaUpgrade');
        $infos['goToSP'] = $this->context->link->getAdminLink('AdminPageCacheConfiguration');

        $this->context->smarty->assign($infos);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/migration_pcu2sp.tpl');
    }
}
