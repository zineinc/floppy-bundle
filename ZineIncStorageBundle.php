<?php

namespace ZineInc\StorageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZineIncStorageBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        //TODO: register Doctrine FileType
    }
}