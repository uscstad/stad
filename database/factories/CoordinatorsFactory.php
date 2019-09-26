<?php

use Faker\Generator as Faker;

$factory->define(App\Coordinators::class, function (Faker $faker) {
    return [
    	'specialty' => $faker->title,
    	'profession' => $faker->title,
        'users_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'admins_id' => function () {
            return factory(App\Admins::class)->create()->id;
        },
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
