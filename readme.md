# EnvDiff


EnvDiff is composer event tool to compare environment keys to find the difference between .env files

```bash
$ composer require ytake/envdiff
```

## usage
[composer Scripts](https://getcomposer.org/doc/articles/scripts.md)
composer event

```json
  "scripts": {
    "post-update-cmd": "Ytake\\EnvDiff\\EnvScript::postUpdate"
  }
```

or custom commands

example)
```json
  "scripts": {
    "check": "Ytake\\EnvDiff\\EnvScript::postUpdate"
  }
```

### optional argument
#### force
example)
```bash
$ composer check -- --force
```
