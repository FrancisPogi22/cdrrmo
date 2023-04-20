<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvacuationCenter;

class CdrrmoController extends Controller{
    public function dashboard(){
        return view('cdrrmo.dashboard');
    }

    public function recordEvacuee(){
        $recordEvacueeController = new RecordEvacueeController();

        $barangays = $recordEvacueeController->barangayList();
        $evacuationCenters = $recordEvacueeController->evacuationCenterList();
        $disasters = $recordEvacueeController->disasterList();

        return view('cdrrmo.recordEvacuee.recordEvacuee', compact('barangays','evacuationCenters','disasters'));
    }

    public function eligtasGuideline(){
        $guidelinesList = new GuidelineController();
        $guidelinesList = $guidelinesList->guideline();

        return view('cdrrmo.guideline.eligtasGuideline', $guidelinesList);
    }

    public function eligtasGuide($guidelineId){
        $guideList = new GuidelineController();
        $guideList = $guideList->guide($guidelineId);

        return view('cdrrmo.guideline.guide' , $guideList , compact('guidelineId'));
    }

    public function disaster(){
        $disasterList = new DisasterController();
        $disasterList = $disasterList->disasterList();

        return view('cdrrmo.disaster.disaster', $disasterList);
    }

    public function barangay(){
        $barangayList = new BarangayController();
        $barangayList = $barangayList->barangayList();

        return view('cdrrmo.barangay.barangay', $barangayList);
    }

    public function evacuationManage(){
        $evacuation = new EvacuationCenterController();
        $evacuation = $evacuation->evacuationCenterList();

        return view('cdrrmo.evacuationCenter.evacuation', $evacuation);
    }

    public function evacuationCenter(){
        $evacuationCenter = EvacuationCenter::all();

        $initialMarkers = [
            [
                'position' => [
                    'lat' => 28.625485,
                    'lng' => 79.821091
                ],
                'label' => [ 'color' => 'white', 'text' => 'P1' ],
                'draggable' => true
            ],
            [
                'position' => [
                    'lat' => 28.625293,
                    'lng' => 79.817926
                ],
                'label' => [ 'color' => 'white', 'text' => 'P2' ],
                'draggable' => false
            ],
            [
                'position' => [
                    'lat' => 28.625182,
                    'lng' => 79.81464
                ],
                'label' => [ 'color' => 'white', 'text' => 'P3' ],
                'draggable' => true
            ]
        ];

        return view('cdrrmo.evacuationCenter.evacuationCenter', ['evacuationCenter' => $evacuationCenter, 'initialMarkers' => $initialMarkers] );
    }

    public function statistics(){
        return view('cdrrmo.statistics.statistics');
    }

    public function reportAccident(){
        return view('cdrrmo.reportAccident.reportAccident');
    }

    public function hotlineNumbers(){
        return view('cdrrmo.hotlineNumbers.hotlineNumbers');
    }

    public function about(){
        return view('cdrrmo.about.about');
    }

    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Successfully Logout Admin Panel');
    }
}