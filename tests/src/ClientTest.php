<?php

/**
 * Copyright 2014 Fabian Grutschus. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those
 * of the authors and should not be interpreted as representing official policies,
 * either expressed or implied, of the copyright holders.
 *
 * @author    Fabian Grutschus <f.grutschus@lubyte.de>
 * @copyright 2014 Fabian Grutschus. All rights reserved.
 * @license   BSD
 * @link      http://github.com/fabiang/xmpp
 */

namespace Fabiang\Xmpp;

use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Protocol\DefaultImplementation;
use Fabiang\Xmpp\Event\EventManager;
use Fabiang\Xmpp\Connection\Test;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-17 at 10:05:30.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    protected $object;

    /**
     *
     * @var Options
     */
    protected $options;
    
    /**
     *
     * @var Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->connection = new Test;
        $this->connection->setData(array(
            "<?xml version='1.0'?><stream:stream xmlns='jabber:client' xmlns:stream='http://etherx.jabber.org/streams' "
            . "id='1234567890' from='localhost' version='1.0' xml:lang='en'><stream:features></stream:features>"
        ));

        $options = new Options;
        $options->setImplementation(new DefaultImplementation);
        $options->setTo('a');
        $options->setConnection($this->connection);

        $this->object  = new Client($options);
        $this->options = $options;
    }

    /**
     * Test constructor.
     *
     * @covers Fabiang\Xmpp\Client::__construct
     * @covers Fabiang\Xmpp\Client::setupImplementation
     * @return void
     */
    public function testConstructor()
    {
        $connection     = $this->object->getOptions()->getConnection();
        $eventManager   = $this->object->getEventManager();
        $implementation = $this->object->getOptions()->getImplementation();

        $this->assertSame($connection->getEventManager(), $eventManager);

        $eventList = $eventManager->getEventList();
        $this->assertArrayHasKey('logger', $eventList);
        $this->assertInstanceOf('\Fabiang\Xmpp\EventListener\Logger', $eventList['logger'][0][0]);
        $this->assertSame('event', $eventList['logger'][0][1]);

        $this->assertSame($implementation->getEventManager(), $eventManager);
    }

    /**
     * Test connection.
     * 
     * @covers Fabiang\Xmpp\Client::connect
     * @covers Fabiang\Xmpp\Client::getConnection
     * @return void
     */
    public function testConnect()
    {
        $this->object->connect();
        $this->assertTrue($this->options->getConnection()->isConnected());
    }

    /**
     * Test disconnecting.
     * 
     * @covers Fabiang\Xmpp\Client::disconnect
     * @depends testConnect
     * @return void
     */
    public function testDisconnect()
    {
        $this->object->connect();
        $this->object->disconnect();
        $this->assertFalse($this->options->getConnection()->isConnected());
    }

    /**
     * Test sending data.
     * 
     * @covers Fabiang\Xmpp\Client::send
     * @return void
     */
    public function testSend()
    {
        $this->object->connect();
        $this->object->send(new Protocol\Message);
        $this->assertCount(2, $this->connection->getBuffer());
    }

    /**
     * Test setting and getting event manager.
     *
     * @covers Fabiang\Xmpp\Client::getEventManager
     * @covers Fabiang\Xmpp\Client::setEventManager
     * @return void
     */
    public function testSetAndGetEventManager()
    {
        $this->assertInstanceOf('\Fabiang\Xmpp\Event\EventManager', $this->object->getEventManager());
        $eventManager = new EventManager;
        $this->assertSame($eventManager, $this->object->setEventManager($eventManager)->getEventManager());
    }

    /**
     * Test getting options object.
     * 
     * @covers Fabiang\Xmpp\Client::getOptions
     * @return void
     */
    public function testGetOptions()
    {
        $this->assertSame($this->options, $this->object->getOptions());
    }

}