<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\JsonResource;

class PostResourceWithJsonOptions extends JsonResource
{
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
