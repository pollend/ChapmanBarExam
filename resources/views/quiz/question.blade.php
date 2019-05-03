@extends('layouts.app')

@section('header')
    @include('partials.header_quiz')
@endsection

@section('content')
    <div class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form method="POST" action="{{ route('quiz.question',['session_id' => $session_id,'page' => $page]) }}">
                        @csrf
                        @foreach ($questions as $question)
                            @switch($question->type)
                                @case('multipleChoice')
                                    @include('partials.multiple_choice',['question' => $question,'index' => $loop->iteration])
                                    @break
                                @case('shortAnswer')
                                    @include('partials.short_answer',['question' => $question,'index' => $loop->iteration])
                                    @break
                            @endswitch
                        @endforeach

                        <div>
                            @if($page > 0)
                                <button class="bx--btn bx--btn--primary"  name="action" value="back" type="submit">
                                    {{ __('Previous') }}
                                </button>
                            @endif

                            @if($page < $maxPage)

                                <button class="bx--btn bx--btn--primary" name="action" value="next" type="submit">
                                    {{ __('Next') }}
                                </button>
                            @else
                                <button class="bx--btn bx--btn--primary" name="action" value="next" type="submit">
                                    {{ __('Submit') }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection