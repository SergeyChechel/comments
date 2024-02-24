<h2>Add Comment</h2>

@if ($errors->any())
    <div>
        <strong>Validation errors:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@include('comments.form')

