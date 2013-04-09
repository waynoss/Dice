<?php
namespace Jasrags;

use Jasrags\Dice\Rule;

/**
 * Class DiceRuleTest
 * @package Jasrags
 */
class DiceRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rule
     */
    private $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Rule();
    }

    /**
     * @test
     */
    public function testSetIsShared()
    {
        $this->object->setShared(true);
        $result = $this->object->isShared();
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testSetIsInherit()
    {
        $this->object->setInherit(true);
        $result = $this->object->isInherit();
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testGetSetConstructParams()
    {
        $this->object->setConstructParams(array('A', 'B', 'C'));
        $result = $this->object->getConstructParams();
        $this->assertEquals(array('A', 'B', 'C'), $result);
    }

    /**
     * @test
     */
    public function testGetSetSubstitutions()
    {
        $this->object->setSubstitutions(array('A' => 'B'));
        $result = $this->object->getSubstitutions();
        $this->assertEquals(array('A' => 'B'), $result);
    }

    /**
     * @test
     */
    public function testAddSubstitution()
    {
        $this->object->addSubstitution('A', 'B');
        $result = $this->object->getSubstitutions();
        $this->assertEquals(array('A' => 'B'), $result);
    }

    /**
     * @test
     */
    public function testGetSetInstanceOf()
    {
        $this->object->setInstanceOf('A');
        $result = $this->object->getInstanceOf();
        $this->assertEquals('A', $result);
    }

    /**
     * @test
     */
    public function testGetSetNewInstances()
    {
        $this->object->setNewInstances(array('A', 'B', 'C'));
        $result = $this->object->getNewInstances();
        $this->assertEquals(array('A', 'B', 'C'), $result);
    }

    /**
     * @test
     */
    public function testGetSetShareInstances()
    {
        $this->object->setShareInstances(array('A', 'B', 'C'));
        $result = $this->object->getShareInstances();
        $this->assertEquals(array('A', 'B', 'C'), $result);
    }

    /**
     * @test
     */
    public function testGetSetCall()
    {
        $this->object->setCall(array('A', 'B', 'C'));
        $result = $this->object->getCall();
        $this->assertEquals(array('A', 'B', 'C'), $result);
    }

    /**
     * @test
     */
    public function testAddCall()
    {
        $this->object->addCall('A', array('B', 'C'));
        $result = $this->object->getCall();
        $this->assertEquals(array(array('A', array('B', 'C'))), $result);
    }
}
