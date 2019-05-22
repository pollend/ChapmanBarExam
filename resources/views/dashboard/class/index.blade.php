@extends('dashboard.layout.app')


@section('content')

<div class="section">

    <div class="content">
        <h1>Classes</h1>
    </div>
    <nav data-tabs class="bx--tabs" role="navigation">
        <div class="bx--tabs-trigger" tabindex="0">
            <a href="javascript:void(0)" class="bx--tabs-trigger-text" tabindex="-1"></a>
            <svg focusable="false" preserveAspectRatio="xMidYMid meet" style="will-change: transform;" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" aria-hidden="true"><path d="M5 6L0 1 .7.3 5 4.6 9.3.3l.7.7z"></path></svg>
        </div>
        <ul class="bx--tabs__nav bx--tabs__nav--hidden" role="tablist">
            <li class="bx--tabs__nav-item bx--tabs__nav-item--selected " data-target=".tab-1" role="presentation" >
                <a tabindex="0" id="tab-link-1" class="bx--tabs__nav-link" href="javascript:void(0)" role="tab" aria-controls="tab-active" aria-selected="true">Active</a>
            </li>
            <li class="bx--tabs__nav-item " data-target=".tab-2" role="presentation" >
                <a tabindex="0" id="tab-link-2" class="bx--tabs__nav-link" href="javascript:void(0)" role="tab" aria-controls="tab-archive">Archive</a>
            </li>
        </ul>
    </nav>
    <div class="content">

    <div id="tab-active" class="tab-1" role="tabpanel" aria-labelledby="tab-link-1" aria-hidden="false">
        <class-datatable routes="{{route('api.dashboard.classes.index')}}" type="active"></class-datatable>
    </div>
    <div id="tab-archive" class="tab-2" role="tabpanel" aria-labelledby="tab-link-2" aria-hidden="true" hidden>
        <class-datatable routes="{{route('api.dashboard.classes.index')}}" type="archive"></class-datatable>
    </div>





</div>
@endsection