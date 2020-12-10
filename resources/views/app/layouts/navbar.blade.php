<nav id="top-menu" class="container navbar navbar-expand-md px-0 py-0 text-white">
    <div class="navbar-brand text-left d-block d-sm-block d-md-none">
        <a class="text-decoration-none" href="{{ __('menu.logo.imediador.url') }}">
            <img class="navbar-brand" src="/img/logo-imediador-small.png" alt="{{ __('menu.logo.imediador.text') }}">
        </a>
        <a class="text-decoration-none" href="{{ __('menu.logo.pm.url') }}" target="_blank">
            <img class="navbar-brand" src="/img/logo-pm-small-blanco.png" alt="{{ __('menu.logo.pm.text') }}">
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav w-100">
            <li class="nav-item m-auto">
                <img src="/img/house.png" class="d-inline mr-1">
                <a class="nav-link d-inline" style="position: relative; top: 2px;" href="{{ __('menu.home.url') }}" class="">{{ __('menu.home.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link quote-link" href="{{ __('menu.quote.url') }}">{{ __('menu.quote.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link quote-link" href="{{ __('menu.queries.url') }}">{{ __('menu.queries.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.documentation.url') }}">{{ __('menu.documentation.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.support.url') }}">{{ __('menu.support.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link" href="{{ __('menu.news.url') }}">{{ __('menu.news.text') }}</a>
            </li>
            <li class="nav-item m-auto px-3 py-3 bg-light-blue">
                <a id="logout" href="{{ __('app.user.logout.url') }}">{{ __('app.user.logout.text') }}</a>
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
            <a class="nav-link text-white" href="{{ __('menu.home.url') }}" class=""><i class="fas fa-chevron-right large"></i>{{ __('menu.home.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.documentation.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.documentation.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.support.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.support.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('menu.news.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.news.text') }}</a>
        </li>
        <li class="nav-item text-left bg-lime-yellow txt-navy-blue">
            <a class="nav-link text-white" href="{{ __('sidebar.menu.quote.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('sidebar.menu.quote.text') }}</a>
        </li>
        <li class="nav-item text-left bg-lime-yellow txt-navy-blue">
            <a class="nav-link text-white" href="{{ __('menu.queries.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('menu.queries.text') }}</a>
        </li>
        <li class="nav-item text-left bg-lime-yellow txt-navy-blue">
            <a class="nav-link text-white" href="{{ __('sidebar.menu.send-policy-request.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('sidebar.menu.send-policy-request.text') }}</a>
        </li>
        <li class="nav-item text-left bg-lime-yellow txt-navy-blue">
            <a class="nav-link text-white" href="{{ __('sidebar.menu.downloadDocuments.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('sidebar.menu.downloadDocuments.text') }}</a>
        </li>
        <li class="nav-item text-left bg-light-blue">
            <a id="logout" class="nav-link text-white" href="{{ __('app.user.logout.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('app.user.logout.text') }}</a>
        </li>
    </div>
</div>