<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReportLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Reporting;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ReportAccidentController extends Controller
{

    private $reportAccident;

    function __construct()
    {
        $this->reportAccident = new Reporting;
    }

    public function displayCReport(Request $request)
    {
        $report = Reporting::latest()->get();

        if ($request->ajax()) {
            $data = Reporting::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $approved = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Approve" class="approve bg-slate-700 hover:bg-slate-900 py-1.5 btn-sm mr-2 text-white approveAccidentReport">Approve</a>';
                    $btn = $approved . '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="bg-red-700 hover:bg-red-900 py-1.5 btn-sm mr-2 text-white removeAccidentReport">Remove</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('userpage.reportAccident.reportAccident', compact('report'));
    }

    public function displayGReport(Request $request)
    {
        $report = Reporting::latest()->get();

        if ($request->ajax()) {
            $data = Reporting::latest()->get();
            return DataTables::of($data)->addIndexColumn()->make(true);
        }

        return view('userpage.reportAccident.reportAccident', compact('report'));
    }

    public function addAccidentReport(Request $request)
    {
        $validatedAccidentReport = Validator::make($request->all(), [
            'description' => 'required',
            'location' => 'required',
            'photo' => 'required|image|mimes:jpeg|max:2048'
        ]);

        if ($validatedAccidentReport->passes()) {
            $user_ip = ReportLog::where('user_ip', $request->ip())->exists();
            $report_time = ReportLog::where('user_ip', $request->ip())->value('report_time');
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('reports_image'), $imageName);
            $reportAccident = [
                'description' => Str::ucFirst(trim($request->description)),
                'location' => Str::of(trim($request->location))->title(),
                'photo' => $imageName,
                'status' => 'On Process'
            ];

            if ($user_ip) {
                $resident_attempt = ReportLog::where('user_ip', $request->ip())->value('attempt');

                if ($resident_attempt >= 3) {
                    if ($report_time <= Carbon::now()->toDateTimeString()) {
                        ReportLog::where('user_ip', $request->ip())->update(['attempt' => 0, 'report_time' => null]);
                        $resident_attempt = 0;
                    } else {
                        return response()->json(['condition' => 2, 'block_time' => "You have been blocked until <font color='red'>$report_time</font>"]);
                    }
                }

                try {
                    $this->reportAccident->registerAccidentReportObject($reportAccident);
                    ReportLog::where('user_ip', $request->ip())->update(['attempt' => $resident_attempt + 1]);
                    $attempt = intval(ReportLog::where('user_ip', $request->ip())->value('attempt'));

                    if ($attempt >= 3) {
                        $remaining_time = Carbon::now()->addDays(3);
                        ReportLog::where('user_ip', $request->ip())->update(['report_time' => $remaining_time]);
                    }

                    return response()->json(['condition' => 0]);
                } catch (\Exception $e) {
                    return response()->json(['condition' => 1]);
                }
            } else {
                try {
                    $this->reportAccident->registerAccidentReportObject($reportAccident);

                    ReportLog::create([
                        'user_ip' => $request->ip(),
                        'attempt' => 1,
                    ]);

                    return response()->json(['condition' => 0]);
                } catch (\Exception $e) {
                    return response()->json(['condition' => 1]);
                }
            }
        }

        return response()->json(['condition' => 1, 'error' => $validatedAccidentReport->errors()->toArray()]);
    }

    public function approveAccidentReport($reportId)
    {
        try {
            $this->reportAccident->approveAccidentReportObject($reportId);

            ActivityUserLog::create([
                'user_id' => auth()->user()->id,
                'activity' => 'Approving Accident Report',
                'date_time' => Carbon::now()->toDayDateTimeString()
            ]);

        } catch (\Exception $e) {
            Alert::success(config('app.name'), 'Failed to Report Accident.');
        }

        return response()->json();
    }

    public function removeAccidentReport($reportAccidentId)
    {
        try {
            $this->reportAccident->removeAccidentReportObject($reportAccidentId);

            ActivityUserLog::create([
                'user_id' => auth()->user()->id,
                'activity' => 'Removing Accident Report',
                'date_time' => Carbon::now()->toDayDateTimeString()
            ]);
        } catch (\Exception $e) {
            Alert::success(config('app.name'), 'Failed to Report Accident.');
        }
    
        return response()->json();
    }
}
