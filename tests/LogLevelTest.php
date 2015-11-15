<?php

namespace duncan3dc\CLImateTests;

use Psr\Log\LogLevel;

class LogLevelest extends AbstractTest
{

    public function testLevelEmergency()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Testing log");
        $this->logger->setLogLevel(LogLevel::EMERGENCY)->emergency("Testing log");
    }

    public function testLevelAlert()
    {
        $this->cli->shouldReceive("alert")->never();
        $this->logger->setLogLevel(LogLevel::EMERGENCY)->alert("Testing log");
    }

    public function testLevelNotice()
    {
        $this->cli->shouldReceive("notice")->once()->with("Notice");
        $this->logger->setLogLevel("notice")->notice("Notice");
    }

    public function testLevelDebug()
    {
        $this->cli->shouldReceive("debug")->once()->with("Debug");
        $this->logger->setLogLevel("DEBUG")->debug("Debug");
    }

    public function testNumericLevel()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Some Info");
        $this->logger->setLogLevel(5)->emergency("Some Info");
    }

    public function testTooHighLevel()
    {
        $this->cli->shouldReceive("debug")->once()->with("Some Info");
        $this->logger->setLogLevel(15)->debug("Some Info");
    }

    public function testTooLowLevel()
    {
        $this->cli->shouldReceive("debug")->never();
        $this->logger->setLogLevel(0)->debug("Some Info");
    }

    public function testInvalidLevel()
    {
        $this->cli->shouldReceive("emergency")->once()->with("Invalid Stuff");
        $this->cli->shouldReceive("info")->never();
        $this->logger->setLogLevel("INVALID");
        $this->logger->emergency("Invalid Stuff");
        $this->logger->info("Nope");
    }
}
