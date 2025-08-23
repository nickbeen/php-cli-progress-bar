# Changelog

All notable changes to `php-cli-progress-bar` will be documented in this file.

## [Unreleased](https://github.com/nickbeen/php-cli-progress-bar/compare/v1.0.0...HEAD)

## 1.1.3 - 2025-01-24

### Added
* Add support for PHP 8.4 tests by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/23
### Fixed
* Fix possible division by zero error by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/22

## 1.1.2 - 2024-06-20

### Fixed
* Fix 24 hour limit when displaying estimated time by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/20

## 1.1.1 - 2024-04-08

### Added
* Add support for PHP 8.2 tests by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/13
* Add support for PHP 8.3 tests by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/18
### Fixed
* Fix build status badge by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/12
* Implicit conversion from float-string to int is deprecated by @thisispiers in https://github.com/nickbeen/php-cli-progress-bar/pull/14
* Add full support for floating values without implicit conversion by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/16
* Fix non-countable warning for iterable values by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/17

## 1.1.0 - 2022-06-21

### Added
* Added ability to use custom bar characters by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/6
* Added ability to show and hide cursor by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/8
* Added ability to throttle display frequency by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/10
* Added configuration file for `php-cs-fixer` by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/5

### Changed
* Allow output during PHPUnit tests by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/2

### Removed
* Removed `.php-cs-fixer.cache` by @nickbeen in https://github.com/nickbeen/php-cli-progress-bar/pull/9

## 1.0.0 - 2022-06-18

-   Initial Release
