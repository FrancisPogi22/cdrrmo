<?php

namespace App\Http\Controllers;

use App\Models\Evacuee;
use App\Models\Feedback;
use App\Models\Disaster;
use App\Models\Guideline;
use Illuminate\Http\Request;
use App\Models\HotlineNumbers;
use App\Models\ResidentReport;
use App\Models\ActivityUserLog;
use App\Models\EvacuationCenter;
use App\Exports\EvacueeDataExport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as FileFormat;

class MainController extends Controller
{
    private $evacuationCenter, $disaster, $evacuee, $guideline, $residentReport, $feedback;

    public function __construct()
    {
        $this->evacuee          = new Evacuee;
        $this->feedback         = new Feedback;
        $this->disaster         = new Disaster;
        $this->guideline        = new Guideline;
        $this->residentReport   = new ResidentReport;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function dashboard()
    {
        $disaster           = $this->disaster->all();
        $evacuees           = $this->evacuee->countEvacueesByStatus();
        $evacuated          = $evacuees['evacuated'];
        $onGoingDisasters   = $disaster->where('status', "On Going");

        if (auth()->user()->organization == "CSWD") {
            $evacuationCenter = $this->evacuationCenter->getEvacuationCount();

            return view('userpage.dashboard.dashboard', [
                'activeEvacuation' => $evacuationCenter->activeEvacuation,
                'inactiveEvacuation' => $evacuationCenter->inactiveEvacuation,
                'fullEvacuation' => $evacuationCenter->fullEvacuation,
                'evacuated' => $evacuated,
                'returnedHome' => $evacuees['returnedHome'],
                'onGoingDisasters' => $onGoingDisasters,
                'disaster' => $disaster,
            ]);
        } else {
            $residentReport = $this->residentReport->getReportCount();

            return view('userpage.dashboard.dashboard', [
                'evacuated' => $evacuated,
                'returnedHome' => $evacuees['returnedHome'],
                'onGoingDisasters' => $onGoingDisasters,
                'disaster' => $disaster,
                'todayReport' => $residentReport['todayReport'],
                'resolvingReport' => $residentReport['resolvingReport'],
                'resolvedReport' => $residentReport['resolvedReport'],
            ]);
        }
    }

    public function searchDisaster($year)
    {
        return $this->disaster->where('year', $year)->get();
    }

    public function generateExcelEvacueeData(Request $request)
    {
        $generateReportValidation = Validator::make($request->all(), ['disaster_id' => 'required']);

        if ($generateReportValidation->fails()) {
            return response(['status' => 'warning', 'message' => 'Disaster is not exist.']);
        }

        return (new EvacueeDataExport($request->disaster_id))
            ->download('evacuee-data.xlsx', FileFormat::XLSX, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
    }

    public function eligtasGuideline()
    {
        $prefix = basename(trim(request()->route()->getPrefix(), '/'));

        if (!request()->ajax()) return view('userpage.guideline.eligtasGuideline', compact('prefix'));

        $guidelineData = auth()->check() ? $this->guideline->where('organization', auth()->user()->organization)->get() : $this->guideline->all();

        return response(['guidelineData' => $guidelineData, 'prefix' => $prefix]);
    }

    public function searchGuideline(Request $request)
    {
        $searchGuidelineValdation = Validator::make($request->all(), ['guideline_name' => 'required']);

        if ($searchGuidelineValdation->fails()) return response(['warning' => $searchGuidelineValdation->errors()->first()]);

        $guidelineData = $this->guideline
            ->select('*')
            ->when(auth()->check(), function ($query) {
                $query->where('organization', auth()->user()->organization);
            })
            ->where('type', 'LIKE', "%{$request->guideline_name}%")
            ->get();

        return response(['guidelineData' => $guidelineData]);
    }

    public function guide($guidelineId)
    {
        $guideline = $this->guideline->find($guidelineId);

        return view('userpage.guideline.guide', compact('guideline'));
    }

    public function manageEvacueeInformation($operation)
    {
        $disasterList        = $this->disaster->where('is_archive', 0)->get();
        $archiveDisasterList = $this->disaster->where('is_archive', 1)->get();
        $yearList            = $archiveDisasterList->pluck('year')->unique()->sortByDesc('year');
        $archiveDisasterList = $archiveDisasterList->where('year', $yearList->first());
        $evacuationList      = $this->evacuationCenter->whereNotIn('status', ['Inactive', 'Archived'])->get();

        return view('userpage.evacuee.evacuee', compact('evacuationList', 'disasterList', 'yearList', 'archiveDisasterList', 'operation'));
    }

    public function disasterInformation($operation)
    {
        return view('userpage.disaster.disaster', compact('operation'));
    }

    public function evacuationCenterLocator()
    {
        $prefix = basename(trim(request()->route()->getPrefix(), '/'));
        $onGoingDisasters = $this->disaster->where('status', 'On Going')->get();

        return view('userpage.evacuationCenter.evacuationCenterLocator', compact('prefix', 'onGoingDisasters'));
    }

    public function evacuationCenter($operation)
    {
        return view('userpage.evacuationCenter.manageEvacuation', compact('operation'));
    }

    public function incidentReporting()
    {
        return view('userpage.residentReport.incidentReporting');
    }

    public function userActivityLog()
    {
        if (!request()->ajax()) return view('userpage.userAccount.activityLog');

        $name = '';

        return DataTables::of(ActivityUserLog::join('user', 'activity_log.user_id', '=', 'user.id')
            ->select('activity_log.*', 'user.name', 'user.status')
            ->orderByDesc('activity_log.id')
            ->where('user.id', '!=', auth()->user()->id)
            ->get())
            ->addColumn('user_status', fn ($userLog) => '<div class="status-container"><div class="status-content bg-' .
                match ($userLog->status) {
                    'Active'   => 'success',
                    'Inactive' => 'warning',
                    'Archived' => 'danger'
                }
                . '">' . $userLog->status . '</div></div>')
            ->addColumn('action', function ($userLog) use (&$name) {
                $newName = $userLog->name != $name;
                $name = $userLog->name;

                return $userLog->status == 'Active' && $newName ?
                    '<div class="action-container"><button class="btn-table-remove" id="disableBtn"><i class="bi bi-person-lock"></i>Disable Account</button></div>' :
                    '';
            })->rawColumns(['user_status', 'action'])->make(1);
    }

    public function userAccounts($operation)
    {
        return view('userpage.userAccount.userAccounts', compact('operation'));
    }

    public function userProfile()
    {
        return view('userpage.userAccount.userProfile');
    }

    public function manageReport($operation)
    {
        $yearList   = [];
        $prefix     = basename(trim(request()->route()->getPrefix(), '/'));
        $reportType = ['All', 'Emergency', 'Incident', 'Flooded', 'Roadblocked'];
        if ($operation == "archived")
            $yearList = $this->residentReport->selectRaw('YEAR(report_time) as year')->where('is_archive', 1)->distinct()->orderBy('year', 'desc')->get();

        return view('userpage.residentReport.manageReport', compact('operation', 'prefix', 'yearList', 'reportType'));
    }

    public function fetchBarangayData()
    {
        return response()->json($this->evacuee->selectRaw('barangay, SUM(individuals) as individuals')->where('evacuee.status', "Evacuated")->join('disaster', 'disaster.id', '=', 'disaster_id')->groupBy('barangay')->get());
    }

    public function fetchDisasterData()
    {
        return response()->json($this->disaster
            ->join('evacuee', 'evacuee.disaster_id', 'disaster.id')
            ->where('evacuee.status', 'Evacuated')
            ->selectRaw('disaster.name as disasterName,
                SUM(evacuee.male) as male,
                SUM(evacuee.female) as female,
                SUM(evacuee.senior_citizen) as seniorcitizen,
                SUM(evacuee.minors) as minors,
                SUM(evacuee.infants) as infants,
                SUM(evacuee.pwd) as pwd,
                SUM(evacuee.pregnant) as pregnant,
                SUM(evacuee.lactating) as lactating')
            ->groupBy('disaster.id', 'disaster.name')
            ->get()
            ->toArray());
    }

    public function fetchReportData()
    {
        $startDate = now()->subMonth();

        $reportData = $this->residentReport
            ->selectRaw('type, DATE(report_time) as report_date, COUNT(*) as report_count')
            ->whereBetween('report_time', [$startDate, now()])
            ->groupBy(['type', 'report_date'])
            ->orderBy('report_date')
            ->get()
            ->groupBy('type')
            ->map(function ($typeData, $type) {
                return [
                    'type' => $type,
                    'data' => $typeData->map(function ($data) {
                        return [
                            'report_date' => $data->report_date,
                            'report_count' => $data->report_count
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response(['data' => $reportData, 'start_date' => $startDate]);
    }

    public function hotlineNumbers($operation)
    {
        $hotlineNumbers = HotlineNumbers::all();

        return view('userpage.hotlineNumber.hotlineNumbers', compact('hotlineNumbers', 'operation'));
    }

    public function getTopEvacuation($feedBackType)
    {
        $topEvacList = $this->feedback
            ->selectRaw("evacuation_center.id as id, evacuation_center.name as name, SUM($feedBackType) as feedback_total")
            ->join('evacuation_center', 'evacuation_center.id', '=', 'feedback.evacuation_center_id')
            ->groupBy('evacuation_center.id', 'evacuation_center.name')
            ->latest('feedback_total')
            ->limit(3)
            ->get();

        return response(['topEvacList' => $topEvacList]);
    }
}
