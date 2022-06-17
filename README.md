# PHP CLI Progress Bar

[![Latest version](https://img.shields.io/packagist/v/nickbeen/php-cli-progress-bar)](https://packagist.org/packages/nickbeen/php-cli-progress-bar)
[![Build status](https://img.shields.io/github/workflow/status/nickbeen/php-cli-progress-bar/Run%20tests)](https://packagist.org/packages/nickbeen/php-cli-progress-bar)
[![Total downloads](https://img.shields.io/packagist/dt/nickbeen/php-cli-progress-bar)](https://packagist.org/packages/nickbeen/php-cli-progress-bar)
[![PHP Version](https://img.shields.io/packagist/php-v/nickbeen/php-cli-progress-bar)](https://packagist.org/packages/nickbeen/php-cli-progress-bar)
[![License](https://img.shields.io/packagist/l/nickbeen/php-cli-progress-bar)](https://packagist.org/packages/nickbeen/php-cli-progress-bar)

For creating minimal progress bars in PHP CLI scripts.
It has no dependencies, but requires PHP 8.0 or higher.
This library is mainly built for iterating to countable variables, but also works easily with ticking through less structured step-by-step scripts.

Many PHP CLI progress bars have been written in the past, but most haven't been updated in years.
This library uses the latest PHP features such as return types, named arguments and constructor property promotions.
For creating richer, more customizable progress bars, check alternatives such as the progress bar helper included in the [symfony/console](https://symfony.com/doc/current/components/console/helpers/progressbar.html) package.

## Requirements
- PHP 8.0 or higher

## Installation

Install the library into your project with Composer.

```
composer require nickbeen/php-cli-progress-bar
```

## Usage

With this library you can display a progress bar in a PHP CLI script to indicate the script is doing its work and how far it has progressed.
All you need to do is start displaying the progress bar, tick through the steps the script goes through and finish the display of the progress bar.

```
  1/100 [#...........................]   1% (00:00:16)
```

```
 64/100 [##################..........]  64% (00:00:07)
```

```
100/100 [############################] 100%
```

### Manually progressing

It is possible to tick through the steps of your scripts manually when the steps in your script cannot be looped.
Each tick adds one progression, but you can override the progression made by including an integer in `tick()`.

You do need to set `maxProgress` for the progress bar to display the correct numbers by including it in the constructor.
If you don't know the maxProgress during initialization, you can set it later with the `setMaxProgress()` method.

```php
$progressBar = new \NickBeen\ProgressBar\ProgressBar(maxProgress: 62);
$progressBar->start();

doSomething();
$progressBar->tick();

doSomethingElse();
$progressBar->tick();

doSixtyTasks();
$progressBar->tick(60);

$progressBar->finish();
```

If you have a little more structure in your step-by-step code, you can easily place `tick()` in a for loop.
There is however a more convenient method when dealing with e.g. arrays.

### Iterating through arrays or traversable instances

This class method works with anything of the pseudo-type [iterable](https://www.php.net/manual/en/language.types.iterable.php) which includes any array or any instance of [Traversable](https://www.php.net/manual/en/class.traversable.php).
The `iterate()` method automatically handles starting the progress bar, managing ticking through the iteration and finally finish displaying the progress bar.

```php
$array = [
    1 => 'A',
    2 => 'B',
    3 => 'C',
];

$progressBar = new \NickBeen\ProgressBar\ProgressBar();

foreach ($progressBar->iterate($array as $key => $value);) {
    echo "$key: $value" . PHP_EOL;
}
```

### Interact with the progress bar

It is possible to interact with the progress bar during its run.
You can retrieve the estimated time to finish, the progress it has made, the maximum progress that has been set and the amount of completion in percentage.
You can use this information e.g. for notifications or other tasks in the background.

```php
foreach ($progressBar->iterate($array);) {
    // Some custom notification
    sendToDiscord($progressBar->getEstimatedTime());

    // Some custom task application
    syncWithCloud($progressBar->getPercentage())

    // Some other custom application
    sendToRaspberryPiDisplay($progressBar->getProgress(), $progressBar->getMaxProgress())
}
```

## License

This library is licensed under the MIT License (MIT). See the [LICENSE](LICENSE.md) for more details.
