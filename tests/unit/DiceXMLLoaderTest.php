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
        $this->markTestIncomplete('create test for loadXml method');
    }
}
