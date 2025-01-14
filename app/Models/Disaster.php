<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disaster extends Model
{
    use HasFactory;

    protected $table = 'disaster';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'type',
        'year',
        'status',
        'is_archive'
    ];

    public $timestamps = false;
}
