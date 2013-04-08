<?php
namespace Jasrags\Dice;

/**
 * Class Instance
 * @package Jasrags\Dice
 */
class Instance
{
    public $name;

    public function __construct($instance)
    {
        $this->name = $instance;
    }
}
