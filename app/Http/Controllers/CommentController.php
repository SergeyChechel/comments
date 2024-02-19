<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

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
        ->paginate(5);
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
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|regex:/^[a-zA-Z0-9]+$/',
            'email' => 'required|email',
            'home_page' => 'nullable|url',
            'text' => ['required', 'not_regex:/<(?!\/?(a|code|i|strong)\b)[^>]*>/'],
            'captcha' => 'required|captcha',
            'image_or_file' => 'nullable|file|mimes:jpeg,png,gif,txt|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('image_or_file')) {
            // Получаем файл из запроса
            $file = $request->file('image_or_file');
            
            // Получаем оригинальное имя файла
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('public', $fileName);

            // Получаем расширение файла
            $fileExtension = $file->getClientOriginalExtension();
            
            // if(!$fileExtension === 'txt') {
            //     $fullFilePath = storage_path('app/'.$filePath);
            //     $image = Image::make($fullFilePath);
            //     if ($image->width() > 320 || $image->height() > 240) {
            //         // Уменьшаем размер изображения до максимальных значений 320x240 пикселей
            //         $image->resize(320, 240, function ($constraint) {
            //             $constraint->aspectRatio(); // Поддерживаем пропорции изображения
            //             $constraint->upsize(); // Не увеличиваем размер, если изначальный меньше указанных размеров
            //         });
            //         // Сохраняем измененное изображение
            //         $image->save($fullFilePath);
            //     }
            // }
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = new User();
            $user->name = $request->user_name;
            $user->email = $request->email;
            $user->homepage = $request->home_page;
            if($fileExtension === 'txt') {
                $user->file = $filePath;
            } else {
                $user->image = $filePath;
            }
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
