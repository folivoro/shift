# folivoro/shift

Keeps your WordPress projects up to date with Sloth. Automatically.

## Install

```bash
composer require --dev folivoro/shift
```

## Use

```bash
vendor/bin/rector process app/ --config vendor/folivoro/shift/config/shift.php
```

## Composer script

Add to `composer.json`:

```json
{
    "scripts": {
        "shift": "rector process app/ --config vendor/folivoro/shift/config/shift.php"
    }
}
```

Then run:

```bash
composer shift
```

## Rules

| Rule | Description |
|------|-------------|
| `NormalizeSlothRegistrationPropertiesRector` | Normalizes Sloth registration properties to `public static` without type declaration |