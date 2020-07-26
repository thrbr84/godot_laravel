<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ret = [
            'value' => $this->value
        ];
        return $ret;
    }
}
