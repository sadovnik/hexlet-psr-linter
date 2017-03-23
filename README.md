# PSR linter
[![Build Status](https://travis-ci.org/sadovnik/hexlet-psr-linter.svg?branch=master)](https://travis-ci.org/sadovnik/hexlet-psr-linter)
[![Code Climate](https://codeclimate.com/github/sadovnik/hexlet-psr-linter/badges/gpa.svg)](https://codeclimate.com/github/sadovnik/hexlet-psr-linter)
[![Test Coverage](https://codeclimate.com/github/sadovnik/hexlet-psr-linter/badges/coverage.svg)](https://codeclimate.com/github/sadovnik/hexlet-psr-linter/coverage)
[![Issue Count](https://codeclimate.com/github/sadovnik/hexlet-psr-linter/badges/issue_count.svg)](https://codeclimate.com/github/sadovnik/hexlet-psr-linter)

This is a linter for PHP with aim to implement all possible [PSR](http://www.php-fig.org/psr/) rules.

This project was originally started as part of [Hexlet](https://hexlet.io)'s traineeship, but actually developed a long time after. However the `hexlet-` prefix remains unchanged.

Thanks to [Roman Lakhtadyr](https://github.com/pldin601) for review. 🙏🏻

## Installation
You can install the package globally:

    composer global require sadovnik/hexlet-psr-linter

or locally:

    composer require sadovnik/hexlet-psr-linter

## Usage

    psr-linter [--fix] [--debug] <path>
    psr-linter (-h | --help)
    psr-linter --version

### Example

    psr-linter src/Symfony/Component/HttpKernel/Bundle/Bundle.php --fix

## Roadmap
- [x] Initiate boilerplate
- [x] Function name rule
- [x] Directory support
- [ ] Variable name rule
- [x] «Either side-effects or definitions» rule
- [x] Autofix using `--fix` flag
- [ ] JSON/YML output
- [ ] Make an interface for user rules
- [ ] Make a useful explanation of rules here in readme
- [ ] Make a friendly demo with cli usage
- [ ] Website with online linter
