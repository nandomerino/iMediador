@php
    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'politica-cookies');
    $title = $data['title'];
@endphp

@extends('app.layouts.core')

@section('content')
    <section>
        {!! $data['content']  !!}
    </section>
@endsection

