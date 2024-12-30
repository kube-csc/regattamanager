@props(['event'])

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-6">
                    <div class="footer-info" data-aos="fade-up" data-aos-delay="50">
                        <h3>{{ str_replace('_', ' ', env('VEREIN_NAME')) }}</h3>
                        <?php
                        /*
                          <p class="pb-3"><em>Qui repudiandae et eum dolores alias sed ea. Qui suscipit veniam excepturi quod.</em></p>
                          // QUESTION: Warum em
                        */
                        ?>
                        <p>
                            {{ str_replace('_', ' ', env('VEREIN_NAME')) }}<br>
                            {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
                            {{ str_replace('_', ' ', env('VEREIN_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}<br>
                            @if(env('VEREIN_TELEFON')<>"")
                                <i class="icofont-telephone"></i>{{ str_replace('_' , ' ' , env('VEREIN_TELEFON')) }}<br>
                            @endif
                            @if(env('VEREIN_FAX')<>"")
                                <i class="icofont-fax"></i>{{ str_replace('_' , ' ' , env('VEREIN_FAX')) }}<br>
                            @endif
                            <i class="icofont-email"></i>
                            <a href="mailto:{{ str_replace('_' , ' ' , env('VEREIN_EMAIL')) }}">{{ str_replace('_' , ' ' , env('VEREIN_EMAIL')) }}</a>
                        </p>

                        @if(env('APP_SOZIALMEDINANZEIGE')=="ja")
                            <div class="social-links mt-3">
                                @if(env('VEREIN_SOZIAL_FACEBOOK')!='')
                                    <a href="{{ str_replace('_' , ' ' , env('VEREIN_SOZIAL_FACEBOOK')) }}" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
                                @endif
                                @if(env('VEREIN_SOZIAL_TWITTER')!='')
                                    <a href="{{ str_replace('_' , ' ' , env('VEREIN_SOZIAL_TWITTER')) }}" class="twitter"><i class="bx bxl-twitter"></i></a>
                                @endif
                                @if(env('VEREIN_SOZIAL_INSTEGRAMM')!='')
                                    <a href="{{ str_replace('_' , ' ' , env('VEREIN_SOZIAL_INSTEGRAMM')) }}" class="instagram"><i class="bx bxl-instagram"></i></a>
                                @endif
                                @if(env('VEREIN_SOZIAL_SKYP')!='')
                                    <a href="{{ str_replace('_' , ' ' , env('VEREIN_SOZIAL_SKYP')) }}" class="google-plus"><i class="bx bxl-skype"></i></a>
                                @endif
                                @if(env('VEREIN_SOZIAL_LINKEDIN')!='')
                                    <a href="{{ str_replace('_' , ' ' , env('VEREIN_SOZIAL_LINKEDIN')) }}" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

                <!--<div class="col-lg-2 col-md-6 footer-links" data-aos="fade-up" data-aos-delay="150"> -->
                <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="100">
                  @include('textimport.footer')
                </div>

                <div class="col-lg-4 col-md-6 footer-newsletter" data-aos="fade-up" data-aos-delay="200">
                    @if($event)
                        @if($event->datumvona >= \Carbon\Carbon::now() && $event->datumbisa <= \Carbon\Carbon::now())
                            <h4>Intern</h4>
                            <ul>
                            @if(Auth::check())
                                <li><a href="{{ route('frontend.logout') }}"><i class="bx bx-log-out"></i> {{ __('main.Log Out') }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}"><i class="bx bx-log-in"></i> {{ __('main.Log In') }}</a></li>
                                <li><a href="/register"><i class="bx bx-log-in"></i> {{ __('main.Register') }}</a></li>
                            @endif
                            </ul>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="copyright">
            &copy; Copyright <strong><span>{{ str_replace('_', ' ', env('VEREIN_NAME')) }}</span></strong><br>
            All Rights Reserved
        </div>
        <div class="credits">
            <a href="/Information/Datenschutzerklaerung">Datenschutzerklärung</a> | <a href="/Impressum">Impressum</a>
        </div>
    </div>
</footer><!-- End Footer -->
