<?php

declare (strict_types = 1);

namespace Base\Document;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class DefaultDocumentRepository extends DocumentRepository
{
    public function isCustomDefaultDocumentRepository(): bool
    {
        return true;
    }
}