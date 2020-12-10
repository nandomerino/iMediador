<aside class="py-4 px-3">
    <div id="side-menu" class="card">
        <div class="card-body">
            <div class="menu-logos">
                <img src="/img/logo-imediador.png" alt="{{ __('menu.logo.imediador.text') }}"><img src="/img/logo-pm.png" alt="{{ __('menu.logo.pm.text') }}">
            </div>
            <div class="description txt-navy-blue mt-3">{{ __('sidebar.menu.shortcuts') }}</div>
            <div class="separator bg-navy-blue my-2">&nbsp;</div>
            <nav class="nav flex-column px-0">
                <a class="nav-link txt-navy-blue px-0" href="{{ __('sidebar.menu.quote.url') }}" class="">
                    <div class="imageWrapper d-inline-block">
                        <img src="/img/calculator.png">
                    </div>{{ __('sidebar.menu.quote.text') }}
                </a>
                <div class="separator bg-light-grey my-2">&nbsp;</div>
                <a class="nav-link txt-navy-blue px-0" href="{{ __('sidebar.menu.send-policy-request.url') }}" class="">
                    <div class="imageWrapper d-inline-block">
                        <img src="/img/certificate.png">
                    </div>{{ __('sidebar.menu.send-policy-request.text') }}
                </a>
                <div class="separator bg-light-grey my-2">&nbsp;</div>
                <a class="nav-link txt-navy-blue px-0" href="{{ __('sidebar.menu.downloadDocuments.url') }}" class="">
                    <div class="imageWrapper d-inline-block">
                        <img src="/img/download-blue.png">
                    </div>{{ __('sidebar.menu.downloadDocuments.text') }}
                </a>

            </nav>
            <div class="separator bg-navy-blue my-2">&nbsp;</div>
        </div>
    </div>
</aside>
