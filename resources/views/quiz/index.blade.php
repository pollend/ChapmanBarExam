@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="row">
            @for ($i = 0; $i < 10; $i++)
                <div class="col-md-4">
                    <div class="card quiz-card">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            <div class="card-foot">
                                <div class="attempts float-left">1/3</div>
                                <div class="due-date float-right">25 days</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
@endsection