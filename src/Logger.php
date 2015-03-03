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
     * @var CLImate $climate The underlying climate instance we are using for output.
     */
    protected $climate;

    /**
     * @var array $log_levels List of supported levels
     */
    protected $log_levels = [
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
     * @var int $level - Ignore logging attempts at a level less the $level
     */
    protected $level = null;

    /**
     * Create a new Logger instance.
     *
     * @param CLImate $climate An existing CLImate instance to use for output
     * @param string  $level   Ignore logging attempts at a level less the $level
     */
    public function __construct(CLImate $climate = null, $level=LogLevel::INFO)
    {
        if ($climate === null) {
            $climate = new CLImate;
        }
        $this->climate = $climate;

        $this->setLogLevel($level);

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
     * @param string $level Ignore logging attempts at a level less the $level
     * @return static
     */
    public function setLogLevel($level) {
        if ( ! isset($this->log_levels[$level]) ) {
            throw new \InvalidArgumentException("Log level is invalid");
        }
        $this->level = $this->log_levels[$level];
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
        $logLevel = isset($this->log_levels[$level]) ? $level : LogLevel::EMERGENCY;
        if ( $this->log_levels[$logLevel] > $this->level ) {
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
