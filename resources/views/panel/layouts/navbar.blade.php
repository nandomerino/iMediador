<nav id="top-menu" class="container navbar navbar-expand-md px-0 py-0 text-white">
    <div class="navbar-brand text-left p-0 m-0">
        <a class="navbar-brand" href="{{ config('filesystems.disks.panel.home') }}">
            <img src="/img/logo-imediador-small.png" alt="{{ __('menu.logo.imediador.text') }}">
        </a>
        <a class="navbar-brand" href="{{ __('menu.logo.pm.url') }}" target="_blank">
            <img class="navbar-brand" src="/img/logo-pm-small.png" alt="{{ __('menu.logo.pm.text') }}">
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav w-100">
            <li class="nav-item m-auto">
                <a class="nav-link text-white" href="{{ __('sidebar.menu.impersonator.url') }}">{{ __('sidebar.menu.impersonator.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                <a class="nav-link text-white" href="{{ __('sidebar.menu.sliders.url') }}">{{ __('sidebar.menu.sliders.text') }}</a>
            </li>
            <li class="nav-item m-auto">
                &nbsp;
            </li>
            <li class="nav-item m-auto">
                &nbsp;
            </li>
            <li class="nav-item m-auto px-3 py-3 bg-navy-blue">
                <a id="logout" class="nav-link text-white" href="{{ __('app.user.logout.url') }}">{{ __('app.user.logout.text') }}</a>
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
            <a class="nav-link text-white" href="{{ __('sidebar.menu.impersonator.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('sidebar.menu.impersonator.text') }}</a>
        </li>
        <li class="nav-item text-left">
            <a class="nav-link text-white" href="{{ __('sidebar.menu.sliders.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('sidebar.menu.sliders.text') }}</a>
        </li>
        </li>
        <li class="nav-item text-left bg-light-blue">
            <a id="logout" class="nav-link text-white" href="{{ __('app.user.logout.url') }}"><i class="fas fa-chevron-right large"></i>{{ __('app.user.logout.text') }}</a>
        </li>
    </div>
</div>
