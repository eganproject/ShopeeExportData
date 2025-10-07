<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'action',
        'description',
        'route',
        'url',
        'method',
        'ip',
        'user_agent',
        'payload',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
