@php
    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'documentacion');
    $title = $data['title'];
@endphp

@extends('app.layouts.core')

@section('content')
    <section>
        @php
            echo $data['content'];
        @endphp
    </section>
@endsection

@include('public.layouts.modal')


