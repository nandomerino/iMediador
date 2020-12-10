<aside class="py-4 px-3">
    <div id="side-menu" class="card">
        <div class="card-body">
            <div class="description txt-navy-blue mt-3">{{ __('sidebar.menu.shortcuts') }}</div>
            <div class="separator bg-navy-blue my-2">&nbsp;</div>
            <nav class="nav flex-column px-0">
                <a class="nav-link txt-navy-blue px-0" href="{{ __('sidebar.menu.impersonator.url') }}" class="">
                    <div class="imageWrapper d-inline-block">
                        <img src="/img/card.png">
                    </div>{{ __('sidebar.menu.impersonator.text') }}
                </a>
                <div class="separator bg-light-grey my-2">&nbsp;</div>
                <a class="nav-link txt-navy-blue px-0" href="{{ __('sidebar.menu.sliders.url') }}" class="">
                    <div class="imageWrapper d-inline-block">
                        <img src="/img/certificate.png">
                    </div>{{ __('sidebar.menu.sliders.text') }}
                </a>

            </nav>
        </div>
    </div>
</aside>
