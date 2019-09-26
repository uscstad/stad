<?php

use Faker\Generator as Faker;

$factory->define(App\Admins::class, function (Faker $faker) {
    return [
        'users_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
