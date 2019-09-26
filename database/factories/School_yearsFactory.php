<?php

use Faker\Generator as Faker;

$factory->define(App\School_years::class, function (Faker $faker) {
    return [
        'name'			=> '2019',
		'description'	=> 'Sin descripcion',
		'admins_id' => function () {
            return factory(App\Admins::class)->create()->id;
        },
		'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
