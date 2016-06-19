# EnvDiff
[![Build Status](http://img.shields.io/travis/ytake/EnvDiff/master.svg?style=flat-square)](https://travis-ci.org/ytake/EnvDiff)
[![Coverage Status](http://img.shields.io/coveralls/ytake/EnvDiff/master.svg?style=flat-square)](https://coveralls.io/r/ytake/EnvDiff?branch=master)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/EnvDiff.svg?style=flat-square)](https://scrutinizer-ci.com/g/ytake/EnvDiff/?branch=master)
[![StyleCI](https://styleci.io/repos/61364639/shield)](https://styleci.io/repos/61364639)

EnvDiff is composer event tool to compare environment keys to find the difference between .env files

```bash
$ composer require ytake/envdiff
```

## usage
[composer Scripts](https://getcomposer.org/doc/articles/scripts.md)
composer event

```json
  "scripts": {
    "post-update-cmd": "Ytake\\EnvDiff\\EnvScript::envDiff"
  }
```

or custom commands

example)
```json
  "scripts": {
    "check": "Ytake\\EnvDiff\\EnvScript::envDiff"
  }
```

### optional argument
#### force
example)
```bash
$ composer check -- --force
```
