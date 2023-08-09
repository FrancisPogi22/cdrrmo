<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReportLog;
use App\Models\Reporting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\IncidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class IncidentReportController extends Controller
{

    private $report, $reportLog, $logActivity, $incidentReport;

    function __construct()
    {
        $this->reportLog = new ReportLog;
        $this->report = new IncidentReport;
        $this->incidentReport = new Reporting;
        $this->logActivity = new ActivityUserLog;
    }

    public function displayPendingIncidentReport()
    {
        $pendingReport = $this->incidentReport->where('status', 'On Process')->get();

        return DataTables::of($pendingReport)
            ->addIndexColumn()
            ->addColumn('status', '<div class="status-container"><div class="status-content bg-warning">On Process</div></div>')
            ->addColumn('action', function ($row) {
                if ($row->user_ip == request()->ip() && !auth()->check())
                    return '<button class="btn-table-remove revertIncidentReport"><i class="bi bi-arrow-counterclockwise"></i>Revert</button>';

                if (auth()->check()) {
                    if (auth()->user()->is_disable == 0)
                        return '<div class="action-container">' .
                            '<button class="btn-table-submit approveIncidentReport"><i class="bi bi-check-circle-fill"></i>Approve</button>' .
                            '<button class="btn-table-remove declineIncidentReport"><i class="bi bi-x-circle-fill"></i>Decline</button>' .
                            '</div>';
                }

                return '<span class="message-text">Currently Disabled.</span>';
            })->addColumn('photo', function ($row) {
                return '<div class="photo-container"><img id="actualPhoto" src="' . asset('reports_image/' . $row->photo) . '"></img></div>';
            })
            ->rawColumns(['status', 'action', 'photo'])
            ->make(true);
    }

    public function displayIncidentReport()
    {
        $incidentReport = $this->incidentReport->whereNotIn('status', ["On Process"])->where('is_archive', 0)->get();

        return DataTables::of($incidentReport)
            ->addIndexColumn()->addColumn('status', function ($row) {
                $color = match ($row->status) {
                    'Approved' => 'success',
                    'Declined' => 'danger'
                };

                return '<div class="status-container"><div class="status-content bg-' . $color . '">' . $row->status . '</div></div>';
            })->addColumn('action', function () {
                return auth()->user()->is_disable == 0 ? '<button class="btn-table-remove removeIncidentReport"><i class="bi bi-trash3-fill"></i>Remove</button>' : '<span class="message-text">Currently Disabled.</span>';
            })->addColumn('photo', function ($row) {
                return '<div class="photo-container"><img id="actualPhoto" src="' . asset('reports_image/' . $row->photo) . '"></div>';
            })
            ->rawColumns(['status', 'action', 'photo'])
            ->make(true);
    }

    public function createIncidentReport(Request $request)
    {
        $validatedIncidentReport = Validator::make($request->all(), [
            'description' => 'required',
            'location' => 'required',
            'photo' => 'image|mimes:jpeg|max:2048'
        ]);

        if ($validatedIncidentReport->fails())
            return response(['status' => 'warning', 'message' => $validatedIncidentReport->errors()->first()]);

        $resident = $this->reportLog->where('user_ip', $request->ip())->first();
        $reportPhotoPath = $request->file('photo')->store();
        $request->photo->move(public_path('reports_image'), $reportPhotoPath);
        $incidentReport = [
            'description' => Str::ucFirst(trim($request->description)),
            'location' => Str::of(trim($request->location))->title(),
            'photo' => $reportPhotoPath,
            'status' => 'On Process',
            'user_ip' => $request->ip(),
            'is_archive' => 0
        ];

        if ($resident) {
            $residentAttempt = $resident->attempt;
            $reportTime = $resident->report_time;

            if ($residentAttempt == 3) {
                if ($reportTime <= Carbon::now()->toDateTimeString()) {
                    $resident->update(['attempt' => 0, 'report_time' => null]);
                    $residentAttempt = 0;
                } else {
                    $reportTime = Carbon::parse($reportTime)->format('F j, Y H:i:s');
                    return response(['status' => 'blocked', 'message' => "You have been blocked until $reportTime."]);
                }
            }
            $this->incidentReport->create($incidentReport);
            $resident->update(['attempt' => $residentAttempt + 1]);
            $attempt = $resident->attempt;
            $attempt == 3 ? $resident->update(['report_time' => Carbon::now()->addDays(3)]) : null;
            //event(new IncidentReport());
            return response(['status' => 'success']);
        }

        $this->incidentReport->create($incidentReport);
        $this->reportLog->create([
            'user_ip' => $request->ip(),
            'attempt' => 1
        ]);
        //event(new IncidentReport());
        return response(['status' => 'success']);
    }

    public function approveIncidentReport($reportId)
    {
        $this->report->approveStatus($reportId);
        $this->logActivity->generateLog('Approving Incident Report');
        //event(new IncidentReport());
        return response()->json();
    }

    public function declineIncidentReport($reportId)
    {
        $this->report->declineStatus($reportId);
        $this->logActivity->generateLog('Declining Incident Report');
        //event(new IncidentReport());
        return response()->json();
    }

    public function revertIncidentReport($reportId)
    {
        $reportPhotoPath = $this->incidentReport->find($reportId)->value('photo');
        $this->report->revertReport($reportId, $reportPhotoPath);
        //event(new IncidentReport());
        return response()->json();
    }

    public function updateUserAttempt()
    {
        $userReport = $this->reportLog->where('user_ip', request()->ip())->value('attempt');

        if ($userReport == 3) {
            $this->reportLog->where('user_ip', request()->ip())->update([
                'attempt' => $userReport - 1,
                'report_time' => null
            ]);
        } else {
            $this->reportLog->where('user_ip', request()->ip())->update([
                'attempt' => $userReport - 1
            ]);
        }
        return response()->json();
    }

    public function removeIncidentReport($reportId)
    {
        $this->incidentReport->find($reportId)->update([
            'is_archive' => 1
        ]);
        $this->logActivity->generateLog('Removing Incident Report');
        return response()->json();
    }
}
