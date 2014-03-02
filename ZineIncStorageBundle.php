<?php

namespace ZineInc\StorageBundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZineIncStorageBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if($this->container->getParameter('zineinc.storage.enable_doctrine_file_type')) {
            if(Type::hasType('file')) {
                Type::addType('file', 'ZineInc\StorageBundle\Doctrine\DBAL\Types\FileType');
            }

            if($this->container->has('doctrine.dbal.default_connection')) {
                $platform = $this->container->get('doctrine.dbal.default_connection')->getDatabasePlatform();
                $platform->markDoctrineTypeCommented(Type::getType('file'));
            }
        }
    }
}