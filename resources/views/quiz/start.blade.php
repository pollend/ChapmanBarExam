@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
           <form method="POST" action="{{ route('quiz.start', ['quiz_id' => $quiz->getId(), 'class_id' => $class->getId()]) }}">
               @csrf
                <h1>Bar Exam</h1>

                <div>This is the bar exam ... some information</div>

               <button type="submit" class="btn btn-primary">
                   {{ __('Start') }}
               </button>
           </form>


        </div>
    </div>
@endsection