<?php

namespace App\Repositiries;

use App\Models\User;

class UserRepositry
{
    public function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'name' => $data['name'],
        ]);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now() , 'is_verified' => 1]);
    }
}
