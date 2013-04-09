<?php
namespace Jasrags\Dice;

/**
 * Class Instance
 * @package Jasrags\Dice
 */
class Instance
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $instance
     */
    public function __construct($instance)
    {
        $this->name = $instance;
    }
}
