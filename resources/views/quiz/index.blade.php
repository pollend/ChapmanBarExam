@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach ($quizzes as $quiz)
            <div class="col-md-4">
                <div class="card quiz-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            @if($quiz->is_open)
                                <a href="{{route('quiz.start',['id' => $quiz->id])}}">{{ $quiz->name }}</a>
                            @else
                                {{ $quiz->name }}
                            @endif
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                        <p class="card-text">{{ $quiz->description }}</p>
                        @if(!$quiz->is_open)
                            {{__('Quiz Locked')}}
                        @endif
                        <div class="card-foot">
                            <div class="attempts float-left">{{$quiz->sessions()->byOwner($user)->count()}}/{{$quiz->num_attempts}}</div>
                            <div class="due-date float-right">{{ $quiz->close_date->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection