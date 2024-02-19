<form id="mainForm" method="POST" 
    @if(Route::currentRouteName() === 'comments.create')
    action="{{ route('comments.store') }}"
    @elseif(Route::currentRouteName() === 'comments.index')
    action="{{ route('comments.replies.store', ['comment' => $comment->id]) }}"
    @endif
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
    <div>
        <label for="image_or_file">Image or File:</label>
        <input type="file" id="image_or_file" name="image_or_file" accept=".jpg, .jpeg, .gif, .png, .txt" required title="разрешены только файлы JPG, GIF, PNG и TXT" onchange="handleFile()">
        {{--  --}}
    </div>
    @if(Route::currentRouteName() === 'comments.index')
        <input type="hidden" name="parent_id" value="{{$comment->id}}">
    @endif
    <button type="submit">Отправить</button>
</form>

<script>

    function checkHTMLTags() {
        // debugger;
        const textContent = document.getElementById('text').value;
        const disallowedTagsRegex = /<(?!\/?(a|code|i|strong)\b)[^>]*>/g; // можно только <a href=”” title=””> </a> <code> </code> <i> </i> <strong> </strong>
        if (textContent.match(disallowedTagsRegex)) {
            alert('Текст содержит недопустимые HTML теги.');
            return false;
        } else {
            return true;
        }
    }

    function handleFile() {
        const fileInput = document.getElementById('image_or_file');

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileName = file.name;
            const fileExtension = fileName.split('.').pop();

            if (fileExtension === 'txt') {
                const fileSizeInBytes = file.size;
                const fileSizeInKB = fileSizeInBytes / 1024;
                if (fileSizeInKB > 100) {
                    alert('Разрешенный размер текстового файла не более 100 KB');
                    return false;
                }
            } else if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = function() {
                        let width = img.width;
                        let height = img.height;
                        
                        // Если размер изображения больше 320x240, уменьшаем его пропорционально
                        if (width > 320 || height > 240) {
                            const ratio = Math.min(320 / width, 240 / height);
                            width *= ratio;
                            height *= ratio;

                            // Создаем элемент canvas для изменения размера изображения
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            canvas.width = width;
                            canvas.height = height;
                            
                            // Рисуем изображение на canvas с новыми размерами
                            ctx.drawImage(img, 0, 0, width, height);
                            
                            // Получаем измененное изображение в формате data URL
                            const resizedImg = canvas.toDataURL('image/jpeg');

                            // Создаем скрытый input для сжатого изображения
                            const compressedFile = dataURLtoFile(resizedImg, fileName);
                            const fileList = new DataTransfer();
                            fileList.items.add(compressedFile);

                            const compressedInput = document.createElement('input');
                            compressedInput.type = 'file';
                            compressedInput.name = 'image_or_file';
                            compressedInput.files = fileList.files;
                            fileInput.parentNode.appendChild(compressedInput);

                            // Удаляем оригинальный input
                            fileInput.parentNode.removeChild(fileInput);
                        }
                    };
                };
                reader.readAsDataURL(file);

            } else {
                alert('Допускаются только файлы изображения и текста');
                fileInput.value = '';
                return false;
            }
        }
    }

    // Вспомогательная функция для преобразования data URL в файл
    function dataURLtoFile(dataurl, filename) {
        const arr = dataurl.split(',');
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, { type: mime });
    }
   

</script>
