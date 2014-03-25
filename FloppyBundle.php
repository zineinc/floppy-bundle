<?php

namespace Floppy\Bundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Floppy\Bundle\Doctrine\DBAL\Types\FileType;

class FloppyBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if($this->container->getParameter('floppy.enable_doctrine_file_type')) {
            $name = $this->container->getParameter('floppy.doctrine_file_type_name');

            if(!Type::hasType($name)) {
                FileType::setName($name);
                Type::addType($name, 'Floppy\Bundle\Doctrine\DBAL\Types\FileType');
            }

            if($this->container->has('doctrine.dbal.default_connection')) {
                $platform = $this->container->get('doctrine.dbal.default_connection')->getDatabasePlatform();
                $platform->markDoctrineTypeCommented(Type::getType($name));
            }
        }
    }
}