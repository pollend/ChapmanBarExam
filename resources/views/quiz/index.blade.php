@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($quizzes as $quiz)
                <div class="col-md-4">
                    <div class="card quiz-card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $quiz->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                            <p class="card-text">{{ $quiz->description }}</p>
                            <div class="card-foot">
                                <div class="attempts float-left">1/3</div>
                                <div class="due-date float-right">25 days</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection