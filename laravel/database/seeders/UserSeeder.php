<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function generateRandomNumber($length = 5)
    {
        return substr(str_shuffle(str_repeat($x = '1234567890', ceil($length / strlen($x)))), 1, $length);
    }

    public function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil($length / strlen($x)))), 1, $length);
    }

    public function run()
    {
        //
        for ($i = 0; $i < 10; $i++) {
            $npp_supervisor = User::where('npp_supervisor', null)->inRandomOrder()->first()->npp;

            $create = User::insert([
                'nama' => $this->generateRandomString(7),
                'email' => $this->generateRandomString(5) . "@mail.com",
                'password' => Hash::make("P@ssW0rd!"),
                'npp' => $this->generateRandomNumber(5),
                'npp_supervisor' => $npp_supervisor
            ]);
        }
    }
}
