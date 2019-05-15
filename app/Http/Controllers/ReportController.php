<?php


namespace App\Http\Controllers;

use App\Repositories\QuizSessionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ReportController extends Controller
{
    private $quizRepository;
    private $sessionRepository;

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        return view('report.index');
    }

    public function show(Request $request,$report)
    {

        $user = Auth::user();

        return ['test' => 'test'];
    }
}