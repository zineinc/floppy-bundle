<?php

namespace Floppy\Bundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Floppy\Common\FileId;

class FileType extends Type
{
    const DEFAULT_LENGTH = 45;

    private static $name = 'floppy_file';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if(empty($fieldDeclaration['length'])) {
            $fieldDeclaration['length'] = self::DEFAULT_LENGTH;
        }

        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (empty($value) || !$value->id()) ? null : $value->id();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return empty($value) ? null : new FileId($value);
    }

    public function getDefaultLength(AbstractPlatform $platform)
    {
        return self::DEFAULT_LENGTH;
    }

    public function getName()
    {
        return self::$name;
    }

    public static function setName($name)
    {
        self::$name = $name;
    }
}