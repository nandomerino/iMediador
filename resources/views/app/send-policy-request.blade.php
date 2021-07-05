@php
    App::setLocale('es');
    $title = __('menu.quote.text');
    $pm = new \App\Http\Middleware\PMWShandler();
    $currentLanguage = App::getLocale();

@endphp

@extends('app.layouts.core')

@section('content')

    <section class="header pb-4 row">
        <div class="col">
            <div class="breadcrumbs txt-navy-blue pb-2"><a href="{{ __('menu.home.url') }}">{{ __('menu.home.text') }}</a> > {{ __('sidebar.menu.send-policy-request.text') }}</div>
            <div class="separator bg-lime-yellow"></div>
        </div>
    </section>

    <section id="downloads" class="pb-5">
        <form id="send-policy-request"  autocomplete="off">
            <div class="select-user row mt-5">
                <div class="col-12 col-md-6 pt-2 text-right mb-2 mb-md-0">
                    <label for="productor">{{ __('uploadPolicyRequest.pick.productor') }}</label>
                </div>
                <div class="col-12 col-md-6">
                    <select class="form-control productor" id="productor" name="productor" autocomplete="off" required>
                        <option value="" disabled selected></option>
                        @php
                            try {
                                $productores = $pm->getProductores();
                            } catch (Throwable $e) {
                                report($e);
                                return false;
                            }
                        @endphp
                        @if( $productores )
                            @if(empty($productores["id"]))
                                @foreach( $productores as $row )
                                    <option value="{{ $row["id"] }}">{{ $row["name"] }}</option>
                                @endforeach
                            @else
                                <option value="{{ $productores["id"] }}">{{ $productores["name"] }}</option>
                            @endif

                        @endif
                    </select>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-12 col-md-6 pt-2 text-right mb-2 mb-md-0">
                    <label for="doc">{{ __('text.chooseFile') }}</label>
                </div>
                <div class="col-12 col-md-6">
                    <input type="file" name="doc" class="doc" id="doc" accept="application/pdf" required>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-12 col-md-6 pt-2 text-right mb-2 mb-md-0">
                    <label for="refId">{{ __('uploadPolicyRequest.policyRequestCode') }}</label>
                </div>
                <div class="col-12 col-md-6">
                    <input type="text" name="refId" class="refId" required>
                </div>
            </div>
            <div class="row">
                <div class="col text-center">
                    <input type="hidden" name="docId" class="docId" value="467">
                    <input type="hidden" name="folderId" class="folderId" value="65">
                    <input type="hidden" name="docType" class="docType" value="application/pdf">
                    <input type="hidden" name="sendType" class="sendType" value="policyRequest">
                    <button id="send-policy-request-button" class="send-policy-request-button bg-lime-yellow text-white py-2 px-3 rounded border-0 position-relative" disabled>
                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                        {{ __('text.send') }}
                    </button>
                </div>
            </div>
        </form>
    </section>

@endsection

@include('app.layouts.modal')
