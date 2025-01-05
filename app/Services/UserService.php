<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function generateUser()
    {
        $name = 'user' . substr(md5(uniqid()), 0, 6);
        $user = User::create([
            'username' => $name,
        ]);
        return $user;
    }
}
