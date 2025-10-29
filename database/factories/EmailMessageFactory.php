<?php

namespace Effectra\LaravelEmail\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;


class EmailMessageFactory extends Factory
{
    protected $model = \Effectra\LaravelEmail\Models\EmailMessage::class;

    public function definition()
    {
        $faker = Faker::create();

        return [
            'subject' => $faker->sentence(),
            'body' => $faker->paragraph(),
            'attachments' => [$faker->filePath()],
            'to' => [$faker->safeEmail()],
            'from' => $faker->safeEmail(),
            'cc' => [$faker->safeEmail()],
            'bcc' => [$faker->safeEmail()],
            'replay_to' => [$faker->safeEmail()],
            'template_id' => $faker->optional()->randomNumber(),
            'type' => \Effectra\LaravelEmail\Enums\EmailTypeEnum::INTERNAL,
            'sended_at' => $faker->optional()->dateTime(),
        ];
    }

}