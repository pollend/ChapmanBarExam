@extends('layouts.app')

@section('header')
    @include('partials.header_quiz')
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="POST" action="{{ route('quiz.question',['session_id' => $session_id,'page' => $page]) }}">
                    @csrf
                    @foreach ($questions as $question)
                        @switch($question->type)
                            @case('multipleChoice')
                                @include('partials.multiple_choice',['question' => $question])
                                @break
                            @case('shortAnswer')
                                @include('partials.short_answer',['question' => $question])
                                @break
                        @endswitch
                    @endforeach

                    @if($page > 0)
                    <button type="submit" name="submit" value="back" class="btn btn-primary">
                        {{ __('Previous') }}
                    </button>
                    @endif

                    @if($page < $maxPage)
                            <button type="submit" name="submit" value="next" class="btn btn-primary">
                                {{ __('Next') }}
                            </button>
                    @else
                        <button type="submit" name="submit" value="next" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection