<div>


    @php ($answer = $question->answers()->bySession($session)->first())
    <div class="bx--form-item">
        <div class="bx--form__helper-text">{{$index}} . {{$question->content}}</div>

        <div class="bx--radio-button-group bx--radio-button-group--vertical bx--radio-button-group--vertical-left ">
            @foreach($question->entries()->orderBy('order','asc')->get() as $e)
                <div class="bx--radio-button-wrapper is-al">
                    <input class="bx--radio-button" type="radio" id="question.{{$e->id}}.id" name="multiple_choice[{{$question->id}}]" value="{{$e->id}}" @if($answer && $answer->quiz_multiple_choice_entry_id == $e->id) checked @endif>
                    <label for="question.{{$e->id}}.id" class="bx--radio-button__label">
                        <span class="bx--radio-button__appearance"></span>
                        <span class="bx--radio-button__label-text">{{$e->content}}</span>
                    </label>
                </div>
            @endforeach
        </div>

    </div>
    <div class="is-divider is-divider-margin-2"></div>


</div>