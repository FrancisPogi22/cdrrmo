<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $fillable = [
        'admin_email',
        'user_role',
        'password',
        'created_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
