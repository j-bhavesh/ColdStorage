<x-guest-layout>
    <!-- Session Status -->
    <p class="login-box-msg">Sign in to start your session</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-3">
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email" />
            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
            <x-text-input id="password" class="form-control"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Password"/>
            <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="row">
            <div class="col-7">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me" />
                    <label class="form-check-label" for="remember_me">Remember Me</label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-5">
                <div class="d-grid gap-2">
                    <x-primary-button class="btn btn-primary">{{ __('Log In') }}</x-primary-button>
                </div>
            </div>
          <!-- /.col -->
        </div>

        <!-- Remember Me -->
    </form>
    @if (Route::has('password.request'))
        <p class="mb-1">        
            <a href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        </p>
    @endif
</x-guest-layout>
