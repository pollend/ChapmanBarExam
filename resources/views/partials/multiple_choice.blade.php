<div>
    @php ($answer = $question->answersBySession($session)->first())
    <div class="bx--form-item">
        <div class="bx--form__helper-text">{{$index}} . {{$question->getContent()}}</div>

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