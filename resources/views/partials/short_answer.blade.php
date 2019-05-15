<div>
    @php ($answer = $question->answersBySession($session)->first())
    <div class="bx--form-item">
        <div class="bx--form__helper-text">{{$index}} . {{$question->getContent()}}</div>
{{--        <textarea class="form-control" name="short_answer[{{$question->id}}]">@if($answer){{$answer->content}}@endif</textarea>--}}
{{--        <div class="bx--text-area__wrapper">--}}
            <textarea  name="short_answer[{{$question->getId()}}]" class="bx--text-area bx--text-area--v2 ful" rows="4" style="width: 100%" >@if($answer){{$answer->getContent()}}@endif</textarea>
{{--        </div>--}}
    </div>
    <div class="is-divider is-divider-margin-2"></div>

</div>