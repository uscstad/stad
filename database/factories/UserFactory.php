<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
    	'type_doc' => 'cc',
    	'doc' => rand(0, 10),
        'name' => $faker->name,
        'lastname' => $faker->lastname,
        'user' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$LP6pQ0U4B7jfD9NYFjiCKutih36P35NVYgRnyEa0KtPYmh8VZGbMO', // secret
        'address' => $faker->address,
        'mobile' => $faker->e164PhoneNumber,
        'phone' => $faker->phoneNumber ,
        'rol' => Str::random('[{"name":"admins"}]','[{"name":"cordinadors"}]','[{"name":"teachers"}]','[{"name":"auxiliares"}]'),
        'remember_token' => Str::random(10),
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
