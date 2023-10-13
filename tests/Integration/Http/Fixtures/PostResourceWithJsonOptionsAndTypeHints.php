<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\JsonResource;

class PostResourceWithJsonOptionsAndTypeHints extends JsonResource
{
    public function __construct(Post $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'reading_time' => $this->reading_time,
        ];
    }

    public function jsonOptions()
    {
        return JSON_PRESERVE_ZERO_FRACTION;
    }
}
