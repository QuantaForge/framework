<?php

namespace QuantaForge\Tests\Integration\Http\Fixtures;

use QuantaForge\Http\Resources\Json\JsonResource;

class PostResourceWithOptionalHasAttributes extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first' => $this->whenHas('is_published'),
            'second' => $this->whenHas('is_published', 'override value'),
            'third' => $this->whenHas('is_published', function () {
                return 'override value';
            }),
            'fourth' => $this->whenHas('is_published', $this->is_published, 'default'),
            'fifth' => $this->whenHas('is_published', $this->is_published, function () {
                return 'default';
            }),
        ];
    }
}
