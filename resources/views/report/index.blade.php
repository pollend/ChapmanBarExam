@extends('layouts.default')

@section('content')
    <div class="container">
        @for ($i = 0; $i < 10; $i++)
            <div>
                Test
            </div>
        @endfor
    </div>
@endsection