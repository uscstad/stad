<?php

use Faker\Generator as Faker;

$factory->define(App\Tasks::class, function (Faker $faker) {
    return [
        'name'				=> $faker->catchPhrase,
        'description'		=> $faker->text,
        'type'				=> 'individuals',
        'coordinators_id' 	=> function () {
            return factory(App\Coordinators::class)->create()->id;
        },
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
