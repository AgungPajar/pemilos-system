<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenLoginController extends Controller
{
    /**
     * Show the token login form for voters.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showLoginForm()
    {
        return view('voter.login');
    }

    /**
     * Handle token authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:12'],
        ]);

        $code = Str::upper(str_replace(' ', '', $data['code']));
        $token = Token::where('code', $code)->first();

        if (! $token) {
            return back()->withErrors(['code' => 'Token tidak ditemukan.'])->withInput();
        }

        if ($token->isUsed()) {
            return back()->withErrors(['code' => 'Token ini sudah digunakan.'])->withInput();
        }

        $request->session()->put(Token::SESSION_KEY, $token->id);
        $request->session()->forget('pkl_student_id');
        $request->session()->regenerate();

        return redirect()->route('voter.dashboard');
    }

    /**
     * Logout the current token session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->forget([Token::SESSION_KEY, 'pkl_student_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('voter.login');
    }
}
