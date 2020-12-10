@php
// TEST USER
// User: 6600031
// Password: G7742

@endphp

<aside class="py-4 px-3">
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="card-title m-0">{{ __('login.title') }}</h4>
        </div>
        <div class="card-body">
            <form method="POST" class="login-form" id="loginForm" action="/login">
                @csrf
                <div class="form-group">
                    <label for="user">* {{ __('login.user') }}</label>
                    <input name="user" type="text" class="user" class="form-control" required autocomplete="login user name">
                </div>
                <div class="form-group">
                    <label for="user">* {{ __('login.password') }}</label>
                    <input name="password" type="password" class="password" class="form-control" required autocomplete="login password">
                </div>
                <div class="form-group">
                    <label for="user">{{ __('login.PM.gestor') }}</label>
                    <input name="gestor" type="text" class="gestor" class="form-control" autocomplete="login user gestor">
                </div>
                <input name="login-type" type="hidden" class="login-type" value="app-login">
                <input type="hidden" name="action" value="sendLoginForm">
                <div class="position-relative">
                    <input type="submit" class="btn btn-block text-white font-weight-bold bg-lime-yellow" value="{{ __('login.submit') }}">
                    <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                </div>
                <div class="error-message mt-3"></div>
            </form>

        </div>
    </div>
</aside>
