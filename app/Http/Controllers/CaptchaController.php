<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mews\Captcha\Captcha;

class CaptchaController extends Controller
{
    public function refresh(Request $request, Captcha $captcha)
    {
        $captchaText = $captcha->create('default', true);
        $request->session()->put('captcha', $captchaText);

        return response()->json(['captcha' => $captchaText]);
    }
}
