@extends('layouts.app')

@section('content')
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
@endsection
