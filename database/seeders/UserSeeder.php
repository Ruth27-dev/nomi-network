<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super.admin@gmail.com',
                'phone' => '092828282',
                'password' => bcrypt('1'),
                'status' => 1,
                'role' => config('dummy.user.role.super_admin'),
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('1'),
                'phone' => '088921893',
                'role' => config('dummy.user.role.admin'),
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ],
            [
                'branch_id' => 1,
                'name' => 'Chea Varin',
                'email' => 'varin@gmail.com',
                'password' => bcrypt('1'),
                'phone' => '088123893',
                'role' => config('dummy.user.role.admin'),
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ],
            [
                'branch_id' => 1,
                'name' => 'Hong Lyhean',
                'email' => 'lyhean@gmail.com',
                'password' => bcrypt('1'),
                'phone' => '017951364',
                'role' => config('dummy.user.role.admin'),
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ],
            [
                'branch_id' => 1,
                'name' => 'Man Vannda',
                'email' => 'man.vannda@gmail.com',
                'password' => bcrypt('1'),
                'phone' => '017902389',
                'role' => null,
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ],
            [
                'branch_id' => 1,
                'name' => 'G-Devid',
                'email' => 'g.devid@gmail.com',
                'password' => bcrypt('1'),
                'phone' => '017902388',
                'role' => null,
                'status' => 'ACTIVE',
                'remember_token' => Str::random(10),
            ]
        ];
        foreach ($users as $key => $user) {
            $exist = User::where('email', $user['email'])->orWhere('phone', $user['phone'])->exists();
            if(!$exist){
                User::create($user);
            }
        }
    }
}
