<div class="switch-lang-content">
    <div class="switch-lang" style="">
        <a @class(['switch-lang-item', 'active' => app()->getLocale() === 'km']) href="{{ route('admin-change-locale', 'km') }}">
            <img src="{{ asset('images/flag-khmer.png') }}" alt="">
            <i data-feather="check"></i>
        </a>
        <a @class(['switch-lang-item', 'active' => app()->getLocale() === 'en']) href="{{ route('admin-change-locale', 'en') }}">
            <img src="{{ asset('images/flag-english.png') }}" alt="">
            <i data-feather="check"></i>
        </a>
    </div>
</div>
