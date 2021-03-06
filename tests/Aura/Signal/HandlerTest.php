<?php
namespace Aura\Signal;

/**
 * Test class for Handler.
 * Generated by PHPUnit on 2011-02-23 at 20:22:43.
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Handler
     */
    protected $handler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }
    
    protected function newHandler($sender)
    {
        $signal = 'mock_signal';
        $callback = function($value) { return $value . '!!!'; };
        return new Handler($sender, $signal, $callback);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @todo Implement testExec().
     */
    public function testExecOnSenderClassAndParent()
    {
        $handler = $this->newHandler('\Exception');
        
        // this should be a match
        $origin = new \Exception;
        $params = $handler->exec($origin, 'mock_signal', ['hello']);
        $this->assertSame('hello!!!', $params['value']);
        $this->assertSame($origin, $params['origin']);
        
        // this should be a match, since it has \Exception as a parent
        $origin = new \UnexpectedValueException;
        $params = $handler->exec($origin, 'mock_signal', ['hello']);
        $this->assertSame('hello!!!', $params['value']);
        $this->assertSame($origin, $params['origin']);
        
        // this should not be a match
        $origin = new \StdClass;
        $params = $handler->exec($origin, 'mock_signal', ['hello']);
        $this->assertNull($params);
    }
    
    public function testExecOnSenderObject()
    {
        $object1 = new \StdClass;
        $handler = $this->newHandler($object1);
        
        // this should be a match
        $params = $handler->exec($object1, 'mock_signal', ['hello']);
        $this->assertSame('hello!!!', $params['value']);
        $this->assertSame($object1, $params['origin']);
        
        // this should not match, even though it's of the same class
        $object2 = new \StdClass;
        $params = $handler->exec($object2, 'mock_signal', ['hello']);
        $this->assertNull($params);
    }
    
    public function testExecOnEveryClass()
    {
        $handler = $this->newHandler('*');
        
        // this should be a match
        $origin = new \Exception;
        $params = $handler->exec($origin, 'mock_signal', ['hello']);
        $this->assertSame('hello!!!', $params['value']);
        $this->assertSame($origin, $params['origin']);
        
        // this should be a match
        $origin = new \StdClass;
        $params = $handler->exec($origin, 'mock_signal', ['hello']);
        $this->assertSame('hello!!!', $params['value']);
        $this->assertSame($origin, $params['origin']);
        
        // this should not be a match (wrong signal)
        $params = $handler->exec($origin, 'wrong_signal', ['hello']);
        $this->assertNull($params);
    }
}
