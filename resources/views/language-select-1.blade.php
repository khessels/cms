<form action="{{ route('post.language.switch') }}" method="POST">
    @csrf
    <select name="language" onchange="this.form.submit()">
        @foreach( config('app.available_locales') as $locale)
            @php
                $selected = '';
                if( app()->getLocale() === $locale){
                    $selected = 'selected';
                }
            @endphp
            <option value="{{ $locale }}" {{ $selected }}>@c(['key' => $locale, 'default' => $locale ])</option>
        @endforeach
    </select>
</form>
