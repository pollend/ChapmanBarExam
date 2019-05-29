<?php


namespace App\Http\Controllers\Dashboard;


use App\Entities\Classroom;
use App\Http\Controllers\Controller;
use App\Repositories\ClassroomRepository;
use Illuminate\Http\Request;

class DashboardClassroomController extends Controller
{
    public function index(){
        return view('dashboard.class.index');
    }

    public function show(Request $request,$id){
        /** @var ClassroomRepository $repo */
        $repo =  \EntityManager::getRepository(Classroom::class);
        /** @var Classroom $class */
        $class = $repo->find($id);
        return view('dashboard.class.show',['class' => $class]);
    }
}