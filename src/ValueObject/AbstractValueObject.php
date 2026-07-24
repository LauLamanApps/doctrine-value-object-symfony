<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\ValueObject;

/**
 * @method mixed sanitize(mixed $value)
 * @method bool  isValid(mixed $value)
 */
abstract class AbstractValueObject implements \Stringable
{
    /** @var mixed */
    protected $value;

    public function __construct(mixed $value, bool $check = true)
    {
        $this->value = $this->sanitize($value);

        if ($check) {
            $this->isValid($this->value);
        }
    }

    abstract public function getValue(): int|string;

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
