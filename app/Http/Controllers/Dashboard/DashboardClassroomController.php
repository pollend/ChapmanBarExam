<?php


namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;

class DashboardClassroomController extends Controller
{
    public function index(){
        return view('dashboard.class.index');
    }
}