<?php

namespace Effectra\LaravelEmail\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
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
        'bb',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attachments' => 'array',
    ];

    public function emails()
    {
        return $this->hasMany(config('email-message.models.template'));
    }
}