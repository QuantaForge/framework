<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\JsonResource;

class SerializablePostResource extends JsonResource
{
    public function toArray($request)
    {
        return new JsonSerializableResource($this);
    }
}
