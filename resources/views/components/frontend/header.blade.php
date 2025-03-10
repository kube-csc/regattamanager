@props(['event'])

<!-- ======= Header ======= -->
<header id="header" class="fixed-top header-transparent">
    <div class="container d-flex align-items-center">

        <div class="logo mr-auto">
            <h1 class="text-light"><a href="/"><span>{{ $_SERVER['HTTP_HOST'] }}</span></a></h1>
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
        </div>

        <nav class="nav-menu d-none d-lg-block">
            <ul>
                <li class="active"><a href="/">Home</a></li>
                <li><a href="/#about">Informationen</a></li>
                @if($event->anmeldetext && $event->beschreibung)
                   <li><a href="/Ausschreibung">Ausschreibung</a></li>
                @endif
                @if($event->datumvona && $event->datumbis && $event->email && now()->between($event->datumvona, $event->datumbis))
                <li><a href="/Meldung">Melden</a></li>
                <li><a href="/Regattateams">Gemeldete Teams</a></li>
                @endif
                <li><a href="/Anfahrt">Anfahrt</a></li>
                {{--
                ToDo: Implementierung der Registrierung von Accounts
                @if($event)
                    @if($event->datumvona >= \Carbon\Carbon::now() && $event->datumbisa <= \Carbon\Carbon::now())
                      @if(Auth::check())
                        <li><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li><a href="{{ route('frontend.logout') }}">{{ __('main.Log Out') }}</a></li>
                      @else
                        <li><a href="/login">{{ __('main.Log In') }}</a></li>
                        <li><a href="/register">{{ __('main.Register') }}</a></li>
                      @endif
                    @endif
                @endif
                --}}
            </ul>
        </nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->
