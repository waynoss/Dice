<?php
namespace Jasrags;

use \Jasrags\Dice\XML\Loader;

/**
 * Class DiceXMLLoaderTest
 * @package Jasrags
 */
class DiceXMLLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader
     */
    private $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Loader();
    }

    /**
     * @test
     */
    public function testGetComponent()
    {
        $this->markTestIncomplete('create test for getComponent method');
    }

    /**
     * @test
     */
    public function testLoadXml()
    {
        $dice = new Dice();
        $this->object->loadXml(__DIR__ . '/data/dice.xml', $dice);
        $result = $dice->getRule('Foo');

        $this->assertTrue($result->shared);
        $this->assertEquals('123', $result->constructParams[0]);
        $this->assertEquals('XYZ', $result->constructParams[1]);
        $this->assertEquals('Foo', $result->newInstances[0]);
        $this->assertEquals('Bar', $result->newInstances[1]);
        $this->assertEquals('Baz', $result->shareInstances[0]->name);
        $this->assertEquals('Boo', $result->shareInstances[1]->name);
        $this->assertEquals('Bar', $result->instanceOf);
        $this->assertEquals('BIterator', $result->substitutions['iterator']->name);
        $this->assertEquals('X', $result->substitutions['y']->name);
        $this->assertEquals('setAttribute', $result->call[0][0]);
        $this->assertEquals('Foo', $result->call[0][1][0]);
        $this->assertEquals('Bar', $result->call[0][1][1]);
        $this->assertEquals('setAttribute', $result->call[1][0]);
        $this->assertEquals('Bar', $result->call[1][1][0]);
        $this->assertEquals('Baz', $result->call[1][1][1]);
        $this->assertEquals('setDependency', $result->call[2][0]);
        $this->assertEquals('X', $result->call[2][1][0]->getName());
    }
}
