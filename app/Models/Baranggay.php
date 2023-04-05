<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baranggay extends Model
{
    use HasFactory;

    protected $table = 'baranggay';

    protected $primaryKey = 'baranggay_id';

    protected $guarded = [];

    protected $fillable = [
        'baranggay_id',
        'baranggay_label',
    ];

    public $timestamps = false;
}