@extends('layouts.app')

@section('content')
    <div class="container">
        <report-component route="{{ route('api.report.index')  }}"></report-component>
    </div>
@endsection