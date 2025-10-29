<?php

use Effectra\LaravelEmail\Models\EmailMessage;
use Effectra\LaravelEmail\Enums\EmailTypeEnum;
use Illuminate\Support\Carbon;

test('it model has correct factory class', function () {
    $model = new EmailMessage();
    $factory = $model->factory();
    expect($factory)->toBeInstanceOf(\Effectra\LaravelEmail\Database\Factories\EmailMessageFactory::class);
    $model::truncate();
});



it('can be created using the factory', function () {
    $email = EmailMessage::factory()->create();

    expect($email)->toBeInstanceOf(EmailMessage::class)
        ->and($email->subject)->not->toBeEmpty()
        ->and($email->to)->toBeArray();
});

it('casts attributes correctly', function () {
    $email = EmailMessage::factory()->create([
        'attachments' => ['file1.pdf', 'file2.png'],
        'to' => ['test@example.com'],
        'type' => EmailTypeEnum::INTERNAL,
        'sended_at' => Carbon::now(),
    ]);

    expect($email->attachments)->toBeArray()
        ->and($email->to)->toBeArray()
        ->and($email->type)->toBeInstanceOf(EmailTypeEnum::class)
        ->and($email->sended_at)->toBeInstanceOf(Carbon::class);
});

it('filters emails by internal type using scope', function () {
    $internal = EmailMessage::factory()->create(['type' => EmailTypeEnum::INTERNAL]);
    $external = EmailMessage::factory()->create(['type' => EmailTypeEnum::EXTERNAL]);

    $found = EmailMessage::internalType()->get();

    expect($found->contains($internal))->toBeTrue()
        ->and($found->contains($external))->toBeFalse();
});

it('filters emails by external type using scope', function () {
    $internal = EmailMessage::factory()->create(['type' => EmailTypeEnum::INTERNAL]);
    $external = EmailMessage::factory()->create(['type' => EmailTypeEnum::EXTERNAL]);

    $found = EmailMessage::externalType()->get();

    expect($found->contains($external))->toBeTrue()
        ->and($found->contains($internal))->toBeFalse();
});
