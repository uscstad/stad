<?php

use Faker\Generator as Faker;

$factory->define(App\Teaching_periods::class, function (Faker $faker) {
    return [
        'name'				=> 'Periodo ',
		'description'	    => 'Sin descripcion',
		'start_date' 	    => date('Y-m-d'),
		'final_date' 	    => date('Y-m-d'),
        'school_years_id' => function () {
            return factory(App\School_years::class)->create()->id;
        },
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
