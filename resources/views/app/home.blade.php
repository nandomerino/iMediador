@php

    App::setLocale('es');
    $wp = new \App\Http\Controllers\Wordpress();
    $data = $wp->get('pages', 'slug', 'app');
    $title = $data['title'];
    //$slider = $wp->get('pages', 'slug', 'home-privada-slider');

    $pm = new \App\Http\Middleware\PMWShandler();
    $currentLanguage = App::getLocale();
    $campaignGoals = $pm->getGoals();
    //app('debugbar')->info('campaignGoals');
    //app('debugbar')->info($campaignGoals);

@endphp

@extends('app.layouts.core')

@section('content')
    <script src="/js/splide.min.js"></script>
    <link rel="stylesheet" href="/css/splide.min.css">

    <div id="home">
        <div class="breadcrumbs txt-navy-blue pb-2">{{ __('menu.home.text') }}</div>
        <div class="separator bg-lime-yellow"></div>

        <section id="slider-home" class="pt-4">
            <div id="app-home-slider" class="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        {{--Dynamically generated--}}
                    </ul>
                </div>
            </div>
        </section>

        <section id="briefing" class="pt-4 pb-0">
            <p>{{ session('home.homeMessage1') }}</p>
            <p>{{ session('home.homeMessage2') }}</p>
        </section>

        @if( $campaignGoals )
            <section id="campaigns" class="py-4 ">
                <div class="row">
                    <div class="col">
                        <h2 class="mb-4">{{ __('app.home.campaigns') }}</h2>
                    </div>
                </div>
                <div class="row">
                    @foreach( $campaignGoals as $row )
                        @php

                            $rewards = "";
                            $anchoConseguido = [];
                            $nextReward = "";
                            $alreadyGotNext = false;
                            $progressBar = [];
                            $currentProgress = $row["valorActual"];


                            $i = 0;

                            foreach($row["tramosIncentivos"] as $row2){
                                $getNextReward = true;
                                // loads current rewards
                                if($row["valorActual"] >= $row2["hasta"]){
                                    $rewards .=  $row2["incentivo"] . ", ";
                                    $getNextReward = false;
                                }

                                // loads next reward
                                if( $getNextReward && !$alreadyGotNext){
                                    $nextReward = $row2["incentivo"];
                                    $getNextReward = false;
                                    $alreadyGotNext = true;
                                }
                                // Generate progress bar
                                $progressBar[$i] = $row2["hasta"];
                                if ($row2['objetivoConseguido'] == 'SI') {
                                    $anchoConseguido[$i] = $row2['porcentajeTotal'];
                                }
                                if ($row2['objetivoActual'] == 'SI') {
                                    $anchoActual = $row2['porcentajeTotal'];
                                    $porcentajeActualConseguido = $row2['porcentajeParcialConseguido'];

                                }

                                $i++;
                            }
                            // Removes last ,
                            if( strlen( $rewards ) > 0 ){
                                $rewards = substr($rewards, 0, -2);
                            }

                            //var_dump($anchoConseguido);
                            //var_dump($progressBar);
                            $totalConseguido = array_sum($anchoConseguido);
                            $anchoActualRestante = ($porcentajeActualConseguido * $anchoActual) / 100;
                            $anchoActualConseguido = $anchoActual -$anchoActualRestante;
                            // Load progress bar
                            rsort($progressBar);
                            $barsHTML = "";
                            $i=1;
                            // Current progress
                            $fullWidth = $progressBar[0];
                            $width = ($currentProgress * 100) /  $fullWidth;

                            $barsHTML .= '<div class="progress-bar" role="progressbar" style="width: '. $totalConseguido .'%" aria-valuenow="'. $totalConseguido .'" aria-valuemin="0" aria-valuemax="100"></div>';
                            $barsHTML .= '<div class="progress-bar bg-success" role="progressbar" style="width: '. $anchoActualRestante .'%" aria-valuenow="'. $anchoActualRestante .'" aria-valuemin="0" aria-valuemax="100"></div>';
                            $barsHTML .= '<div class="progress-bar bg-info" role="progressbar" style="width: '.$anchoActualConseguido.'%" aria-valuenow="'.$anchoActualConseguido.'" aria-valuemin="0" aria-valuemax="100"></div>';
                            //$barsHTML .= "<div class='PM-progress-bar text-center current-progress-bar bar-" . $i . "' style='width: " . $width . "%' >" . $currentProgress . "</div>";
                            // Goals
                            /*foreach($progressBar as $row3){
                                $width = ($row3 * 100) /  $fullWidth;

                                $barsHTML .= "<div class='PM-progress-bar bar-" . $i . "' style='width: " . $width . "%' >&nbsp;</div>";
                                $i++;
                            }*/

                        @endphp

                        <div class="col-12 col-md-12 pt-4 pt-md-0">
                            <div class="card campaign">
                                <div class="card-header bg-white">
                                    <h4 class="card-title m-0 text-center">{{ $row["titulo"] }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="description">
                                        {{ $row["descripcion"] }}
                                    </div>
                                    <div class="separator bg-navy-blue my-2">&nbsp;</div>
                                    <div class="rewards">
                                        <h5 class="text-center">{{ __('campaigns.rewards.title') }}</h5>
                                        <span class="font-weight-bold">{{ __('campaigns.rewards.current') }}: {{ $rewards }}</span>
                                        @if( $nextReward != "" )
                                            <br>
                                            <span class="font-weight-bold">{{ __('campaigns.rewards.next') }}: {{ $nextReward }}</span>
                                        @else
                                            <br>
                                            <br>
                                        @endif
                                    </div>
                                    <div class="separator bg-navy-blue my-2">&nbsp;</div>
                                    <div class="current-progress">
                                        <h5 class="text-center">{{ __('campaigns.progress.title') }}</h5>
                                        <div class="progress-text" style="color: #fff;">{{ $currentProgress }}  ({{ $porcentajeActualConseguido }}%)</div>
                                        <div class="progress" style="background-color: #c3c5c7;">
                                            {!! $barsHTML !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if( session('home.infoBoxes') )
            <section id="info-boxes" class="py-4">
                <div class="row mx-n2">
                    @foreach( session('home.infoBoxes') as $row )
                        <div class="col bg-navy-blue text-white text-center p-2 m-2">
                            <div class="data">{{ $row["data"] }}</div>
                            <div class="name">{{ $row["name"] }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section id="shortcut-buttons" class="pb-4">
            <div class="row">
                <div class="col-12 col-md-4 text-center mb-3">
                    <a href="{{ __('menu.quote.url') }}">
                        <div class="shortcut-button bg-lime-yellow text-white p-4 rounded">
                            <img src="/img/tarificacion blanco.png"> {{ __('menu.quote.text') }}
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4 text-center mb-3">
                    <a href="https://demo.laprevisionmallorquina.com/iMediador_demo/index.jsp?codigoAcceso={{ session('login.tokenAcceso') }}" target="_blank">
                        <div class="shortcut-button bg-lime-yellow text-white p-4 rounded">
                            <img src="/img/document.png"> {{ __('menu.queries.text') }}
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <a href="{{ __('menu.support.url') }}" class="">
                        <div class="shortcut-button bg-lime-yellow text-white p-4 rounded">
                            <img src="/img/headset.png"> {{ __('menu.support.text') }}
                        </div>
                    </a>
                </div>
            </div>
        </section>

        @if( session('home.recentActivity') )
            <section id="recent-activity" class="py-4">
                <h2>{{ __('app.home.recentActivity') }}</h2>

                @foreach( session('home.recentActivity') as $row )
                    <div class="item">
                        <h3 class="txt-lime-yellow mt-4 mb-2">{{ $row["name"] }} <a class="primary-color arrow-collapse collapsed" data-toggle="collapse" data-target="#collapse{{ $row["data"] }}" aria-expanded="false" aria-controls="collapse{{ $row["data"] }}" style="float: right;"><i class="fas fa-plus"></i></a> </h3>
                        <div class="data-list collapse" id="collapse{{ $row["data"] }}">
                            <table>
                                <thead>
                                <tr>
                                    @foreach( $row["table"]["header"] as $row2 )
                                        <th>
                                            {{ $row2 }}
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $row["table"]["rows"] as $row2 )
                                    <tr>
                                        @foreach( $row2 as $row3 )
                                            <td>
                                                {{ $row3 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
        {{--<section id="metrics" class="py-4">
            <h2>{{ __('app.home.metrics') }}</h2>
            <!-- tables -->

            <!-- charts -->

        </section>--}}
        <section id="latest-news" class="py-4">
            <h2 class="d-inline-block mb-4">{{ __('app.home.latestNews') }}</h2> <a class="pl-3 txt-lime-yellow" href="{{ __('menu.news.url') }}">{{ __('text.viewMore') }}</a>
            {!! $data['content']  !!}
        </section>
    </div>
@endsection

