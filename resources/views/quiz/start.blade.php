@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
           <form method="POST" action="{{ route('quiz.start', ['id' => $quiz_id]) }}">
               @csrf
                <h1>Quiz Start</h1>

                <div>Some information </div>

               <button type="submit" class="btn btn-primary">
                   {{ __('Start') }}
               </button>
           </form>


        </div>
    </div>
@endsection