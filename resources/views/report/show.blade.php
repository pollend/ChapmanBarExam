@extends('layouts.app')

@section('content')
    @include('report.report_header')

    <div class="container has-margin-bottom-20">
        <div class="columns">
            <div class="column is-one-fifth">
                Response Description:
            </div>
            <div class="column  is-size-7">
                <div class="columns">
                    <div class="column is-4">
                        <p>&lt - &gt correct</p>
                        <p>&lt A-Z &gt student's incorrect response</p>

                    </div>
                    <div class="column is-4">
                        <p>&lt # &gt multiple marks</p>
                        <p>&lt * &gt bonus test item</p>

                    </div>
                    <div class="column is-4">
                        <p>&lt _ &gt no response</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container has-margin-bottom-60">
        @php
            $index = 0;
        @endphp

        @foreach(Illuminate\Support\Collection::make($questions)->filter(function ($q){ return $q instanceof App\Entities\MultipleChoiceQuestion; })->chunk(5)->chunk(10) as $table)
            <table class="table is-bordered is-size-7" style="width: 100%;">
                <thead>
                    <tr>
                        <th>
                            Test Items:
                        </th>
                        @foreach($table as $groups)
                            <th>
                                {{$index + 1}}- {{$index += 5}}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <td>
                        Answers:
                    </td>
                    @foreach($table as $groups)
                        <td>

                            @foreach($groups as $e)
                                @if (!$loop->first)
                                    ,
                                @endif
                                    @if(Arr::has($responses,$e->getId()))
                                        @if($responses[$e->getId()]->getChoice() == $e->getCorrectEntry())
                                            -
                                        @else
                                            {{ $e->toCharacter($responses[$e->getId()]->getChoice()) }}

                                        @endif

                                    @else
                                        _
                                    @endif
                            @endforeach

                        </td>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    <div class="container">
        @foreach ($groups as $index => $group)
            <div class="article">

                @foreach ($questions as $question)

                    <div class="tags are-small">
                        @foreach($question->getTags() as $tag)
                            <span class="tag">{{$tag->getName()}}</span>
                        @endforeach
                    </div>
                    @php
                        $answer = Arr::has($responses,$question->getId()) ? $responses[$question->getId()] : null
                    @endphp
                    @if($question instanceof \App\Entities\MultipleChoiceQuestion)
                        <div>
                            @php
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

@endsection