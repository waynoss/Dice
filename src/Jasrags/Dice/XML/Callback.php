<?php
namespace Jasrags\Dice\XML;

/**
 * Class Callback
 * @package Jasrags\Dice\XML
 */
class Callback
{
    /**
     * @var string
     */
    private $str;

    /**
     * @param string $str
     */
    public function __construct($str)
    {
        $this->str = $str;
    }

    /**
     * @param Dice $dic
     *
     * @return mixed
     */
    public function create(Dice $dic)
    {
        $parts = explode('::', trim($this->str, '{}'));
        $object = $dic->create(array_shift($parts));
        while ($var = array_shift($parts)) {
            if (strpos($var, '(') !== false) {
                $args = explode(',', substr($var, strpos($var, '(') + 1, strpos($var, ')') - strpos($var, '(') - 1));
                $object = call_user_func_array(array($object, substr($var, 0, strpos($var, '('))), ($args[0] == null) ? array() : $args);
            } else {
                $object = $object->$var;
            }
        }

        return $object;
    }
}
