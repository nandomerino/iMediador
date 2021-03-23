<div class="container hiddenprint">
    <div class="row socialIcons position-relative justify-content-md-center">
        <div class="col-2 text-center">
            <a href="{{ __('social.facebook.url') }}" target="_blank">
                <i class="fab fa-facebook-f fa-2x bg-lime-yellow rounded-circle p-2" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-2 text-center">
            <a href="{{ __('social.twitter.url') }}" target="_blank">
                <i class="fab fa-twitter fa-2x bg-lime-yellow rounded-circle p-2" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-2 text-center">
            <a href="{{ __('social.linkedin.url') }}" target="_blank">
                <i class="fab fa-linkedin-in fa-2x bg-lime-yellow rounded-circle p-2" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="row py-5">
        <div class="col text-center">
            <a href="{{ __('footer.avisolegal.url') }}">
                {{ __('footer.avisolegal.text') }}
            </a>
        </div>
        <div class="col text-center">
            <a href="{{ __('footer.rgpd.url') }}">
                {{ __('footer.rgpd.text') }}
            </a>
        </div>
        <div class="col text-center">
            <a href="{{ __('footer.cookies.url') }}">
                {{ __('footer.cookies.text') }}
            </a>
        </div>
        <div class="col text-center">
            {{ __('footer.copyright.text') }} @php echo date("Y"); @endphp
        </div>
    </div>
</div>
