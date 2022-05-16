<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return[
            'id' =>$this->id,
            'user_id' =>$this->user_id,
            'name' =>$this->user->name,
            'email' =>$this->user->email,
            'gender' =>$this->gender,
            'city' =>$this->city,
            'bio' =>$this->bio,
        ];
    }
}
