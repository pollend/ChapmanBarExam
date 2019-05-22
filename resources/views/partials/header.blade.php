<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="../">
                {{ config('app.name', 'Laravel') }}
            </a>
            <span class="navbar-burger burger" data-target="navbarMenu">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </div>
        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-end">
{{--                @section('left-bar-items')--}}

                @guest @else
                    <a class="navbar-item" href="{{route('home')}}">
                        Home
                    </a>
                    <a class="navbar-item" href="{{route('report.index')}}">
                        Reports
                    </a>
                @endguest
{{--                @endsection--}}
                @guest
                    <a class="navbar-item" href="{{ route('login') }}">
                        {{ __('Login') }}
                    </a>
                    @if (Route::has('register'))
                        <a class="navbar-item" href="{{ route('register') }}">
                            {{ __('Register') }}
                        </a>
                    @endif
                @else
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            {{ Auth::user()->getName() }}
                        </a>
                        <div class="navbar-dropdown">
                            <hr class="navbar-divider">
                            <a class="navbar-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            @if(Auth::user())
                                @if(Auth::user()->isAdmin())
                                    <a class="navbar-item" href="{{ route('dashboard.index') }}">
                                        {{ __('Admin') }}
                                    </a>
                                @endif
                            @endif

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
