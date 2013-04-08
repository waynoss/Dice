<?php
namespace Jasrags;

use \Jasrags\Dice\Instance;

/**
 * Class DiceInstanceTest
 * @package Jasrags
 */
class DiceInstanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Instance
     */
    private $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Instance('instance');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertEquals('instance', $this->object->getName());
    }

    /**
     * @test
     */
    public function testGetSetName()
    {
        $this->object->setName('testGetSetName');
        $result = $this->object->getName();
        $this->assertEquals('testGetSetName', $result);
    }
}
