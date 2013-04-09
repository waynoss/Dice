<?php
namespace Jasrags;

use Jasrags\Dice\Rule;
use Jasrags\Dice\Instance;

/**
 * Class DiceTest
 * @package Jasrags
 */
class DiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jasrags\Dice
     */
    private $dice;

    protected function setUp()
    {
        parent::setUp();
        $this->dice = new Dice();
    }

    protected function tearDown()
    {
        $this->dice = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function testAssign()
    {
        $obj = $this->getMock('\stdClass', array(), array(), 'AssignMe');
        $this->dice->assign($obj);
        $result = $this->dice->create('AssignMe');
        $this->assertSame($obj, $result);
    }

    /**
     * @test
     */
    public function testSetDefaultRule()
    {
        $defaultBehaviour = new Rule();
        $defaultBehaviour->shared = true;
        $defaultBehaviour->newInstances = array('Foo', 'Bar');
        $this->dice->addRule('*', $defaultBehaviour);
        $this->assertSame($defaultBehaviour, $this->dice->getRule('*'));
    }

    /**
     * @test
     */
    public function testDefaultRuleWorks()
    {
        $defaultBehaviour = new Rule();
        $defaultBehaviour->shared = true;

        $this->dice->addRule('*', $defaultBehaviour);

        $rule = $this->dice->getRule('Jasrags\A');

        $this->assertTrue($rule->shared);

        $a1 = $this->dice->create('Jasrags\A');
        $a2 = $this->dice->create('Jasrags\A');

        $this->assertSame($a1, $a2);
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $this->getMock('stdClass', array(), array(), 'TestCreate');
        $myobj = $this->dice->create('TestCreate');
        $this->assertInstanceOf('TestCreate', $myobj);
    }

    /**
     * @test
     */
    public function testCreateInvalid()
    {
        //"can't expect default exception". Not sure why.
        $this->setExpectedException('ErrorException');
        try {
            $this->dice->create('SomeClassThatDoesNotExist');
        } catch (\Exception $e) {
            throw new \ErrorException('Error occurred');
        }
    }

    /*
     * Object graph creation cannot be tested with mocks because the constructor need to be tested.
     * You can't set 'expects' on the objects which are created making them redundant for that as well
     * Need real classes to test with unfortunately.
     * @test
     */
    public function testObjectGraphCreation()
    {
        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\B', $a->b);
        $this->assertInstanceOf('Jasrags\C', $a->b->c);
        $this->assertInstanceOf('Jasrags\D', $a->b->c->d);
        $this->assertInstanceOf('Jasrags\E', $a->b->c->e);
        $this->assertInstanceOf('Jasrags\F', $a->b->c->e->f);
    }

    /**
     * @test
     */
    public function testNewInstances()
    {
        $rule = new Rule;
        $rule->shared = true;
        $this->dice->addRule('Jasrags\B', $rule);

        $rule = new Rule;
        $rule->newInstances[] = 'Jasrags\B';
        $this->dice->addRule('Jasrags\A', $rule);

        $a1 = $this->dice->create('Jasrags\A');
        $a2 = $this->dice->create('Jasrags\A');

        $this->assertNotSame($a1->b, $a2->b);
    }

    /**
     * @test
     */
    public function testSharedNamed()
    {
        $rule = new Rule();
        $rule->shared = true;
        $rule->instanceOf = 'Jasrags\A';

        $this->dice->addRule('[\Jasrags\A]', $rule);

        $a1 = $this->dice->create('[\Jasrags\A]');
        $a2 = $this->dice->create('[\Jasrags\A]');
        $this->assertSame($a1, $a2);
    }

    /**
     * @test
     */
    public function testForceNewInstance()
    {
        $rule = new Rule;
        $rule->shared = true;
        $this->dice->addRule('Jasrags\A', $rule);

        $a1 = $this->dice->create('Jasrags\A');
        $a2 = $this->dice->create('Jasrags\A');

        $a3 = $this->dice->create('Jasrags\A', array(), null, true);

        $this->assertSame($a1, $a2);
        $this->assertNotSame($a1, $a3);
        $this->assertNotSame($a2, $a3);
    }

    /**
     * @test
     */
    public function testSharedRule()
    {
        $shared = new Rule();
        $shared->shared = true;

        $this->dice->addRule('Jasrags\MyObj', $shared);

        $obj = $this->dice->create('Jasrags\MyObj');
        $this->assertInstanceOf('Jasrags\MyObj', $obj);

        $obj2 = $this->dice->create('Jasrags\MyObj');
        $this->assertInstanceOf('Jasrags\MyObj', $obj2);

        $this->assertSame($obj, $obj2);

        //This check isn't strictly needed but it's nice to have that safety measure!
        $obj->setFoo('bar');
        $this->assertEquals($obj->getFoo(), $obj2->getFoo());
        $this->assertEquals($obj->getFoo(), 'bar');
        $this->assertEquals($obj2->getFoo(), 'bar');
    }

    /**
     * @test
     * @group grain
     */
    public function testSubstitutionText()
    {
        $rule = new Rule();
        $rule->substitutions['Jasrags\B'] = new Instance('Jasrags\ExtendedB');
        $this->dice->addRule('Jasrags\A', $rule);

        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testSubstitutionCallback()
    {
        $rule = new Rule;
        $injection = $this->dice;
        $rule->substitutions['Jasrags\B'] = function () use ($injection) {
            return $injection->create('Jasrags\ExtendedB');
        };

        $this->dice->addRule('Jasrags\A', $rule);

        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testSubstitutionObject()
    {
        $rule = new Rule;

        $rule->substitutions['Jasrags\B'] = $this->dice->create('Jasrags\ExtendedB');

        $this->dice->addRule('Jasrags\A', $rule);

        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testSubstitutionString()
    {
        $rule = new Rule;

        $rule->substitutions['Jasrags\B'] = new Instance('Jasrags\ExtendedB');

        $this->dice->addRule('Jasrags\A', $rule);

        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testConstructParams()
    {
        $rule = new Rule();
        $rule->constructParams = array('foo', 'bar');
        $this->dice->addRule('Jasrags\RequiresConstructorArgsA', $rule);

        $obj = $this->dice->create('Jasrags\RequiresConstructorArgsA');

        $this->assertEquals($obj->foo, 'foo');
        $this->assertEquals($obj->bar, 'bar');
    }

    /**
     * @test
     */
    public function testConstructParamsMixed()
    {
        $rule = new Rule;
        $rule->constructParams = array('foo', 'bar');
        $this->dice->addRule('Jasrags\RequiresConstructorArgsB', $rule);

        $obj = $this->dice->create('Jasrags\RequiresConstructorArgsB');

        $this->assertEquals($obj->foo, 'foo');
        $this->assertEquals($obj->bar, 'bar');
        $this->assertInstanceOf('Jasrags\A', $obj->a);
    }

    /**
     * @test
     */
    public function testConstructArgs()
    {
        $obj = $this->dice->create('Jasrags\RequiresConstructorArgsA', array('foo', 'bar'));
        $this->assertEquals($obj->foo, 'foo');
        $this->assertEquals($obj->bar, 'bar');
    }

    /**
     * @test
     */
    public function testConstructArgsMixed()
    {
        $obj = $this->dice->create('Jasrags\RequiresConstructorArgsB', array('foo', 'bar'));
        $this->assertEquals($obj->foo, 'foo');
        $this->assertEquals($obj->bar, 'bar');
        $this->assertInstanceOf('Jasrags\A', $obj->a);
    }

    /**
     * @test
     */
    public function testCreateCallback()
    {
        $result = false;
        $callback = function ($params) use (&$result) {
            $result = $params;
        };

        $this->dice->create('Jasrags\A', array(), $callback);

        $this->assertTrue(is_array($result));
        $this->assertInstanceOf('Jasrags\B', $result[0]);
    }

    /**
     * @test
     */
    public function testCreateArgs1()
    {
        $a = $this->dice->create('Jasrags\A', array($this->dice->create('Jasrags\ExtendedB')));
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testCreateArgs2()
    {
        $a2 = $this->dice->create('Jasrags\A2', array($this->dice->create('Jasrags\ExtendedB'), 'Foo'));
        $this->assertInstanceOf('Jasrags\B', $a2->b);
        $this->assertInstanceOf('Jasrags\C', $a2->c);
        $this->assertEquals($a2->foo, 'Foo');
    }

    /**
     * @test
     */
    public function testCreateArgs3()
    {
        //reverse order args. It should be smart enough to handle this.
        $a2 = $this->dice->create('Jasrags\A2', array('Foo', $this->dice->create('Jasrags\ExtendedB')));
        $this->assertInstanceOf('Jasrags\B', $a2->b);
        $this->assertInstanceOf('Jasrags\C', $a2->c);
        $this->assertEquals($a2->foo, 'Foo');
    }

    /**
     * @test
     */
    public function testCreateArgs4()
    {
        $a2 = $this->dice->create('Jasrags\A3', array('Foo', $this->dice->create('Jasrags\ExtendedB')));
        $this->assertInstanceOf('Jasrags\B', $a2->b);
        $this->assertInstanceOf('Jasrags\C', $a2->c);
        $this->assertEquals($a2->foo, 'Foo');
    }

    /**
     * @test
     */
    public function testMultipleSharedInstancesByNameMixed()
    {
        $rule = new Rule;
        $rule->shared = true;
        $rule->constructParams[] = 'FirstY';

        $this->dice->addRule('Jasrags\Y', $rule);

        $rule = new Rule;
        $rule->instanceOf = 'Jasrags\Y';
        $rule->shared = true;
        $rule->constructParams[] = 'SecondY';

        $this->dice->addRule('[Y2]', $rule);

        $rule = new Rule;
        $rule->constructParams = array(new Instance('Jasrags\Y'), new Instance('[Y2]'));

        $this->dice->addRule('Jasrags\Z', $rule);

        $z = $this->dice->create('Jasrags\Z');
        $this->assertEquals($z->y1->name, 'FirstY');
        $this->assertEquals($z->y2->name, 'SecondY');
    }

    /**
     * @test
     */
    public function testNonSharedComponentByNameA()
    {
        $rule = new Rule;
        $rule->instanceOf = 'Jasrags\ExtendedB';
        $this->dice->addRule('$B', $rule);

        $rule = new Rule;
        $rule->constructParams[] = new Instance('$B');
        $this->dice->addRule('Jasrags\A', $rule);

        $a = $this->dice->create('Jasrags\A');
        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
    }

    /**
     * @test
     */
    public function testNonSharedComponentByName()
    {
        $rule = new Rule;
        $rule->instanceOf = 'Jasrags\Y3';
        $rule->constructParams[] = 'test';


        $this->dice->addRule('$Y2', $rule);


        $y2 = $this->dice->create(new Instance('$Y2'));

        $this->assertInstanceOf('Jasrags\Y3', $y2);

        $rule = new Rule;

        $rule->constructParams[] = new Instance('$Y2');
        $this->dice->addRule('Jasrags\Y1', $rule);

        $y1 = $this->dice->create('Jasrags\Y1');
        $this->assertInstanceOf('Jasrags\Y3', $y1->y2);
    }

    /**
     * @test
     */
//    public function testSubstitutionByName()
//    {
//        $rule = new Rule;
//        $rule->instanceOf = 'Jasrags\ExtendedB';
//        $this->dice->addRule('$B', $rule);
//
//        $rule = new Rule;
//        $rule->substitutions['B'] = new Instance('$B');
//
//        $this->dice->addRule('Jasrags\A', $rule);
//        $a = $this->dice->create('Jasrags\A');
//
//        $this->assertInstanceOf('Jasrags\ExtendedB', $a->b);
//    }

    /**
     * @test
     */
    public function testMultipleSubstitutions()
    {
        $rule = new Rule;
        $rule->instanceOf = 'Jasrags\Y2';
        $rule->constructParams[] = 'first';
        $this->dice->addRule('$Y2A', $rule);

        $rule = new Rule;
        $rule->instanceOf = 'Jasrags\Y2';
        $rule->constructParams[] = 'second';
        $this->dice->addRule('$Y2B', $rule);

        $rule = new Rule;
        $rule->constructParams = array(new Instance('$Y2A'), new Instance('$Y2B'));
        $this->dice->addRule('Jasrags\HasTwoSameDependencies', $rule);

        $twodep = $this->dice->create('Jasrags\HasTwoSameDependencies');

        $this->assertEquals('first', $twodep->y2a->name);
        $this->assertEquals('second', $twodep->y2b->name);
    }

    /**
     * @test
     */
    public function testCall()
    {
        $rule = new Rule;
        $rule->call[] = array('callMe', array());
        $this->dice->addRule('Jasrags\TestCall', $rule);
        $object = $this->dice->create('Jasrags\TestCall');
        $this->assertTrue($object->isCalled);
    }

    /**
     * @test
     */
    public function testCallWithParameters()
    {
        $rule = new Rule;
        $rule->call[] = array('callMe', array('one', 'two'));
        $this->dice->addRule('Jasrags\TestCall2', $rule);
        $object = $this->dice->create('Jasrags\TestCall2');
        $this->assertEquals('one', $object->foo);
        $this->assertEquals('two', $object->bar);
    }

    /**
     * @test
     */
    public function testInterfaceRule()
    {
        $rule = new Rule;

        $rule->shared = true;
        $this->dice->addRule('Jasrags\interfaceTest', $rule);

        $one = $this->dice->create('Jasrags\InterfaceTestClass');
        $two = $this->dice->create('Jasrags\InterfaceTestClass');


        $this->assertSame($one, $two);
    }

    /**
     * @test
     */
    public function testBestMatch()
    {
        $bestMatch = $this->dice->create('Jasrags\BestMatch', array('foo', $this->dice->create('Jasrags\A')));
        $this->assertEquals('foo', $bestMatch->string);
        $this->assertInstanceOf('Jasrags\A', $bestMatch->a);
    }

    /**
     * @test
     */
    public function testShareInstances()
    {
        $rule = new Rule();
        $rule->shareInstances = array(new Instance('Jasrags\Shared'));
        $this->dice->addRule('Jasrags\TestSharedInstancesTop', $rule);


        $shareTest = $this->dice->create('Jasrags\TestSharedInstancesTop');

        $this->assertinstanceOf('Jasrags\TestSharedInstancesTop', $shareTest);

        $this->assertInstanceOf('Jasrags\SharedInstanceTest1', $shareTest->share1);
        $this->assertInstanceOf('Jasrags\SharedInstanceTest2', $shareTest->share2);

        $this->assertSame($shareTest->share1->shared, $shareTest->share2->shared);
        $this->assertEquals($shareTest->share1->shared->uniq, $shareTest->share2->shared->uniq);
    }

    /**
     * @test
     */
    public function testShareInstancesMultiple()
    {
        $rule = new Rule();
        $rule->shareInstances = array(new Instance('Jasrags\Shared'));
        $this->dice->addRule('Jasrags\TestSharedInstancesTop', $rule);

        $shareTest = $this->dice->create('Jasrags\TestSharedInstancesTop');

        $this->assertinstanceOf('Jasrags\TestSharedInstancesTop', $shareTest);

        $this->assertInstanceOf('Jasrags\SharedInstanceTest1', $shareTest->share1);
        $this->assertInstanceOf('Jasrags\SharedInstanceTest2', $shareTest->share2);

        $this->assertSame($shareTest->share1->shared, $shareTest->share2->shared);
        $this->assertEquals($shareTest->share1->shared->uniq, $shareTest->share2->shared->uniq);

        $shareTest2 = $this->dice->create('Jasrags\TestSharedInstancesTop');
        $this->assertSame($shareTest2->share1->shared, $shareTest2->share2->shared);
        $this->assertEquals($shareTest2->share1->shared->uniq, $shareTest2->share2->shared->uniq);

        $this->assertNotSame($shareTest->share1->shared, $shareTest2->share2->shared);
        $this->assertNotEquals($shareTest->share1->shared->uniq, $shareTest2->share2->shared->uniq);
    }
}

class Shared
{
    public $uniq;

    public function __construct()
    {
        $this->uniq = uniqid();
    }
}

class TestSharedInstancesTop
{
    public $share1;
    public $share2;

    public function __construct(\Jasrags\SharedInstanceTest1 $share1, \Jasrags\SharedInstanceTest2 $share2)
    {
        $this->share1 = $share1;
        $this->share2 = $share2;
    }
}

class SharedInstanceTest1
{
    public $shared;

    public function __construct(\Jasrags\Shared $shared)
    {
        $this->shared = $shared;
    }
}

class SharedInstanceTest2
{
    public $shared;

    public function __construct(\Jasrags\Shared $shared)
    {
        $this->shared = $shared;
    }
}

class TestCall
{
    public $isCalled = false;

    public function callMe()
    {
        $this->isCalled = true;
    }
}

class TestCall2
{
    public $foo;
    public $bar;

    public function callMe($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class TestCall3
{
    public $a;

    public function callMe(\Jasrags\A $a)
    {
        $this->a = $a;
    }
}

class HasTwoSameDependencies
{
    public $y2a;
    public $y2b;

    public function __construct(\Jasrags\Y2 $y2a, \Jasrags\Y2 $y2b)
    {
        $this->y2a = $y2a;
        $this->y2b = $y2b;
    }
}

class Y1
{
    public $y2;

    public function __construct(\Jasrags\Y2 $y2)
    {
        $this->y2 = $y2;
    }
}

class Y2
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}

class Y3 extends \Jasrags\Y2
{
}

class Z
{
    public $y1;
    public $y2;

    public function __construct(\Jasrags\Y $y1, \Jasrags\Y $y2)
    {
        $this->y1 = $y1;
        $this->y2 = $y2;
    }
}

class Y
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}

class BestMatch
{
    public $a;
    public $string;
    public $b;

    public function __construct($string, \Jasrags\A $a, \Jasrags\B $b)
    {
        $this->a = $a;
        $this->string = $string;
        $this->b = $b;
    }
}

/**
 * Because the DIC's job is to create other classes, some dummy class
 * definitions are required. Mocks cannot be used because the DIC relies
 * on class definitions.
 */

class MyObj
{
    private $foo;

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}

class A2
{
    public $b;
    public $c;
    public $foo;

    public function __construct(\Jasrags\B $b, \Jasrags\C $c, $foo)
    {
        $this->b = $b;
        $this->foo = $foo;
        $this->c = $c;
    }
}

class A3
{
    public $b;
    public $c;
    public $foo;

    public function __construct(\Jasrags\C $c, $foo, \Jasrags\B $b)
    {
        $this->b = $b;
        $this->foo = $foo;
        $this->c = $c;
    }
}

class A
{
    public $b;

    public function __construct(\Jasrags\B $b)
    {
        $this->b = $b;
    }
}

class B
{
    public $c;

    public function __construct(\Jasrags\C $c)
    {
        $this->c = $c;
    }
}

class ExtendedB extends \Jasrags\B
{

}

class C
{
    public $d;
    public $e;

    public function __construct(\Jasrags\D $d, \Jasrags\E $e)
    {
        $this->d = $d;
        $this->e = $e;
    }
}

class D
{

}

class E
{
    public $f;

    public function __construct(\Jasrags\F $f)
    {
        $this->f = $f;
    }
}

class F
{
}

class RequiresConstructorArgsA
{
    public $foo;
    public $bar;

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class RequiresConstructorArgsB
{
    public $a;
    public $foo;
    public $bar;

    public function __construct(\Jasrags\A $a, $foo, $bar)
    {
        $this->a = $a;
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

interface interfaceTest
{
}

class InterfaceTestClass implements \Jasrags\interfaceTest
{
}
