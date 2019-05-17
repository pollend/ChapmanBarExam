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
                            @if($question instanceof \App\Entities\MultipleChoiceQuestion)
                                <div>
                                    @php ($answer = $question->answersBySession($session)->first())
                                    <div class="bx--form-item">
                                        <div class="bx--form__helper-text">{{$loop->iteration}} . {{$question->getContent()}}</div>

                                        <div class="bx--radio-button-group bx--radio-button-group--vertical bx--radio-button-group--vertical-left ">
                                            @foreach($question->getEntries() as $e)
                                                <div class="bx--radio-button-wrapper is-al">
                                                    <input class="bx--radio-button" type="radio" id="question.{{$e->getId()}}.id" name="multiple_choice[{{$question->getId()}}]" value="{{$e->getId()}}" @if($answer && $answer->getChoice() == $e) checked @endif>
                                                    <label for="question.{{$e->getId()}}.id" class="bx--radio-button__label">
                                                        <span class="bx--radio-button__appearance"></span>
                                                        <span class="bx--radio-button__label-text">{{$e->getContent()}}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="is-divider is-divider-margin-2"></div>
                                </div>
                            @elseif($question instanceof \App\Entities\ShortAnswerQuestion)
                                <div>
                                    @php ($answer = $question->answersBySession($session)->first())
                                    <div class="bx--form-item">
                                        <div class="bx--form__helper-text">{{$loop->iteration}} . {{$question->getContent()}}</div>
                                        <textarea  name="short_answer[{{$question->getId()}}]" class="bx--text-area bx--text-area--v2 ful" rows="4" style="width: 100%" >@if($answer){{$answer->getContent()}}@endif</textarea>
                                    </div>
                                    <div class="is-divider is-divider-margin-2"></div>
                                </div>
                            @endif
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