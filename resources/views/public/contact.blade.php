@php
    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'contacto');
    $title = $data['title'];
@endphp

@extends('public.layouts.core')

@section('content')
    <script src="/js/contactMap.js?v=2"></script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5x6LbVybWQF3NXdCLT2sN8Fmbbn9xXWo&callback=initMap"></script>

    <section class="py-4 px-0 contact-page">
        @php
            echo $data['content'];
        @endphp
    </section>
@endsection

@include('public.layouts.modal')


