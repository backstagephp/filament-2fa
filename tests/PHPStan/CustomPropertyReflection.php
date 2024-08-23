<?php

namespace Vormkracht10\TwoFactorAuth\Tests\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;

class CustomPropertyReflection implements PropertyReflection
{
    private $declaringClass;

    private $type;

    public function __construct(ClassReflection $declaringClass, Type $type)
    {
        $this->declaringClass = $declaringClass;
        $this->type = $type;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function isDeprecated(): bool
    {
        return false;
    }

    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    public function isInternal(): bool
    {
        return false;
    }

    public function getDocComment(): ?string
    {
        return null;
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
