@php
    App::setLocale('es');
@endphp

@extends('panel.layouts.core')

@section('content')
    <section id="home">
        <div class="row p-5">
            <div class="col-12 col-md-6 text-center">
                <a href="{{ __('sidebar.menu.impersonator.url') }}">
                    <div class="shortcut-button m-3 bg-lime-yellow text-white p-4 rounded">
                    {{ __('panel.shortcutButton.impersonator.text') }}
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 text-center">
                <a href="{{ __('sidebar.menu.sliders.url') }}" class="">
                    <div class="shortcut-button m-3 bg-lime-yellow text-white p-4 rounded">
                        {{ __('panel.shortcutButton.sliders.text') }}
                    </div>
                </a>
            </div>
        </div>
    </section>

@endsection
