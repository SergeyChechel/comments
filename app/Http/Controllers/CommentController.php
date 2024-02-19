<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::with(
            ['replies' => fn ($query) => $query->latest()]
        )
        ->with('user')
        ->where('is_reply', false)
        ->latest()
        ->paginate(3);
        return view('comments.index', compact('comments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
                $validator = Validator::make($request->all(), [
                    'user_name' => 'required|regex:/^[a-zA-Z0-9]+$/',
                    'email' => 'required|email',
                    'home_page' => 'nullable|url',
                    'text' => ['required', 'not_regex:/<(?!\/?(a|code|i|strong)\b)[^>]*>/'],
                    'captcha' => 'required|captcha'
                ]);
        
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $user = User::where('email', $request->email)->first();

                if (!$user) {
                    $user = new User();
                    $user->name = $request->user_name;
                    $user->email = $request->email;
                    $user->homepage = $request->home_page;
                    $user->save();
                }

                $comment = new Comment();
                $comment->user_id = $user->id;
                $comment->content = $request->text;
                $comment->save();
        
                return redirect()->route('comments.index')->with('success', 'Comment added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
