<?php

namespace App\Modules\Catalog\Application;

interface FileReferenceValidator
{
    public function status(string $reference): FileReferenceStatus;
}
