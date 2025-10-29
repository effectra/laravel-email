<?php

use Effectra\LaravelEmail\Enums\EmailTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->json('to');
            $table->string('from');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('replay_to')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->enum('type', enumToArray(EmailTypeEnum::class));
            $table->timestamp('sended_at')->nullable();
            $table->timestamps();

        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('attachments')->nullable();
            $table->json('to')->nullable();
            $table->string('from');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('replay_to')->nullable();
            $table->timestamps();
        });

        

        
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('email_templates');
        
    }
};