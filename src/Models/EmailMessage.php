<?php

namespace Effectra\LaravelEmail\Models;

use Effectra\LaravelEmail\Database\Factories\EmailMessageFactory;
use Effectra\LaravelEmail\Enums\EmailTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailMessage
 *
 * Represents a Email Message.
 *
 * @package Effectra\LaravelEmail\Models
 *
 * @property int|string $id
 * @property string $subject
 * @property string $body
 * @property array|null $attachments
 * @property string[] $to
 * @property string $from
 * @property string[]|null $cc
 * @property string[]|null $bcc
 * @property string[]|null $replay_to
 * @property int|string|null $template_id
 * @property \Effectra\LaravelEmail\Enums\EmailTypeEnum $type
 * @property \Illuminate\Support\Carbon|null $sended_at
 * 
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EmailMessage extends Model
{
    /** @use HasFactory<\Effectra\LaravelEmail\Database\Factories\EmailMessageFactory> */
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_messages';
    /** 
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject',
        'body',
        'attachments',
        'to',
        'from',
        'cc',
        'bcc',
        'replay_to',
        'template_id',
        'type',
        'sended_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attachments' => 'array',
        'to' => 'array',
        'from' => 'string',
        'cc' => 'array',
        'bcc' => 'array',
        'replay_to' => 'array',
        'type' => EmailTypeEnum::class,
        'sended_at' => 'datetime',
    ];


    protected static function newFactory()
    {
        return EmailMessageFactory::new();
    }

    public function template()
    {
        return $this->belongsTo(config('email-message.models.template'));
    }

    public function scopeInternalType($query)
    {
        return $query->where('type', EmailTypeEnum::INTERNAL);
    }

    public function scopeExternalType($query)
    {
        return $query->where('type', EmailTypeEnum::EXTERNAL);
    }
}