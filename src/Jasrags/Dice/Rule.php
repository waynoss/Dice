<?php
namespace Jasrags\Dice;

/**
 * Class Rule
 * @package Jasrags\Dice
 */
class Rule
{
    /**
     * @var bool
     */
    public $shared = false;

    /**
     * @var array
     */
    public $constructParams = array();

    /**
     * @var array
     */
    public $substitutions = array();

    /**
     * @var array
     */
    public $newInstances = array();

    /**
     * @var string
     */
    public $instanceOf;

    /**
     * @var array
     */
    public $call = array();

    /**
     * @var bool
     */
    public $inherit = true;

    /**
     * @var array
     */
    public $shareInstances = array();
}
