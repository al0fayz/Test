<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'username'  => 'admin',
                'email'     => 'admin@me.com',
                'password'  => Hash::make('abc123'),
            ],
        ];
        DB::table('users')->insert($user);
    }
}
