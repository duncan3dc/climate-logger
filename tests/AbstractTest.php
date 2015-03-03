<?php

namespace duncan3dc\CLImate;

use League\CLImate\CLImate;
use Mockery;
use Psr\Log\LogLevel;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected $cli;
    protected $logger;

    public function setUp()
    {
        $this->cli = Mockery::mock('League\CLImate\CLImate');

        $style = Mockery::mock('League\CLImate\Decorator\Style');
        $style->shouldReceive("get")->andReturn(true);
        $this->cli->style = $style;

        $this->logger = new Logger(LogLevel::DEBUG, $this->cli);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
