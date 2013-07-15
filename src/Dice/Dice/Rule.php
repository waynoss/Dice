<?php
namespace ATNWebServiceClient\SolaCore\DependencyInjection\Dice;

/**
 * Class Rule
 * @package DependencyInjection\Dice
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

    /**
     * @param boolean $shared
     * @return $this
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * @param boolean $inherit
     * @return $this
     */
    public function setInherit($inherit)
    {
        $this->inherit = $inherit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInherit()
    {
        return $this->inherit;
    }

    /**
     * @param array $constructParams
     * @return $this
     */
    public function setConstructParams(array $constructParams)
    {
        $this->constructParams = $constructParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getConstructParams()
    {
        return $this->constructParams;
    }

    /**
     * @param array $substitutions
     * @return $this
     */
    public function setSubstitutions(array $substitutions)
    {
        $this->substitutions = $substitutions;

        return $this;
    }

    /**
     * @return array
     */
    public function getSubstitutions()
    {
        return $this->substitutions;
    }

    /**
     * @param string $class
     * @param mixed $instance
     * @return $this
     */
    public function addSubstitution($class, $instance)
    {
        $this->substitutions[$class] = $instance;

        return $this;
    }

    /**
     * @param string $instanceOf
     * @return $this
     */
    public function setInstanceOf($instanceOf)
    {
        $this->instanceOf = $instanceOf;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstanceOf()
    {
        return $this->instanceOf;
    }

    /**
     * @param array $newInstances
     * @return $this
     */
    public function setNewInstances(array $newInstances)
    {
        $this->newInstances = $newInstances;

        return $this;
    }

    /**
     * @return array
     */
    public function getNewInstances()
    {
        return $this->newInstances;
    }

    /**
     * @param array $shareInstances
     * @return $this
     */
    public function setShareInstances(array $shareInstances)
    {
        $this->shareInstances = $shareInstances;

        return $this;
    }

    /**
     * @return array
     */
    public function getShareInstances()
    {
        return $this->shareInstances;
    }

    /**
     * @param array $call
     * @return $this
     */
    public function setCall(array $call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return array
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param string $method
     * @param array $args
     * @return $this
     */
    public function addCall($method, array $args)
    {
        $this->substitutions[] = array($method, $args);

        return $this;
    }
}
