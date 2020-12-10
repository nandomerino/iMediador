@php
    App::setLocale('es');
@endphp

@extends('panel.layouts.core')

@section('content')
    <script src="/js/jscolor.js"></script>
    <script>
        jscolor.presets.default = {
            format:'hex', padding:5
        };
        jscolor.trigger('input change');
    </script>

    <section id="sliders">

        <section class="header pb-4 row">
            <div class="col">
                <div class="breadcrumbs txt-navy-blue pb-2"><a href="{{ __('panel.home.url') }}">{{ __('panel.home.text') }}</a> > {{ __('sidebar.menu.sliders.text') }}</div>
                <div class="separator bg-lime-yellow"></div>
            </div>
        </section>

        <section class="pb-5 row add-new">
            <div class="col">
                <button class="text-white bold py-2 px-5 border-0 rounded mt-4 position-relative bg-lime-yellow">
                    <img src="/img/add-circle.png">
                    {{ __('panel.sliders.newSlider') }}
                </button>
            </div>
        </section>

        <section class="pb-5 row go-back" style="display: none;">
            <div class="col">
                <button class="text-white bold py-2 px-5 border-0 rounded mt-4 position-relative bg-lime-yellow">
                    {{ __('text.back') }}
                </button>
            </div>
        </section>

        <section class="pb-5 row list-all">
            <div class="col">
                <table>
                    <thead>
                    <tr>
                        <th>
                            {{ __('panel.sliders.table.header.id') }}
                        </th>
                        <th>
                            {{ __('panel.sliders.table.header.name') }}
                        </th>
                        <th>
                            {{ __('panel.sliders.table.header.lastUpdate') }}
                        </th>
                        <th>
                            {{ __('panel.sliders.table.header.actions') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- Loaded dynamically--}}
                    </tbody>
                </table>
            </div>
        </section>

        <section class="pb-5 row new-slider" style="display:none;">
            <div class="col">
                <form name="slider-form" autocomplete="off">
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-2 field-wrapper px-0">
                            <span class="field-name">{{ __('panel.sliders.edit.name') }}</span>
                        </div>
                        <div class="col col-xl-4 field-wrapper px-0">
                            <input type="text" class="slider-name" name="name" placeholder="{{ __('panel.sliders.edit.name.placeholder') }}">
                        </div>
                    </div>
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-2 field-wrapper px-0">
                            <span class="field-name">{{ __('panel.sliders.edit.color') }}</span>
                        </div>
                        <div class="col col-xl-4 field-wrapper px-0">
                            <input type="text" id="slider-color" class="slider-color valid" name="color"  placeholder="{{ __('panel.sliders.edit.color.placeholder') }}" data-jscolor="" maxlength="7" >
                        </div>
                    </div>
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-2 field-wrapper px-0">
                            <span class="field-name">{{ __('panel.sliders.edit.title') }}</span>
                        </div>
                        <div class="col col-xl-4 field-wrapper px-0">
                            <input type="text" class="slider-header valid" name="title" placeholder="{{ __('panel.sliders.edit.title.placeholder') }}">
                        </div>
                    </div>
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-2 field-wrapper px-0">
                            <span class="field-name">{{ __('panel.sliders.edit.description') }}</span>
                        </div>
                        <div class="col col-xl-4 field-wrapper px-0">
                            <textarea  name="description" class="slider-description valid"></textarea>
                        </div>
                    </div>
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-2 field-wrapper px-0">
                            <span class="field-name">{{ __('panel.sliders.edit.image') }}</span>
                        </div>
                        <div class="col col-xl-4 field-wrapper px-0">
                            <input id="uploaded-image" type="file"  name="image" class="slider-image" accept=".jpg">
                        </div>
                    </div>
                    <div class="row px-3 pb-4">
                        <div class="col col-xl-6 px-0">
                            <img src="" id="preview-image" class="preview-image" style="display:none;">
                        </div>
                    </div>

                    <section class="row pb-4 save">
                        <div class="col col-xl-6 px-0 text-center">
                            <button class="text-white bold py-2 px-5 border-0 rounded mt-4 position-relative bg-lime-yellow" disabled>
                                <img src="/img/save.png">
                                {{ __('text.save') }}
                            </button>
                        </div>
                    </section>

                    <input type="hidden" name="slider-id" class="slider-id"value="">
                </form>
            </div>

    </section>

@endsection

@include('panel.layouts.modal')
