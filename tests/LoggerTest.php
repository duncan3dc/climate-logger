<?php

namespace duncan3dc\CLImate;

use League\CLImate\CLImate;
use Mockery;
use Psr\Log\LogLevel;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    protected $cli;

    /**
     * @var Logger $logger
     */
    protected $logger;

    public function setUp()
    {
        $this->cli = Mockery::mock('League\CLImate\CLImate');

        $style = Mockery::mock('League\CLImate\Decorator\Style');
        $style->shouldReceive("get")->andReturn(true);
        $this->cli->style = $style;

        $this->logger = new Logger($this->cli, LogLevel::DEBUG);
    }

    public function tearDown()
    {
        Mockery::close();
    }


    public function testEmergency()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Testing emergency");
        $this->logger->emergency("Testing emergency");
    }


    public function testAlert()
    {
        $this->cli->shouldReceive("alert")->once()->with("Testing alert");
        $this->logger->alert("Testing alert");
    }


    public function testCritical()
    {
        $this->cli->shouldReceive("critical")->once()->with("Testing critical");
        $this->logger->critical("Testing critical");
    }


    public function testError()
    {
        $this->cli->shouldReceive("error")->once()->with("Testing error");
        $this->logger->error("Testing error");
    }


    public function testWarning()
    {
        $this->cli->shouldReceive("warning")->once()->with("Testing warning");
        $this->logger->warning("Testing warning");
    }


    public function testNotice()
    {
        $this->cli->shouldReceive("notice")->once()->with("Testing notice");
        $this->logger->notice("Testing notice");
    }


    public function testInfo()
    {
        $this->cli->shouldReceive("info")->once()->with("Testing info");
        $this->logger->info("Testing info");
    }


    public function testDebug()
    {
        $this->cli->shouldReceive("debug")->once()->with("Testing debug");
        $this->logger->debug("Testing debug");
    }


    public function testLog()
    {
        $this->cli->shouldReceive("critical")->once()->with("Testing log");
        $this->logger->log("critical", "Testing log");
    }

    public function testLogLevel()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::EMERGENCY)->log("emergency", "Testing log");

        $this->cli->shouldReceive("alert")->never()->with("Testing log");
        $this->logger->log("alert", "Testing log");
        $this->cli->shouldReceive("alert")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::ALERT)->log("alert", "Testing log");

        $this->cli->shouldReceive("critical")->never()->with("Testing log");
        $this->logger->log("critical", "Testing log");
        $this->cli->shouldReceive("critical")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::CRITICAL)->log("critical", "Testing log");

        $this->cli->shouldReceive("error")->never()->with("Testing log");
        $this->logger->log("error", "Testing log");
        $this->cli->shouldReceive("error")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::ERROR)->log("error", "Testing log");

        $this->cli->shouldReceive("warning")->never()->with("Testing log");
        $this->logger->log("warning", "Testing log");
        $this->cli->shouldReceive("warning")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::WARNING)->log("warning", "Testing log");

        $this->cli->shouldReceive("notice")->never()->with("Testing log");
        $this->logger->log("notice", "Testing log");
        $this->cli->shouldReceive("notice")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::NOTICE)->log("notice", "Testing log");

        $this->cli->shouldReceive("info")->never()->with("Testing log");
        $this->logger->log("info", "Testing log");
        $this->cli->shouldReceive("info")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::INFO)->log("info", "Testing log");

        $this->cli->shouldReceive("debug")->never()->with("Testing log");
        $this->logger->log("debug", "Testing log");
        $this->cli->shouldReceive("debug")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::DEBUG)->log("debug", "Testing log");

    }


    public function testContext()
    {
        $this->cli->shouldReceive("info")->once()->with("With context");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("context: ");
        $this->cli->shouldReceive("info")->once()->with("CONTEXT");

        $this->logger->info("With context", [
            "context"   =>  "CONTEXT",
        ]);
    }


    public function testEmptyContext()
    {
        $this->cli->shouldReceive("info")->once()->with("No context");
        $this->logger->info("No context", []);
    }



    public function testPlaceholders()
    {
        $this->cli->shouldReceive("info")->once()->with("I am Spartacus!");
        $this->logger->info("I am {username}!", [
            "username"  =>  "Spartacus",
        ]);
    }


    public function testPlaceholdersAndContext()
    {
        $this->cli->shouldReceive("info")->once()->with("I am Spartacus!");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("date: ");
        $this->cli->shouldReceive("info")->once()->with("2015-03-01");

        $this->logger->info("I am {username}!", [
            "username"  =>  "Spartacus",
            "date"      =>  "2015-03-01",
        ]);
    }


    public function testRecursiveContext()
    {
        $this->cli->shouldReceive("info")->once()->with("INFO");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("data: ");
        $this->cli->shouldReceive("info")->once()->with("[");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("field1: ");
        $this->cli->shouldReceive("info")->once()->with("One");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("field2: ");
        $this->cli->shouldReceive("info")->once()->with("Two");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("extra: ");
        $this->cli->shouldReceive("info")->once()->with("[");

        $this->cli->shouldReceive("tab")->with(3)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("0: ");
        $this->cli->shouldReceive("info")->once()->with("Three");

        $this->cli->shouldReceive("tab")->with(3)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->andReturn($this->cli);
        $this->cli->shouldReceive("inline")->once()->with("1: ");
        $this->cli->shouldReceive("info")->once()->with("Four");

        $this->cli->shouldReceive("tab")->with(2)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->with("]");

        $this->cli->shouldReceive("tab")->with(1)->once()->andReturn($this->cli);
        $this->cli->shouldReceive("info")->once()->with("]");

        $this->logger->info("INFO", [
            "data"      =>  [
                "field1"    =>  "One",
                "field2"    =>  "Two",
                "extra"     =>  ["Three", "Four"],
            ],
        ]);
    }
}
