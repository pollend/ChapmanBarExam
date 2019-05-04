<?php


namespace App\Http\Controllers;


use App\QuizSession;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $user = Auth::user();
        $sessions = QuizSession::query()->byOwner($user)->get();
        return view('report.index',['sessions' => $sessions]);
    }

    public function show($id)
    {

    }
}