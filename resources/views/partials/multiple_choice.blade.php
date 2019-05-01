<div>
    <div>{{$question->content}}</div>

    @php ($answer = $question->answers()->bySession($session)->first())
    @foreach($question->entries()->orderBy('order','asc')->get() as $e)
            <div class="form-check">
                <input type="radio" id="question.{{$e->id}}.id" name="multiple_choice[{{$question->id}}][{{$e->id}}]" value="{{$e->id}}"
                    @if($answer && $answer->quiz_multiple_choice_entry_id == $e->id)
                        checked
                    @endif
                >
                <label class="form-check-label" for="question.{{$e->id}}.id">{{$e->content}}</label>
            </div>
    @endforeach
</div>