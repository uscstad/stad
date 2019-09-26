<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = App\User::create([
            'type_doc'      => 'cc',
            'doc'           => '31221521',
            'name'          => 'Andres',
            'lastname'      => 'Parra',
            'user'          => 'andres01',
            'email'         => 'admin@hotmail.com',
            'password'      => bcrypt('123456'),
            'address'       => 'Calle 31 # 45-25',
            'mobile'        => '3006858893',
            'phone'         => '3282255',
            'type'          => 'admins',
            'status'        => 1,
            'deleted'       => 0,   
            'created_at'    => new DateTime,
            'updated_at'    => new DateTime,
        ]);
        //
        $admin = App\Admins::create([
            'users_id'      => $user->id,
        ]);
        //
        App\Configs::create([
            'admins_id'     => $admin->id,
        ]);

    }
}
