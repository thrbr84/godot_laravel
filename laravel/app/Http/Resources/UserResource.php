<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // User save data
        $user = User::where("id", $this->id)->with(['save_data'])->first();
        $user_save_data = (new UserSaveResource($user->save_data))['value'];

        $ret = [
            'id' => $this->id,
            'name' => ucfirst(mb_strtolower($this->name)),
            'full_name' => $user->full_name,
            'codename' => ucfirst(mb_strtolower($this->codename)),
            'lastname' => ucfirst(mb_strtolower($this->lastname)),
            'email' => mb_strtolower($this->email),
            'save_data' => $user_save_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        return $ret;
    }
}
