# laulamanapps/doctrine-value-object-symfony

Typed **value objects** and a **dedicated ID type per entity** for Doctrine/Symfony — with
matching Doctrine column types and **zero-config auto-registration**. You never pass a raw
`int` around again; you pass a `PersonId`, a `CurriculumId`, an `OrganizationId`.

Why: dedicated ID types make signatures self-documenting and make it impossible to
accidentally pass a person's id where a curriculum's is expected. Value objects keep
validation/normalisation next to the data.

## Requirements

- PHP 8.4+
- Symfony 7.1+ / 8.x
- Doctrine DBAL 4 + DoctrineBundle (2.13+/3.x)

## Installation

```bash
composer require laulamanapps/doctrine-value-object-symfony
```

Register the bundle (Symfony Flex does this automatically):

```php
// config/bundles.php
return [
    // ...
    LauLamanApps\DoctrineValueObject\DoctrineValueObjectBundle::class => ['all' => true],
];
```

That's it — the bundle registers the compiler pass that auto-registers your Doctrine types.
No `Kernel::build()` wiring, no `doctrine.dbal.types` config.

## Usage

### 1. A dedicated ID per entity

The ID is a tiny class extending the shared base — declare a `NAME` and that's the whole class.
No separate Doctrine type per ID:

```php
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractId;

final class PersonId extends AbstractId
{
    public const string NAME = 'person_id';
}
```

Map the entity column to that `NAME` — nothing else to configure:

```php
#[ORM\Id]
#[ORM\Column(type: 'person_id')]
#[ORM\GeneratedValue(strategy: 'AUTO')]
private PersonId $id;
```

Every ID is served by the shared `Doctrine\IdType`: the compiler pass registers each
`AbstractId` subclass' `NAME` against it, and at runtime it resolves the concrete ID class back
from the name it was registered under. It converts both ways (`new PersonId($value)` ⇄
`$id->getValue()`), uses an integer column, and refuses to persist an ID that fails its own
`isValid()` (throwing `ValueObjectIsNotSafeToPersistException`). An `AbstractId` subclass without
a `NAME` is simply not registered — handy for IDs you never persist as a column.

### 2. Rich value objects

Extend the integer or string base and implement `sanitize()` / `isValid()`:

```php
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectString;

final class EmailAddress extends AbstractValueObjectString
{
    public function sanitize(string $value): string
    {
        return strtolower(trim($value));
    }

    public function isValid(string $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
```

Rich value objects still get a small `AbstractValueObjectType` subclass — the only thing it needs
is a `NAME` and the class it maps:

```php
use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;

final class EmailAddressType extends AbstractValueObjectType
{
    public const string NAME = 'email_address';

    public function getClassName(): string
    {
        return EmailAddress::class;
    }
}
```

`AbstractValueObjectType` picks the SQL column type (string vs integer) from the value object,
converts both ways, and refuses to persist a value object that fails its own `isValid()`. Map
columns with `#[ORM\Column(type: 'email_address')]`.

### 3. Backed-enum columns

```php
use LauLamanApps\DoctrineValueObject\Doctrine\AbstractPhpEnumType;

final class StatusType extends AbstractPhpEnumType
{
    public const string NAME = 'status';

    protected function getEnumClassName(): string
    {
        return Status::class; // an enum: string / enum: int
    }
}
```

## Auto-registration — how it works

The `DoctrineApplicationTypeRegisterCompilerPass` scans loaded classes and registers each with
Doctrine under its `NAME`:

- every `AbstractId` subclass that declares a `NAME` → the shared `Doctrine\IdType`;
- every `AbstractValueObjectType` / `AbstractPhpEnumType` subclass → itself, under its own `NAME`.

It relies on those classes being **loaded** at container-compile time — the standard `App\`
service loader (`resource: '../src/'`) does this by reflecting over `src/`, so no extra step is
needed. A dedicated type without a non-empty `NAME` constant fails fast with a clear error; an
`AbstractId` without a `NAME` is skipped (not every ID is persisted).

## What's in the box

| Class | Role |
|---|---|
| `ValueObject\AbstractValueObject` | Base value object (`sanitize` / `isValid` / `__toString`) |
| `ValueObject\AbstractValueObjectInteger` · `AbstractValueObjectString` | Typed int / string value objects |
| `ValueObject\AbstractId` | Integer-id value object base (what every `*Id` extends); declare a `NAME` to auto-map it |
| `Doctrine\IdType` | The one shared Doctrine type serving every `AbstractId` by `NAME` — no per-ID type class |
| `Doctrine\AbstractValueObjectType` | Doctrine type mapping a (non-ID) value object ↔ column |
| `Doctrine\AbstractPhpEnumType` | Doctrine type for backed enums |
| `Doctrine\Exception\ValueObjectIsNotSafeToPersistException` | Thrown when an invalid VO would be persisted |
| `DependencyInjection\Compiler\DoctrineApplicationTypeRegisterCompilerPass` | The zero-config registrar (wired by the bundle) |

## Development

```bash
composer install
composer test      # PHPUnit
composer phpstan   # PHPStan (level max)
```

## License

MIT — see [LICENSE](LICENSE).
