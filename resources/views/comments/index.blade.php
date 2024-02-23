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
        <div class="table">
            <div class="tbody">
                @foreach ($comments as $comment)
                    <div class="tr comment">
                        <div class="td user-data">
                            <span class="user-pic">
                                @if($comment->user->image) 
                                    <img src="{{ asset('storage/'. str_replace('public/', '', $comment->user->image)) }}" alt="Изображение пользователя" width="45" height="45">
                                @elseif($comment->user->file)
                                    <img class="txt" src="{{ asset('storage/icon-text-file.png') }}" alt="Изображение пользователя" width="45" height="45">
                                @endif
                            </span>
                            <span class="user-name">{{ $comment->user->name }}</span>
                            <span class="td date">
                                {{ $comment->created_at->format('d.m.Y \в H:i') }}
                            </span>
                        </div>
                        <div class="td comment-text">
                            <span>{!! strip_tags($comment->content, '<a><code><i><strong>') !!}</span>
                            <div>
                                <div class="ctrls">
                                    @if ($comment->replies->isNotEmpty())
                                        <button class="show-replies hidd">Посмотреть ответы</button>
                                    @endif
                                    <button class="reply-link">Ответить</button>
                                </div>
                                <div class="reply-form" style="display: none;">
                                    @include('comments.form', ['comment' => $comment])
                                </div>
                                @if ($comment->replies->isNotEmpty())
                                    <ul class="replies" style="display: none;">
                                        @foreach ($comment->replies as $reply)
                                            @php
                                                $commentt = \App\Models\Comment::with('replies')->with('user')->findOrFail($reply->reply_id);
                                            @endphp
                                            @include('comments.comment', ['comment' => $commentt])
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <nav class="navi">
            {{ $comments->links() }}
        </nav>
    @endif
</div>

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
    .add-comment button {
        font-weight: bold;
        margin-top: 10px;
    }
    .add-comment h2 {
        margin-top: 9px;
    }

    .table .tr {
        /* display: flex; */
        margin: 10px 0 10px;
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
        margin-left: 4px; 
    }   
    .clearfix::after {
        content: "";
        display: table;
        clear: both;
        margin-bottom: 10px;
    } 
    .cancel {
        margin-left: 15px;
    }


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

