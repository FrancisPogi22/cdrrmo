<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportLog extends Model
{
    use HasFactory;
    protected $table = 'report_log';

    protected $primaryKey = 'report_id';

    protected $guarded = [];

    protected $fillable = [
        'user_ip',
        'attempt',
        'report_time',
    ];
}