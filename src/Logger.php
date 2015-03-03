<?php

namespace duncan3dc\CLImate;

use League\CLImate\CLImate;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * A PSR-3 compatiable logger that uses CLImate for output.
 */
class Logger extends AbstractLogger
{
    /**
     * @var array $levels Conversion of the level strings to their numeric representations.
     */
    protected $levels = [
        LogLevel::EMERGENCY =>  1,
        LogLevel::ALERT     =>  2,
        LogLevel::CRITICAL  =>  3,
        LogLevel::ERROR     =>  4,
        LogLevel::WARNING   =>  5,
        LogLevel::NOTICE    =>  6,
        LogLevel::INFO      =>  7,
        LogLevel::DEBUG     =>  8,
    ];

    /**
     * @var int $level Ignore logging attempts at a level less than this.
     */
    protected $level;

    /**
     * @var CLImate $climate The underlying climate instance we are using for output.
     */
    protected $climate;

    /**
     * Create a new Logger instance.
     *
     * @param mixed   $level   Either a LogLevel constant, or a string (eg 'debug'), or a number (1-8)
     * @param CLImate $climate An existing CLImate instance to use for output
     */
    public function __construct($level = LogLevel::INFO, CLImate $climate = null)
    {
        $this->setLogLevel($level);

        if ($climate === null) {
            $climate = new CLImate;
        }
        $this->climate = $climate;

        # Define some default styles to use for the output
        $commands = [
            "emergency" =>  ["white", "bold", "background_red"],
            "alert"     =>  ["white", "background_yellow"],
            "critical"  =>  ["red", "bold"],
            "error"     =>  ["red"],
            "warning"   =>  "yellow",
            "notice"    =>  "light_cyan",
            "info"      =>  "green",
            "debug"     =>  "dark_gray",
        ];

        # If any of the required styles are not defined then define them now
        foreach ($commands as $command => $style) {
            if (!$this->climate->style->get($command)) {
                $this->climate->style->addCommand($command, $style);
            }
        }
    }


    /**
     * Get a numeric log level for the passed parameter.
     *
     * @param mixed $level Either a LogLevel constant, or a string (eg 'debug'), or a number (1-8)
     *
     * @return int
     */
    protected function convertLevel($level)
    {
        # If this is one of the defined string log levels then return it's numeric value
        $key = strtolower($level);
        if (isset($this->levels[$key])) {
            return $this->levels[$key];
        }

        # If it doesn't look like a number, default to the most severe log level
        if (!is_numeric($level)) {
            return 1;
        }

        # If it's lower than the lowest level then default to the lowest level
        if ($level < 1) {
            return 1;
        }

        # If it's higher than the highest level then default to the highest
        if ($level > 8) {
            return 8;
        }

        # If it's already a valid numeric log level then return it
        return $level;
    }


    /**
     * Set the current level we are logging at.
     *
     * @param mixed $level Ignore logging attempts at a level less the $level
     *
     * @return static
     */
    public function setLogLevel($level)
    {
        $this->level = $this->convertLevel($level);

        return $this;
    }


    /**
     * Log messages to a CLImate instance.
     *
     * @param mixed          $level    The level of the log message
     * @param string|object  $message  If an object is passed it must implement __toString()
     * @param array          $context  Placeholders to be substituted in the message
     *
     * @return static
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->convertLevel($level) > $this->level) {
            return $this;
        }

        # Handle objects implementing __toString
        $message = (string) $message;

        # Handle any placeholders in the $context array
        foreach ($context as $key => $val) {
            $placeholder = "{" . $key . "}";

            # If this context key is used as a placeholder, then replace it, and remove it from the $context array
            if (strpos($message, $placeholder) !== false) {
                $val = (string) $val;
                $message = str_replace($placeholder, $val, $message);
                unset($context[$key]);
            }
        }

        # Send the message to the climate instance
        $this->climate->{$level}($message);

        # Append any context information not used as placeholders
        $this->outputRecursiveContext($level, $context, 1);

        return $this;
    }


    /**
     * Handle recursive arrays in the logging context.
     *
     * @param mixed  $level    The level of the log message
     * @param array  $context  The array of context to output
     * @param int    $indent   The current level of indentation to be used
     *
     * @return void
     */
    protected function outputRecursiveContext($level, array $context, $indent)
    {
        foreach ($context as $key => $val) {
            $this->climate->tab($indent);

            $this->climate->{$level}()->inline("{$key}: ");

            if (is_array($val)) {
                $this->climate->{$level}("[");
                $this->outputRecursiveContext($level, $val, $indent + 1);
                $this->climate->tab($indent)->{$level}("]");
            } else {
                $this->climate->{$level}((string) $val);
            }
        }
    }
}
