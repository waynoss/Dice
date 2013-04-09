<?php
namespace Jasrags;

use \Jasrags\Dice\XML\Callback;

/**
 * Class DiceXMLCallbackTest
 * @package Jasrags
 */
class DiceXMLCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Callback
     */
    private $object;

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function testCallback()
    {
        $this->markTestIncomplete('create test for Callback class');
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $this->markTestIncomplete('create test for create method');
    }
}

class ACallback
{
    public $test;

    public function callback($arg1, $arg2)
    {
    }
}
