@extends('layouts.app')

@section('content')
    @include('report.report_header')
    <div class="container has-margin-top-40 has-margin-bottom-40">
            <table class="table is-bordered is-size-7 is-full-width">
                <thead>
                    <tr>
                        <th>
                            Subtest Name
                        </th>
                        <th>
                            Possible Points
                        </th>
                        <th>
                            % Required To Pass
                        </th>
                        <th>
                            Points Scored
                        </th>
                        <th>
                            % Score
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($breakdown as $id => $entry)
                        <tr>
                            <td>
                                {{ $tags[$id]->getName() }}
                            </td>
                            <td>
                                {{ $entry->getMaxScore() }}
                            </td>
                            <td>
                                67.00%
                            </td>
                            <td>
                                {{ $entry->getScore() }}
                            </td>
                            <td>
                                {{ number_format(($entry->getScore()/$entry->getMaxScore()) * 100,2,'.',',') }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
@endsection