<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Database\Eloquent\Model::unguard();
        \App\models\User::create([
            'name' => 'Anto',
            'email' => 'a@b.com',
            'password' => bcrypt('123456')
        ]);
        \Illuminate\Database\Eloquent\Model::reguard();
    }
}
