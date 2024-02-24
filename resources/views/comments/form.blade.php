@if(Route::currentRouteName() === 'comments.create')
@php
    $form = 'new';
@endphp
@elseif(Route::currentRouteName() === 'comments.index')
@php
    $form = $comment->id;
@endphp
@endif

<div id="app-{{ $form }}" class="main-form-wrap">
    <form class="main-form" method="POST" 
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
                @if($form == 'new')
                    <img class="captcha-image" src="{{ Captcha::src() }}" alt="CAPTCHA">
                @elseif($form != 'new')
                    <img id="captcha-image" class="captcha-image" src="{{ asset('images/blank_captcha.png') }}" alt="CAPTCHA">
                @endif

            </div>
        </div>
        <div class="clearfix"></div>
        <div>
            <label for="text">Text:</label>
            <div class="tag-buttons">
                <button @click.prevent="insertTag('<i>', '</i>')">[i]</button>
                <button @click.prevent="insertTag('<strong>', '</strong>')">[strong]</button>
                <button @click.prevent="insertTag('<code>', '</code>')">[code]</button>
                <button @click.prevent="insertTag('<a>', '</a>')">[a]</button>
            </div>
            <textarea id="text" name="text" v-model="text" rows="7" cols="40"></textarea>
        </div>
        <div class="image-or-file">
            <label for="image_or_file">Image or File:</label>
            <input type="file" id="image_or_file" name="image_or_file" accept=".jpg, .jpeg, .gif, .png, .txt" title="разрешены только файлы JPG, GIF, PNG и TXT" onchange="handleFile()">
        </div>
        @if($form != 'new')
            <input type="hidden" name="parent_id" value="{{$comment->id}}">
        @endif
        <button type="submit">Отправить</button>
        <a href="" class="cancel" onclick="cancel(e)">Отменить</a>
    </form>
</div>

@if($form == 'new')
<script>
        Vue.createApp({
            data() {
                return {
                    text: ''
                };
            },
            methods: {
                insertTag(startTag, endTag) {
                    const textarea = document.getElementById('text');
                    const startPos = textarea.selectionStart;
                    const endPos = textarea.selectionEnd;
                    if(startTag === '<a>') {
                        startTag = '<a href="" title="" target="_blank">'
                    }

                    this.text = this.text.substring(0, startPos) + startTag + this.text.substring(startPos, endPos) + endTag + this.text.substring(endPos);
                },
            }
        }).mount("#app-{{ $form }}");

</script>
@endif