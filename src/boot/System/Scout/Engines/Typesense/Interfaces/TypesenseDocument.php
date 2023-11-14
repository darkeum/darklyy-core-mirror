<?php

namespace Boot\System\Scout\Engines\Typesense\Interfaces;

/**
 * Interface TypesenseSearch
 *
 * @package Boot\System\Scout\Engines\Typesense\Interfaces
 */
interface TypesenseDocument
{
    public function typesenseQueryBy(): array;

    public function getCollectionSchema(): array;
}
