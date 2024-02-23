<li class="comment">
    <div class="tr comment">
        <div class="td user-data">
            <span class="user-pic nested">
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
                    <button class="reply-link">Ответить</button>
                </div>
                <div class="reply-form" style="display: none;">
                    @include('comments.form', ['comment' => $comment])
                </div>
                @if ($comment->replies->isNotEmpty())
                    <ul class="replies" > 
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
</li>