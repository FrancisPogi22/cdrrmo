<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentReport extends Model
{
    use HasFactory;

    protected $table = 'resident_report';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'photo',
        'status',
        'user_ip',
        'details',
        'latitude',
        'longitude',
        'is_archive',
        'report_time',
        'notification'
    ];

    public $timestamps = false;

    public function getReportCount()
    {
        return [
            'todayReport'     => $this->whereRaw('DATE(report_time) >= CURDATE()')->count(),
            'resolvingReport' => $this->where('status', 'Resolving')->whereRaw('DATE(report_time) >= CURDATE()')->count(),
            'resolvedReport'  => $this->where('status', 'Resolved')->whereRaw('DATE(report_time) >= CURDATE()')->count()
        ];
    }
}
