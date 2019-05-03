@extends('layouts.app')

@section('content')

<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <form method="POST" class="qz-login-form" action="{{ route('login') }}">
                @csrf
                <div class="bx--form-item">

                    @if ($errors->has('email'))
                        @include('partials.components.notification-error',['title' => 'error','sub_title' => $errors->first('email') ])
                    @endif

                    <label for="text-email" class="bx--label">{{ __('E-Mail Address') }}</label>
                    <input id="text-email" type="text" name="email" class="bx--text-input{{ $errors->has('email') ? ' bx--text-input--invalid' : '' }}" required>
                </div>

                <div class="bx--form-item">
                    @if ($errors->has('password'))
                        @include('partials.components.notification-error',['title' => 'error','sub_title' => $errors->first('password') ])
                    @endif

                    <label for="text-password" class="bx--label">{{ __('Password') }}</label>
                    <input id="text-password" type="password" class="bx--text-input{{ $errors->has('password') ? ' bx--text-input--invalid' : '' }}" name="password" required>
                </div>

                <div class="bx--form-item">
                    <input id="bx--remember" class="bx--checkbox" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="bx--remember" class="bx--checkbox-label">{{ __('Remember Me') }}</label>
                </div>


                <div>
                    <button class="bx--btn bx--btn--primary" type="submit">
                        {{ __('Login') }}
                    </button>
                    @if (Route::has('password.request'))
                        <a class="bx--btn bx--btn--primary" href="{{ route('password.request') }}" >
                            {{ __('Forgot Your Password?')}}
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
