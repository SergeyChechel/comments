<form method="POST" 
    @if(Route::currentRouteName() === 'comments.create')
    action="{{ route('comments.store') }}"
    @elseif(Route::currentRouteName() === 'comments.index')
    action="{{ route('comments.replies.store', ['comment' => $comment->id]) }}"
    @endif
    onsubmit="return checkHTMLTags()"
>
    @csrf
    <div>
        <label for="user_name">User Name:</label>
        <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" pattern="[a-zA-Z0-9]+" required>
    </div>
    <div>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div>
        <label for="home_page">Home Page:</label>
        <input type="url" id="home_page" name="home_page" value="{{ old('home_page') }}">
    </div>
    <div>
        <label for="captcha">CAPTCHA:</label>
        @if(Route::currentRouteName() === 'comments.create')
            <img src="{{ Captcha::src() }}" alt="CAPTCHA">
        @elseif(Route::currentRouteName() === 'comments.index')
            <img id="captcha-image" src="{{ asset('images/blank_captcha.png') }}" alt="CAPTCHA">
        @endif
        <input type="text" id="captcha" name="captcha" pattern="[a-zA-Z0-9]+" required>
    </div>
    <div>
        <label for="text">Text:</label>
        <textarea id="text" name="text" title="разрешенные теги только <a href=”” title=””></a> <code></code> <i></i> <strong></strong>" required>{{ old('text') }}</textarea>
    </div>
    @if(Route::currentRouteName() === 'comments.index')
        <input type="hidden" name="parent_id" value="{{$comment->id}}">
    @endif
    <button type="submit">Отправить</button>
</form>

<script>
    function checkHTMLTags() {
        const textContent = document.getElementById('text').value;
        const disallowedTagsRegex = /<(?!\/?(a|code|i|strong)\b)[^>]*>/g; // можно только <a href=”” title=””> </a> <code> </code> <i> </i> <strong> </strong>
        if (textContent.match(disallowedTagsRegex)) {
            alert('Текст содержит недопустимые HTML теги.');
            return false;
        } else {
            return true;
        }
    }
</script>
