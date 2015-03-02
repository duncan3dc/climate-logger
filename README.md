# climate-logger
Use your best friend for the terminal with your favourite PSR-3 compatible projects

## CLImate
Read all about CLImate [here](http://climate.thephpleague.com/)

## PSR-3
Read all about PSR-3 [here](http://www.php-fig.org/psr/psr-3/)

## Examples
Combine the two for simple terminal logging:

```php
$somethingThatLogs->setLogger(new \duncan3dc\CLImate\Logger);
```

## Advanced
You can pass your own customised instance of CLImate to the logger:

```php
$climate = new \League\CLImate\CLImate;
$climate->style->addCommand("debug", ["yellow", "background_black"]);
$logger = new \duncan3dc\CLImate\Logger($climate);

# Now my debug information will be yellow and black
$somethingThatLogs->setLogger($logger);
```
