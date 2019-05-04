@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach($sessions as $session)
            <div>
                {{ $session->created_at  }}
            </div>
        @endforeach
    </div>
@endsection