<?php

use Faker\Generator as Faker;

$factory->define(App\Tasks_contents::class, function (Faker $faker) {
    return [
        'start_date'			=> date('Y-m-d H:i:s'),
        'final_date'			=> date('Y-m-d H:i:s'),
        'teachers_id'			=> function () {
            return factory(App\Teachers::class)->create()->id;
        },
        'teaching_periods_id'	=> function () {
            return factory(App\Teaching_periods::class)->create()->id;
        },
        'tasks_id'				=> function () {
            return factory(App\Tasks::class)->create()->id;
        },
        'created_at'    => new DateTime,
        'updated_at'    => new DateTime,
    ];
});
