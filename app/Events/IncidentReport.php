<?php

namespace App\Events;

use App\Models\Reporting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentReport implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $reportLog, $incidentReport;

    public function __construct()
    {
    }

    function approveStatus($accidentReportId)
    {
        $this->incidentReport = new Reporting;
        $this->incidentReport->find($accidentReportId)->update([
            'status' => 'Approved'
        ]);
    }

    function declineStatus($accidentReportId)
    {
        $this->incidentReport = new Reporting;
        $this->incidentReport->find($accidentReportId)->update([
            'status' => 'Declined'
        ]);
    }

    function revertIncidentReport($accidentReportId, $reportPhotoPath)
    {
        $this->incidentReport = new Reporting;
        $image_path = public_path('reports_image/' . $reportPhotoPath);

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $this->incidentReport->find($accidentReportId)->delete();
    }

    function confirmDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport = new Reporting;
        $this->incidentReport->find($dangerAreaId)->update([
            'status' => 'Confirmed'
        ]);
    }

    function removeDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport = new Reporting;
        $this->incidentReport->find($dangerAreaId)->update([
            'status' => "Archived",
            'is_archive' => 1
        ]);
    }

    function revertDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport = new Reporting;
        $this->incidentReport->find($dangerAreaId)->delete();
    }

    public function broadcastOn()
    {
        return new Channel('incident-report');
    }
}
