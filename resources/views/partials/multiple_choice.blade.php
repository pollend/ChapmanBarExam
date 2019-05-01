<div>
    <div>{{$question->content}}</div>

    @foreach($question->entries()->orderBy('order','asc')->get() as $e)
            <div class="form-check">
                <input type="radio" id="question.{{$e->id}}.id" name="question[{{$question->id}}][{{$e->id}}]" value="{{$e->id}}">
                <label class="form-check-label" for="question.{{$e->id}}.id">{{$e->content}}</label>
            </div>
    @endforeach
</div>