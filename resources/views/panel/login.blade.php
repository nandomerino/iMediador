@php
    App::setLocale('es');

// TEST USER
// User: 6600031
// Password: G7742
// Interno: 451

@endphp

@extends('panel.layouts.core-fullscreen')

@section('content')

    <section id="login-private" >
        <div class="card">
            <div class="card-body text-center px-5 pb-5 pt-4">
                <a class="navbar-brand active ml-5 mr-2" href="/">
                    <img src="/img/logo-imediador-small.png" alt="Inicio">
                </a>
                <a class="navbar-brand ml-2 mb-3" href="https://previsionmallorquina.com" target="_blank">
                    <img class="navbar-brand" src="/img/logo-pm-small.png" alt="Web de Prevision Mallorquina">
                </a>
                <form method="POST" class="login-form-private" id="loginFormPrivate" action="/login">
                    @csrf
                    <div class="form-group">
                        <label for="user" class="txt-navy-blue font-weight-bold mb-2">{{ __('login.user') }}</label>
                        <input name="user" type="text" class="user form-control text-center" required autocomplete="login user name panel">
                    </div>
                    <div class="form-group">
                        <label for="user" class="txt-navy-blue font-weight-bold mb-2">{{ __('login.password') }}</label>
                        <input name="password" type="password" class="password orm-control text-center" required autocomplete="login password panel">
                    </div>
                    <div class="form-group">
                        <label for="user" class="txt-navy-blue font-weight-bold mb-2">{{ __('login.PM.user') }}</label>
                        <input name="pm-user" type="text" class="gestor form-control text-center" required autocomplete="pmuser ">
                    </div>
                    <input name="login-type" type="hidden" class="login-type" value="private-login">
                    <input name="entry-channel" type="hidden" class="login-type" value="GI">
                    <input type="hidden" name="action" value="sendLoginForm">
                    <div class="position-relative button-wrapper">
                        <input type="submit" class="btn btn-block text-white font-weight-bold bg-lime-yellow mt-5" value="{{ __('login.PM.submit') }}">
                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                    </div>
                    <div class="error-message mt-3"></div>
                </form>
            </div>
        </div>
    </section>
@endsection
