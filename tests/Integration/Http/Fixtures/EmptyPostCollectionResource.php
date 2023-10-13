<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\ResourceCollection;

class EmptyPostCollectionResource extends ResourceCollection
{
    public $collects = PostResource::class;
}
