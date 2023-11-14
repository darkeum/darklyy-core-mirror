<div class="divider my-2">
    <div class="divider-text">
        @lang('profile::lang.auth.login.or')
    </div>
</div>

<div class="auth-footer-btn d-flex justify-content-center">
    <a href="{{ route('social.redirect', 'vk') }}" class="btn btn-vk">
        <i class="fab fa-vk"></i>
    </a>
    <a href="{{ route('social.redirect', 'yandex') }}" class="btn btn-openid white">
        <i class="fab fa-yandex"></i>
    </a>
    <a href="{{ route('social.redirect', 'google') }}" class="btn btn-google">
        <i class="fab fa-google"></i>
    </a>
</div>