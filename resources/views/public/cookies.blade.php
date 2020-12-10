@php
    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'politica-cookies');
    $title = $data['title'];
@endphp

@extends('public.layouts.core')

@section('content')
    <section class="py-4 px-0">
        @php
            echo $data['content'];
        @endphp
    </section>
@endsection

