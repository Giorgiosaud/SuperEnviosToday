<?php

/** @var Factory $factory */

use App\Attachment;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\File;

$factory->define(Attachment::class, function (Faker $faker) {
    $filepath = storage_path('app/public/images/test');
    if(!File::exists($filepath)){
        File::makeDirectory($filepath);
    }

    $image=  $faker->image($filepath,640,480, null, false);
    return [
        'name'      => $faker->name,
        'path'      => $filepath.'/'.$image,
        'extension' => $faker->fileExtension,
    ];
});
