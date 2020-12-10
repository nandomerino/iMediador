@php
    App::setLocale('es');
    $title = __('error.404.title');
@endphp

@extends('errors.layouts.core')

@section('content')
    <section class="py-5 px-3">
        <h1 class="text-center">
            @php
                echo __('error.404.title');
            @endphp
        </h1>
        <h3 class="text-center font-weight-bold py-5">
        @php
            echo __('error.404.message');
        @endphp
        </h3>
        <p class="text-center py-5">
            <a class="bg-navy-blue p-3 text-white" href="javascript:history.back()">
                @php
                    echo __('text.back');
                @endphp
            </a>
        </p>
    </section>
@endsection

