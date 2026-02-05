<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class JprestaMigPCU2SPStepTest extends JprestaMigPCU2SPStep
{

    public function __construct($upgradeModule, $id = 'steptest')
    {
        $this->module = $upgradeModule;
        parent::__construct(
            $id,
             "Test $id",
            '../modules/'.$upgradeModule->name.'/views/img/test.png',
            $upgradeModule->l('This is a test step', 'jprestamigpcu2spsteptest')
        );
    }

    public function isRequired()
    {
        return true;
    }

    public function init()
    {
        $this->state = parent::STATE_TO_VALIDATE;
    }

    public function run()
    {
        //$this->errors[] = 'Oops!';
        sleep(1);
        $this->state = parent::STATE_VALIDATED;
    }
}
