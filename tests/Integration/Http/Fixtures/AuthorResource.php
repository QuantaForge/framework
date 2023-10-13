<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray($request)
    {
        return ['name' => $this->name];
    }
}
