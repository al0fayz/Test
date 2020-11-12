<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
                'id'        => 1,
                'username'  => 'admin',
                'email'     => 'admin@me.com',
                'password'  => Hash::make('abc123'),
            ],
        ];
        DB::table('users')->insert($user);
        $detail = [
            [
                'user_id'        => 1,
                'first_name'        => 'admin',
                'last_name'     => 'saja',
                'jenis_kelamin'  => 'L',
                'tanggal_lahir' => Carbon::now()
            ]
            ];
        DB::table('detail_user')->insert($detail);
    }
}
