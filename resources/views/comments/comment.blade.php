<li class="comment">
    <p>{!! strip_tags($comment->content, '<a><code><i><strong>') !!}</p>
    <a href="#" class="reply-link">Ответить</a>
    <div class="reply-form" style="display: none;">
        @include('comments.form', ['comment' => $comment])
    </div>

    @if (isset($comment->replies) && $comment->replies->isNotEmpty())
        <ul class="replies">
            @foreach ($comment->replies as $reply)
            @php
                $commentt = \App\Models\Comment::with('replies')->findOrFail($reply->reply_id);
            @endphp
            @include('comments.comment', ['comment' => $commentt])
            @endforeach
        </ul>
    @endif
</li>





