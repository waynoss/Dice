<?php
namespace ATNWebServiceClient\SolaCore\DependencyInjection;

use ATNWebServiceClient\SolaCore;
use ATNWebServiceClient\SolaCore\DependencyInjection\Dice\Rule;
use ATNWebServiceClient\SolaCore\DependencyInjection\Dice\Instance;

/**
 * Class Dice
 * @description     Dice - A minimal Dependency Injection Container for PHP
 * @author          Tom Butler tom@r.je
 * @copyright       2012-2013 Tom Butler <tom@r.je>
 * @link            http://r.je/dice.html
 * @license         http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version         1.0
 *
 * @author          Jason Ragsdale
 *
 * @author          Wayne David Harris
 *
 */
class Dices
{
    /**
     * @var array
     */
    private static $rules = array();

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * @param $object
     */
    public static function assign($object)
    {
        self::$instances[strtolower(get_class($object))] = $object;
    }

    /**
     * @param string $name
     * @param Rule $rule
     */
    public static function addRule($name, Rule $rule)
    {
        $rule->substitutions = array_change_key_case($rule->substitutions);
        self::$rules[strtolower($name)] = $rule;
    }

    /**
     * @param string $name
     *
     * @return Rule
     */
    public static function getRule($name)
    {
        if (isset(self::$rules[strtolower($name)])) {
            return self::$rules[strtolower($name)];
        }
        foreach (self::$rules as $key => $value) {
            if ($key !== '*' && is_subclass_of($name, $key) && $value->inherit == true) {
                return $value;
            }
        }

        return isset(self::$rules['*']) ? self::$rules['*'] : new Rule;
    }

    /**
     * wdh
     *
     * @param string $component
     * @param array $rules
     * @param array $args
     * @param null $callback
     * @param bool $forceNewInstance
     *
     * @return object
     */
    public static function setInstance($component, array $params = array(), array $rules = array(), $callback = null, $forceNewInstance = false)
    {
        $rule = self::setRules($params,$rules);

        self::addRule($component, $rule);

        return self::create($component, array(), $callback, $forceNewInstance);

    }
    /**
     * wdh
     * eg $app = Dices::setSharedInstance( __NAMESPACE__.'\App', array( param1, param2 )  );
     *
     * @param string $component
     * @param array $rules
     * @param array $args
     * @param null $callback
     * @param bool $forceNewInstance
     *
     * @return object
     */
    public static function setSharedInstance($component, array $params = array(), array $rules = array(), $callback = null, $forceNewInstance = false)
    {

        $rule = self::setRules($params,$rules);

        $rule->setShared(true);

        self::addRule($component, $rule);

        return self::create($component, array(), $callback, $forceNewInstance);

    }
    /**
     * wdh
     *
     * @param array $rules
     *
     * @return object
     */
    public static function setRules( array $params=array(), array $rules = array() )
    {
        $rule = new Rule;

        foreach( $rules as $key=>$value) {
            $rule->$key = $value;
        }

        $rule->setConstructParams($params);

        return $rule;
    }
    /**
     * @param string $component
     * @param array $args
     * @param null $callback
     * @param bool $forceNewInstance
     *
     * @return object
     * @throws \Exception
     */
    public static function create($component, array $args = array(), $callback = null, $forceNewInstance = false)
    {
        if ($component instanceof Instance) {
            $component = $component->getName();
        }

        if (!isset(self::$rules[strtolower($component)]) && !class_exists($component)) {
            throw new \Exception('Class does not exist for creation: ' . $component);
        }

        if (!$forceNewInstance && isset(self::$instances[strtolower($component)])) {
            return self::$instances[strtolower($component)];
        }

        $rule = self::getRule($component);
        $className = (!empty($rule->instanceOf)) ? strtolower($rule->instanceOf) : $component;
        $share = self::getParams($rule->shareInstances);

        $params = self::getMethodParams($className, '__construct', $rule, array_merge($share, $args, self::getParams($rule->constructParams)), $share);

        if (is_callable($callback, true)) {
            call_user_func_array($callback, array($params));
        }

        $reflection = new \ReflectionClass($className);
        $object = (count($params) > 0) ? $reflection->newInstanceArgs($params) : $object = new $className;

        if ($rule->shared == true) {
            self::$instances[strtolower($component)] = $object;
        }

        foreach ($rule->call as $call) {
            call_user_func_array(array($object, $call[0]), self::getMethodParams($className, $call[0], $rule, array_merge(self::getParams($call[1]), $args)));
        }

        return $object;
    }

    /**
     * @param array $params
     * @param array $newInstances
     *
     * @return array
     */
    private function getParams(array $params = array(), array $newInstances = array())
    {
        for ($i = 0; $i < count($params); $i++) {
            if ($params[$i] instanceof Instance) {
                $params[$i] = self::create($params[$i]->getName(), array(), null, in_array(strtolower($params[$i]->getName()), array_map('strtolower', $newInstances)));
            } else {
                $params[$i] = (!(is_array($params[$i]) && isset($params[$i][0]) && is_string($params[$i][0])) && is_callable($params[$i])) ? call_user_func($params[$i], $this) : $params[$i];
            }
        }

        return $params;
    }

    /**
     * @param string $className
     * @param string $method
     * @param Rule $rule
     * @param array $args
     * @param array $share
     *
     * @return array
     */
    private function getMethodParams($className, $method, Rule $rule, array $args = array(), array $share = array())
    {
        if (!method_exists($className, $method)) {
            return array();
        }
        $reflectionMethod = new \ReflectionMethod($className, $method);
        $params = $reflectionMethod->getParameters();
        $parameters = array();
        foreach ($params as $param) {
            foreach ($args as $argName => $arg) {
                $class = $param->getClass();
                if ($class && is_object($arg) && $arg instanceof $class->name) {
                    $parameters[] = $arg;
                    unset($args[$argName]);
                    continue 2;
                }
            }

            $paramClassName = $param->getClass() ? strtolower($param->getClass()->name) : false;
            if ($paramClassName && isset($rule->substitutions[$paramClassName])) {
                $parameters[] = is_string($rule->substitutions[$paramClassName]) ? new Instance($rule->substitutions[$paramClassName]) : $rule->substitutions[$paramClassName];
            } else if ($paramClassName && class_exists($paramClassName)) {
                $parameters[] = self::create($paramClassName, $share, null, in_array($paramClassName, array_map('strtolower', $rule->newInstances)));
            } else if (is_array($args) && count($args) > 0) {
                $parameters[] = array_shift($args);
            } else {
                $parameters[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
        }

        return self::getParams($parameters, $rule->newInstances);
    }
}
