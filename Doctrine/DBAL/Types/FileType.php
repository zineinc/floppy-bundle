<?php

namespace ZineInc\StorageBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use ZineInc\Storage\Common\FileId;

class FileType extends Type
{
    const DEFAULT_LENGTH = 45;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if(empty($fieldDeclaration['length'])) {
            $fieldDeclaration['length'] = self::DEFAULT_LENGTH;
        }

        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : $value->id();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : new FileId($value);
    }

    public function getDefaultLength(AbstractPlatform $platform)
    {
        return self::DEFAULT_LENGTH;
    }

    public function getName()
    {
        return 'file';
    }
}