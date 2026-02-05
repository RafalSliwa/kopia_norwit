<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

abstract class JprestaMigPCU2SPStep
{
    const STATE_INIT = -1;
    const STATE_ERROR = 0;
    const STATE_VALIDATED = 1;
    const STATE_TO_VALIDATE = 2;
    const STATE_TO_VALIDATE_AGAIN = 3;
    const STATE_CANNOT_VALIDATE = 4;

    /**
     * @var string Identifier
     */
    var $id;

    /**
     * @var string A name for the user
     */
    var $name;

    /**
     * @var string the icon path of the step
     */
    var $icon;

    /**
     * @var int State of the step
     */
    var $state;

    /**
     * @var string What this step will do and why
     */
    var $description;

    var $errors = [];
    var $confirmations = [];

    /**
     * @param string $id
     * @param string $name
     * @param string $icon
     * @param string $description
     */
    public function __construct($id, $name, $icon, $description)
    {
        $this->id = $id;
        $this->state = self::STATE_INIT;
        $this->name = $name;
        $this->icon = $icon;
        $this->description = $description;
    }

    public abstract function isRequired();

    public abstract function init();

    public abstract function run();
}
