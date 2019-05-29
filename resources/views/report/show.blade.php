@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="columns is-mobile is-centered">
            <div class="column is-5">
                <p>
                    Course #:
                </p>
                <p>
                    Course Title:
                </p>
                <p>
                    Day\Time:
                </p>
            </div>
            <div class="column is-5">
                <p>
                    Instructor:
                </p>
                <p>
                    Description:
                </p>
                <p>
                    Term/Year:
                </p>
            </div>
        </div>
    </div>

    <div class="section">
        <table class="table is-bordered">
            <thead>
                <tr>
                    <th>
                       Test Items:
                    </th>
                    <th>
                        1-5
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="section">
        <div class="container">
            @foreach ($groups as $index => $qgroup)
                <div class="article">
                @foreach ($qgroup as $question)
                    @if($question instanceof \App\Entities\MultipleChoiceQuestion)
                        <div>
                            @php
                                $answer = $question->answersBySession($session)->first();
                                $correctChoice =  $question->getCorrectEntry();
                            @endphp

                            <div class="bx--form-item" style="position: relative">
                                <div class="qz-dot {{ ($answer == false) ? 'qz-dot-non-response' : (($answer->getChoice() == $correctChoice) ? 'qz-dot-correct' : 'qz-dot-incorrect')}}"></div>
                                <div class="bx--form__helper-text qz--form__helper-text-disable-max-width"> {{$loop->iteration}} . {{$question->getContent()}}</div>

                                <div class="bx--radio-button-group bx--radio-button-group--vertical bx--radio-button-group--vertical-left ">
                                    @foreach($question->getEntries() as $e)

                                        <div class="bx--radio-button-wrapper is-al">
                                            <label for="question.{{$e->getId()}}.id" class="bx--radio-button__label {{(($answer && $answer->getChoice() == $e) || $correctChoice == $e) ? 'qz--radio-button-checked' : ''}} {{ $answer && $answer->getChoice() == $e ? (($answer->getChoice() == $correctChoice) ? 'qz--radio-correct' : 'qz--radio-incorrect') : '' }}">
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
                                <div class="bx--form__helper-text qz--form__helper-text-disable-max-width">{{$loop->iteration}} . {{$question->getContent()}}</div>
                                {{$answer ? $answer->getContent() : ''}}
                            </div>
                            <div class="is-divider is-divider-margin-2"></div>
                        </div>
                    @endif
                @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection