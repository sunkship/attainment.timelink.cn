<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        DB::table('users')->insert([
            'username'      =>'admin',
            'passport_id'   =>'7',
            'password'      => bcrypt('12341234'),
        ]);
        DB::table('attainment')->insert([
            'user_id'   =>'1',
            'url'       =>'https://www.baidu.com/',
            'content'   =>'这是一个神奇的网站。',
            'created_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
