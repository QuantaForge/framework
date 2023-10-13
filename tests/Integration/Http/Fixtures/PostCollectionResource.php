<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\ResourceCollection;

class PostCollectionResource extends ResourceCollection
{
    public $collects = PostResource::class;

    public function toArray($request)
    {
        return ['data' => $this->collection];
    }
}
