<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Comments')</title>
    <!-- Здесь можно добавить ссылки на ваши стили и скрипты -->
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <style>
        .hidden {
            display: none
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        ul {
            list-style-type: none;
        }
        button {
            margin-left: 5px;
        }
        .alert {
            margin-bottom: 10px;
        }
        .alert-success {
            color: green;
        }
        .alert-danger {
            color: red;
        }
        h1 {
            display: inline-block;
            margin: 0;
            min-width: 300px;
        }
        .header {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .add-comment button,
        .reply-form button {
            font-weight: bold;
            margin-top: 10px;
        }
        .add-comment h2 {
            margin-top: 9px;
        }
    
        .table .tr {
            margin: 30px 0 30px;
        }
        .thead .tr:first-of-type {
            margin: 30px 0 20px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .table .th {
            text-align: left;
            /* width: 33%; */
        }
        tr.comment {
            height: 1.2em;
        }
        .comment-text .ctrls {
            text-align: end;
        }
        .comment-text span {
            display: inline-block;
        }
        .comment-text > span {
            margin: 15px 0 15px;
        }
        .comment p {
            margin-bottom: 5px;
            margin-top: 10px;
        }
        svg {
            width: 1em;
            height: 1em;
        }
        .user-data {
            display: flex;
            align-items: center;
            background: #d3d3d347;
            min-height: 63px;
        }
        .user-pic img {
            width: 45px;
            height: 45px;
            border: 2px solid white;
            margin: 5px;
        }
        .user-data .user-name {
            font-weight: bold;
            margin-right: 10px;
            margin-left: 10px;
        }
        .user-data .date {
            font-size: 14px;
        }
        .user-pic img:not(.txt) {
            border-radius: 50%;
        }
        .navi nav div:first-of-type {
            text-align: center;
        }
        .navi nav div:first-of-type span, 
        .navi nav div:first-of-type a {
            margin: 15px;
        }
        form {
            max-width: 60%;
        }
        form label {
            display: inline-block;
            min-width: 100px;
        }
        form .captcha-label {
            float: left;
        }
        form .wrap {
            width: 30%;
            float: left;
        }   
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
            margin-bottom: 10px;
        } 
        .cancel {
            margin-left: 30px;
        }
        .tag-buttons {
            display: inline-block;
            margin: -7px 10px 7px 12px;
        }
        .image-or-file {
            margin-top: 10px;
        }
    
    
    </style>
</head>
<body>

    <!-- Основное содержимое -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Здесь можно добавить ваши скрипты -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> 

    <script>
        function cancel(e) {
            // debugger;
            e.preventDefault();
            const form = e.target.closest('form');
            const reply_form = form.querySelector('input[name="parent_id"]');
            if(reply_form) {
                form.closest('.reply-form').style.display = 'none';
            } else {
                const addComment = form.parentNode;
                const button = document.createElement('button');
                button.textContent = 'Добавить комментарий';
                addComment.parentNode.replaceChild(button, addComment);
            }
        } 
        

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

                                // Получаем измененное изображение в формате Blob
                                canvas.toBlob(function(blob) {
                                    // Создаем скрытый input для сжатого изображения
                                    const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
                                    const fileList = new DataTransfer();
                                    fileList.items.add(compressedFile);

                                    const compressedInput = document.createElement('input');
                                    compressedInput.type = 'file';
                                    compressedInput.name = 'image_or_file';
                                    compressedInput.files = fileList.files;
                                    fileInput.parentNode.appendChild(compressedInput);

                                    // Удаляем оригинальный input
                                    fileInput.parentNode.removeChild(fileInput);
                                }, 'image/jpeg');
                            }
                        };

                        // Загружаем изображение из объекта FileReader
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Допускаются только файлы изображения и текста');
                    fileInput.value = '';
                    return false;
                }
            }
        }

    </script>
    <script>

        // Функция для обновления CAPTCHA
        function refreshCaptcha(el) {
            // Отправка запроса на сервер для получения новой CAPTCHA
            fetch('/refresh-captcha')
                .then(response => response.json())
                .then(data => {
                    el.parentElement.querySelector('#captcha-image').src = data.captcha.img;
                })
                .catch(error => console.error('Ошибка при обновлении CAPTCHA:', error));
        }

        // Обработчик события клика по кнопке "Обновить CAPTCHA"
        document.querySelectorAll('.reply-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                refreshCaptcha(e.target.parentElement);
            });
        });

        $(document).ready(function(){
            $('.reply-link').click(function(){
                $(this).parent().siblings('.reply-form').first().toggle();
            });

            $('.show-replies').click(function(e){
                if(e.target.classList.contains('hidd')) {
                    e.target.textContent = "Скрыть ответы";
                } else {
                    e.target.textContent = "Просмотреть ответы";
                }
                e.target.classList.toggle('hidd');

                $(this).parent().siblings('.replies').toggle();
            });

            $('.add-comment button').click(function() {
            $.ajax({
                    url: '/comments/create', // URL для отправки запроса
                    method: 'GET', // HTTP-метод
                    success: function(response) {
                        $('.add-comment button').replaceWith(response); // Заменяем кнопку на полученный контент
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error); // Обработка ошибок
                    }
                });
            });
            
        });


    </script>
    <script>
        const formWraps = document.querySelectorAll('.main-form-wrap');
        formWraps.forEach(formWrap => {
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
            }).mount("#" + formWrap.id);
        });
    </script>
    
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
</body>
</html>
