<?php

namespace Effectra\LaravelEmail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailTemplate
 *
 * Represents a Email Template.
 *
 * @package Effectra\LaravelEmail\Models
 *
 * @property int|string $id
 * @property string|null $subject
 * @property string|null $body
 * @property array|null $attachments
 * @property string[]|null $to
 * @property string $from
 * @property string[]|null $cc
 * @property string[]|null $bcc
 * @property string[]|null $replay_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EmailTemplate extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_templates';
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
    ];

    public function emails()
    {
        return $this->hasMany(config('email-message.models.template'));
    }
}