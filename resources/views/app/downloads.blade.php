@php
    App::setLocale('es');
    $title = __('menu.quote.text');
    $pm = new \App\Http\Middleware\PMWShandler();
    $currentLanguage = App::getLocale();

    $filesList = $pm->getFilesList();
    app('debugbar')->info($filesList);


@endphp

@extends('app.layouts.core')

@section('content')

    <section class="header pb-4 row">
        <div class="col">
            <div class="breadcrumbs txt-navy-blue pb-2"><a href="{{ __('menu.home.url') }}">{{ __('menu.home.text') }}</a> > {{ __('sidebar.menu.downloadDocuments.text') }}</div>
            <div class="separator bg-lime-yellow"></div>
        </div>
    </section>

    <section id="downloads" class="pb-5 row">
        <div class="col">
           

                <table id="download-documents-table" class="w-100">
                    <thead>
                        <tr>
                            <th class="px-1 txt-navy-blue">{{ __('downloadDocuments.table.header.col1') }}</th>
                            <th class="px-1 txt-navy-blue">{{ __('downloadDocuments.table.header.col2') }}</th>
                            <th class="px-1 txt-navy-blue">{{ __('downloadDocuments.table.header.col3') }}</th>
                            <th class="px-1 txt-navy-blue"><img src="/img/download-blue.png"></th>
                        </tr>
                    </thead>
                     @if( is_array($filesList) )
                    <tbody>

                        @foreach( $filesList as $row )
                            <tr>
                                <td class="p-1 text-left txt-dark-grey">{{ $row["nombreFichero"] }}</td>
                                <td class="p-1 text-left txt-dark-grey">{{ $row["descFichero"] }}</td>
                                <td class="p-1 text-center txt-dark-grey">{{ $row["fechaDescarga"] }}</td>
                                <td class="p-1 text-center txt-dark-grey">
                                    <form class="download-documents" action="/download" method="get">
                                        <input type="hidden" name="fileId" class="fileId" value="{{ $row["codigo"] }}">
                                        <input type="hidden" name="filename" class="filename" value="{{ $row["nombreFichero"] }}">
                                        <input type="hidden" name="tipoFichero" class="tipoFichero" value="{{ $row["tipoFichero"] }}">
                                        <input type="hidden" name="downloadType" class="downloadType" value="file">
                                        <input type="submit" class="download-file-button txt-lime-yellow border-0 my-1 bg-white"  value=">> {{ __('text.download') }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    @endif
                </table>

            
        </div>
    </section>

@endsection

@include('app.layouts.modal')
