<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = [
        'session_id',
        'status',
        'advisor_id',
        'last_user_message_at',
        'closed_at',
    ];

    protected $casts = [
        'last_user_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
