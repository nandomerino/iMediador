<nav id="top-menu" class="container navbar navbar-expand-md px-0 py-0 text-white">
    <div class="navbar-brand text-left">
        <a class="text-decoration-none" href="{{ __('menu.logo.imediador.url') }}">
            <img class="navbar-brand" src="/img/logo-imediador-small.png" alt="{{ __('menu.logo.imediador.text') }}">
        </a>
        <a class="text-decoration-none" href="{{ __('menu.logo.pm.url') }}" target="_blank">
            <img class="navbar-brand" src="/img/logo-pm-small-blanco.png" alt="{{ __('menu.logo.pm.text') }}">
        </a>
    </div>
    {{--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarText" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>--}}
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav w-100">
            <li class="nav-item m-auto">
                <a class="nav-link d-inline" style="position: relative; top: 2px;" href="/" class="">{{ __('menu.home.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.contact.url') }}" class="">{{ __('menu.contact.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.info.url') }}">{{ __('menu.info.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.help.url') }}">{{ __('menu.help.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="tel:999888777"><i class="fas fa-phone-alt large"></i>&nbsp;999888777</a>
            </li>
        </ul>
    </div>
    <div class="d-block d-sm-block d-md-none">&nbsp;</div>
</nav>
<div class="d-block d-sm-block d-md-none mobile-menu bg-navy-blue">
    <div class="icon text-right">
        <i class="fa fa-bars text-white" aria-hidden="true"></i>
    </div>
    <div class="navbar-nav hidden">
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.contact.url') }}" class=""><i class="fas fa-chevron-right large"></i>{{ __('menu.contact.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.info.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.info.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.help.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.help.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="tel:999888777"><i class="fas fa-phone-alt large"></i>&nbsp;999888777</a>
        </li>
    </div>
</div>
