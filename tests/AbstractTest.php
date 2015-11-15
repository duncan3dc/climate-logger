<?php

namespace duncan3dc\CLImateTests;

use duncan3dc\CLImate\Logger;
use League\CLImate\CLImate;
use League\CLImate\Decorator\Style;
use Mockery;
use Psr\Log\LogLevel;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected $cli;
    protected $logger;

    public function setUp()
    {
        $this->cli = Mockery::mock(CLImate::class);

        $style = Mockery::mock(Style::class);
        $style->shouldReceive("get")->andReturn(true);
        $this->cli->style = $style;

        $this->logger = new Logger(LogLevel::DEBUG, $this->cli);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
