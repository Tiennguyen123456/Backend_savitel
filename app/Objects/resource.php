<?php

namespace App\Http\Resources--ResourcePath--;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class --ResourceName-- extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->attrOnly = [

        ];

        $this->attrMores = [

        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
