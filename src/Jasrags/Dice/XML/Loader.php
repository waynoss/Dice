<?php
namespace Jasrags\Dice\XML;

/**
 * Class XmlLoader
 * @package Jasrags\Dice\XML
 */
class XmlLoader
{
    private function getComponent($str, $createInstance = false)
    {
        if ($createInstance) return (strpos((string)$str, '{') === 0) ? array(new DiceXmlCallback($str), 'create') : new Instance((string)$str);
        else return (strpos((string)$str, '{') === 0) ? array(new DiceXmlCallback($str), 'create') : (string)$str;
    }

    public function loadXml($map, Dice $dic)
    {
        if (!($map instanceof SimpleXmlElement)) $map = simplexml_load_file($map);
        $rules = array();
        foreach ($map as $key => $value) {
            $rule = clone $dic->getRule((string)$value->name);
            $rule->shared = ($value->shared == 'true');
            $rule->inherit = ($value->inherit == 'true');
            if ($value->call) {
                foreach ($value->call as $name => $call) {
                    $callArgs = array();
                    if ($call->params) foreach ($call->params->children() as $key => $param) $callArgs[] = $this->getComponent((string)$param, ($key == 'instance'));
                    $rule->call[] = array((string)$call->method, $callArgs);
                }
            }
            if ($value->instanceof) $rule->instanceOf = (string)$value->instanceof;
            if ($value->newinstances) $rule->newInstances = explode(',', $value->newinstances);
            if ($value->substitute) foreach ($value->use as $use) $rule->substitutions[(string)$use->as] = $this->getComponent((string)$use->use, true);
            if ($value->construct) foreach ($value->construct->children() as $child) $rule->constructParams[] = $this->getComponent((string)$child);
            if ($value->shareinstances) foreach ($value->shareinstances as $share) $rule->shareInstances[] = $this->getComponent((string)$share, true);
            $dic->addRule((string)$value->name, $rule);
        }
    }
}
