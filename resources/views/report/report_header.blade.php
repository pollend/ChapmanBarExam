<div class="container has-margin-bottom-20">
    <div class="columns is-mobile">
        <div class="column is-5 is-pulled-left">
            <p>
                Course #:
            </p>
            <p>
                Course Title:
            </p>
            <p>
                Day\Time:
            </p>
        </div>
        <div class="column is-5 is-pulled-left">
            <p>
                Instructor:
            </p>
            <p>
                Description:
            </p>
            <p>
                Term/Year:
            </p>
        </div>
    </div>
</div>

<div class="container has-margin-bottom-20">
    <div class="tabs">
        <ul>
            <li class="{{ ((route('report.show',['id' => $session->getId()]) == Request::url()) ? 'is-active' : '') }}"><a href="{{route('report.show',['id' => $session->getId()])}}">Overview</a></li>
            <li class="{{ ((route('report.breakdown',['id' => $session->getId()]) == Request::url()) ? 'is-active' : '') }}"><a href="{{route('report.breakdown',['id' => $session->getId()])}}">Breakdown</a></li>
        </ul>
    </div>
</div>