<?php

  namespace Database\Factories;

  use App\Models\Attachment;
  use Illuminate\Database\Eloquent\Factories\Factory;
  use Illuminate\Support\Facades\File;

  class AttachmentFactory extends Factory
  {
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      $filepath = storage_path('app/public/images/test');
      if (!File::exists($filepath)) {
        File::makeDirectory($filepath);
      }

      $image = $this->faker->image($filepath, 640, 480, null, false);
      return [
        'name' => $this->faker->name,
        'path' => $filepath . '/' . $image,
        'extension' => $this->faker->fileExtension,
      ];
    }

  }

