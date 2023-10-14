<?php

namespace QuantaForge\Tests\Integration\Http\Fixtures;

use QuantaForge\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray($request)
    {
        return ['name' => $this->name];
    }
}
