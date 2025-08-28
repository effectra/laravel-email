<?php

namespace Effectra\LaravelEmail\Models;

use Effectra\LaravelStatus\Enums\EmailTypeEnum;
use Illuminate\Database\Eloquent\Model;

class EmailMessage extends Model
{
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
        'bb',
        'template_id',
        'type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attachments' => 'array',
        'type' => EmailTypeEnum::class,
    ];

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