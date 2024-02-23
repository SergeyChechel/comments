<form id="mainForm" method="POST" 
    action="{{ route('comments.store') }}"
    enctype="multipart/form-data"
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
        <label for="captcha" class="captcha-label">CAPTCHA:</label>
        <div class="wrap">
            <input type="text" id="captcha" name="captcha" pattern="[a-zA-Z0-9]+" required>
            @if(Route::currentRouteName() === 'comments.create')
                <img class="captcha-image" src="{{ Captcha::src() }}" alt="CAPTCHA">
            @elseif(Route::currentRouteName() === 'comments.index')
                <img id="captcha-image" class="captcha-image" src="{{ asset('images/blank_captcha.png') }}" alt="CAPTCHA">
            @endif

        </div>
    </div>
    <div class="clearfix"></div>
    <div>
        <label for="text">Text:</label>
        <textarea id="text" name="text" title="разрешенные теги только <a href=”” title=””></a> <code></code> <i></i> <strong></strong>" required>{{ old('text') }}</textarea>
    </div>
    <div>
        <label for="image_or_file">Image or File:</label>
        <input type="file" id="image_or_file" name="image_or_file" accept=".jpg, .jpeg, .gif, .png, .txt" title="разрешены только файлы JPG, GIF, PNG и TXT" onchange="handleFile()">
    </div>
    @if(Route::currentRouteName() === 'comments.index')
        <input type="hidden" name="parent_id" value="{{$comment->id}}">
    @endif
    <button type="submit">Отправить</button>
    <a href="" class="cancel" onclick="cancel(e)">Отменить</a>
</form>