<?php

    use App\Attachment;
    use Faker\Generator as Faker;


    if (isset($factory)) {
        $factory->define(Attachment::class, function (Faker $faker) {
            $image = $faker->image;
            return [
                'name' => $faker->name,
                'path' => $image,
                'extension' => $faker->fileExtension
            ];
        });
    }
