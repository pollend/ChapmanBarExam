@extends('layouts.app')

@section('content')
<div class="section">
    <div class="container">
        @foreach ($quizzes->chunk(4) as  $block)
                <div class="columns">
                    @foreach($block as $quiz)
                        <div class="column is-3">
                            <div class="card">
                                <header class="card-header">
                                    <p class="card-header-title">
                                        {{ $quiz->getName() }}
                                    </p>
                                    <a href="#" class="card-header-icon" aria-label="more options">
                                  <span class="icon">
                                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                                  </span>
                                    </a>
                                </header>
                                <div class="card-content">
                                    <div class="content">
                                        <div>
                                        {{ $quiz->getDescription() }}
                                        </div>
                                        <div class="is-divider is-divider-margin-4"></div>
                                        <div class="columns">
                                            <div class="column is-2">
                                                <span class="tag is-pulled-left">{{ $quiz->getQuizSessionsByUser($user)->count()}}/{{$quiz->getNumAttempts()}} attempts</span>
                                            </div>
                                            <div class="column is-pulled-right">
                                                <span class="tag is-pulled-right">{{ $quiz->getCloseDate()->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    @if($quiz->isOpen($user))
                                        <a href="{{route('quiz.start',['id' => $quiz->getId()])}}" class="bx--link card-footer-item">Start</a>
                                    @else
                                        <div class="card-footer-item">Locked</div>
                                    @endif
                                </footer>

                            </div>
                        </div>
                    @endforeach
                </div>
        @endforeach
    </div>
</div>
@endsection