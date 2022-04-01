@php
    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'inicio');
    $title = $data['title'];
@endphp

@extends('public.layouts.core')

@section('content')
    <section class="py-4 px-0">
        @php
            echo $data['content'];
        @endphp
    </section>
    <div id="cambiarPWD" style="display: none">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="card-title m-0">{{ __('login.change.title') }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" class="login-form" id="loginChangeForm" action="/login">
                    @csrf
                    <div class="form-group">
                        <label for="user">* {{ __('login.nowpassword') }}</label>
                        <input name="password" type="password" class="password" required autocomplete="login password">
                    </div>
                    <div class="form-group">
                        <label for="user">* {{ __('login.newpassword') }}</label>
                        <input name="passwordNew" type="password" class="password" class="form-control" required autocomplete="login password">
                    </div>
                    <div class="form-group">
                        <label for="user">* {{ __('login.repitpassword') }}</label>
                        <input name="repitPassword" type="password" class="password" class="form-control" required autocomplete="login password">
                        <span class="message text-center mt-3"></span>
                    </div>
                    <input name="user" type="hidden" class="user" class="form-control" required autocomplete="login user name">
                    <input name="login-type" type="hidden" class="login-type" value="change-password">
                    <input type="hidden" name="action" value="changePassword">
                    <input name="entry-channel" type="hidden" class="login-type" value="IM">

                    <div class="position-relative">
                        <input type="submit" class="btn btn-block text-white font-weight-bold bg-lime-yellow disable" id="loginSubmitChangeForm" value="{{ __('login.submitpassword') }}" disabled>
                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                    </div>
                    <div class="error-message mt-3 text-center"></div>
                </form>

            </div>
        </div>
    </div>
@endsection
