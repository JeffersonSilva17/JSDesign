<?php

namespace App\Modules\Catalog\Infrastructure\Files;

use App\Modules\Catalog\Application\FileReferenceStatus;
use App\Modules\Catalog\Application\FileReferenceValidator;

final class FailClosedFileReferenceValidator implements FileReferenceValidator
{
    public function status(string $reference): FileReferenceStatus
    {
        return FileReferenceStatus::Rejected;
    }
}
