@vite(['resources/js/components/basic-header.ts'])

<header class="navbar navbar-light bg-white shadow sticky-top py-0">
    <div class="container-fluid h-100">
        <a href="/" class="link-underline link-underline-opacity-0"><h1 class="mb-0 fw-bold text-primary">Spencer</h1></a>

        @php
            $langMapping = [
                'czech'   => 'cz',
                'english' => 'en',
                'german'  => 'de'
            ];
        @endphp

        <select name="language" class="form-select w-auto">
            @foreach($languages as $option)
                @php
                    $localeCode = $langMapping[$option->option_data] ?? $option->option_data;
                @endphp
                <option value="{{ $localeCode }}"
                    {{ app()->getLocale() == $localeCode ? 'selected' : '' }}>
                    {{ strtoupper($localeCode) }}
                </option>
            @endforeach
        </select>
    </div>
</header>
