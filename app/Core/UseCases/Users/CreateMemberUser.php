<?php

namespace App\Core\UseCases\Users;

use App\Models\User;

class CreateMemberUser {
    public function execute($data){
        $user = User::create($data);
        $user->assignRole('member');
        $user->save();

        return $user;
    }
}