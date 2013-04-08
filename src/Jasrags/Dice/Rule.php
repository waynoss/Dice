<?php
namespace Jasrags\Dice;

/**
 * Class Rule
 * @package Jasrags\Dice
 */
class Rule
{
    public $shared = false;
    public $constructParams = array();
    public $substitutions = array();
    public $newInstances = array();
    public $instanceOf;
    public $call = array();
    public $inherit = true;
    public $shareInstances = array();
}
