<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed username
 * @property mixed balance
 * @property mixed updated_at
 * @property mixed created_at
 * @property mixed avatar
 * @property mixed gender
 * @property mixed profession
 * @property mixed birthday
 * @property mixed biography
 * @property mixed mobile
 * @property mixed email
 * @property mixed wallet
 * @property mixed roles
 */
class User extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray( $request ): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "username" => $this->username,
            "email" => $this->email,
            "mobile" => $this->mobile,
            "biography" => $this->biography,
            "birthday" => $this->birthday,
            "profession" => $this->profession,
            "gender" => $this->gender,
            "avatar" => $this->avatar,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "wallet" => $this->wallet,
            "roles" => $this->roles,
        ];

    }

}
