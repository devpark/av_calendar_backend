<?php

namespace App\Http\Transformers;

use App\Models\User as U;

class User extends AbstractTransformer
{
    /**
     * Transform User object into array
     *
     * @param U $user
     *
     * @return array
     */
    public function transform(U $user)
    {
        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role_id' => $user->role_id,
            'avatar' => $user->avatar,
            'deleted' => (bool) $user->deleted,
        ];
        
        $data = $this->transformRelations($data, $user);
        
        return $data;
    }
}
