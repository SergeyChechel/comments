<form id="mainForm" method="POST" 
    @if(Route::currentRouteName() === 'comments.create')
    action="{{ route('comments.store') }}"
    @elseif(Route::currentRouteName() === 'comments.index')
    action="{{ route('comments.replies.store', ['comment' => $comment->id]) }}"
    @endif
    enctype="multipart/form-data"
    onsubmit="checkForm(e)"
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
    </div>
    @if(Route::currentRouteName() === 'comments.index')
        <input type="hidden" name="parent_id" value="{{$comment->id}}">
    @endif
    <button type="submit">Отправить</button>
</form>

<script>
    let formData;

    function checkForm(e) {
        debugger;
        e.preventDefault();
        if(checkHTMLTags()) {
            mixFormData(formData);
        };
    }

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

                        // Создаем объект FormData
                        formData = new FormData();
                        formData.append('resizedImage', resizedImg);
                        console.log(formData.get('resizedImage'));
                        return true;
                    }
                };
            };
            reader.readAsDataURL(file);

            return false;
        }
    }
}

function mixFormData(formData) {
    debugger;

    const form = document.getElementById('mainForm');
    const mainFormData = new FormData(form);

    // Добавляем остальные данные формы в объект FormData
    for (const [key, value] of mainFormData.entries()) {
        formData.append(key, value);
    }
    console.log(formData);
    return;
    // Отправляем FormData на сервер с помощью Fetch API
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Проверяем успешность ответа
        if (!response.ok) {
            // Если ответ не успешен, генерируем ошибку
            throw new Error('Ошибка HTTP: ' + response.status);
        }
        // Обработка успешного ответа от сервера
        console.log('Форма успешно отправлена');
    })
    .catch(error => {
        // Обработка ошибки
        console.error('Ошибка отправки формы:', error);
    });
}




</script>
