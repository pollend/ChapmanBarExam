<div>
    @php ($answer = $question->answers()->bySession($session)->first())
    <div class="form-group">
        <div>{{$question->content}}</div>
        <textarea class="form-control" name="short_answer[{{$question->id}}]">@if($answer){{$answer->content}}@endif</textarea>
    </div>

</div>