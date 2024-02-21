<div class="container">
    <div class="header">
        <h1>Комментарии</h1>
        <div class="add-comment">
            <button>Добавить комментарий</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($comments->count())
        <table>
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Комментарий</th>
                    <th>Добавлен</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comments as $comment)
                    <tr class="comment">
                        <td class="user-data">
                            <span class="user-pic">
                                @if($comment->user->image) 
                                    <img src="{{ asset('storage/'. str_replace('public/', '', $comment->user->image)) }}" style="width: 65px"; alt="Изображение пользователя">
                                @elseif($comment->user->file)
                                    <img src="{{ asset('storage/icon-text-file.png') }}" style="width: 65px"; alt="Изображение пользователя">
                                @endif
                            </span>
                            <span class="user-name">{{ $comment->user->name }}</span>
                        </td>
                        <td class="comment-text">
                            <span>{!! strip_tags($comment->content, '<a><code><i><strong>') !!}</span>
                            <div>
                                <a href="#" class="reply-link">Ответить</a>
                                @if ($comment->replies->isNotEmpty())
                                    <a href="#" class="show-replies">Посмотреть ответы</a>
                                @endif
                                <div class="reply-form" style="display: none;">
                                    @include('comments.form', ['comment' => $comment])
                                </div>
                                @if ($comment->replies->isNotEmpty())
                                    <ul class="replies" style="display: none;">
                                        @foreach ($comment->replies as $reply)
                                            @php
                                                $commentt = \App\Models\Comment::with('replies')->findOrFail($reply->reply_id);
                                            @endphp
                                            @include('comments.comment', ['comment' => $commentt])
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </td>
                        <td style="margin-bottom: 10px;">{{ $comment->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <nav class="navi">
            {{ $comments->links() }}
        </nav>
    @endif
</div>

<style>
    .container {
        /* width: 80%; */
        max-width: 1000px;
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
        margin-bottom: 20px;
    }
    .add-comment button {
        font-weight: bold;
        margin-top: 10px;
    }
    .add-comment h2 {
        margin-top: 9px;
    }
    table th {
        text-align: left;
    }
    table td {
        vertical-align: top;
    }
    tr.comment {
        height: 1.2em;
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
        width: 10em;
    }
    .user-data .user-name {
        margin-right: 10px;
        margin-left: 10px;
    }
    /* .user-data .user-pic {
        min-width: 75px;
    } */


</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            refreshCaptcha(e.target);
        });
    });

    $(document).ready(function(){
        $('.reply-link').click(function(){
            $(this).siblings('.reply-form').first().toggle();
        });

        $('.show-replies').click(function(e){
            e.preventDefault();
            $(this).siblings('.replies').toggle();
        });

        $('tr.comment').each(function(){
            var contentHeight = $(this).find('td').height(); // Получаем высоту контента в строке
            var newHeight = contentHeight * 1.2; // Устанавливаем высоту строки на 120% от высоты контента
            $(this).height(newHeight); // Устанавливаем новую высоту строки

            var containerWidth = $('.container').width();
            $(this).find('.comment-text').width(containerWidth * 0.5);
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

