<div class="change-language">
    <div class="change-language-row">
        <div class="change-language-row-item" :class="{ 'active': locale == arrayLangLocale.en }" @click="locale = arrayLangLocale.en">
            <span>@lang('form.locale.en')</span>
        </div>
        <div class="change-language-row-item" :class="{ 'active': locale == arrayLangLocale.km }" @click="locale = arrayLangLocale.km">
            <span>@lang('form.locale.km')</span>
        </div>
    </div>
</div>
