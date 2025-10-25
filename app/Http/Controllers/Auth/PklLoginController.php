<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PklStudent;
use App\Models\Token;
use Illuminate\Http\Request;

class PklLoginController extends Controller
{
    /**
     * Display the PKL login form.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showLoginForm()
    {
        return view('pkl.login');
    }

    /**
     * Handle an incoming PKL login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nis' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
        ]);

        $nis = trim($credentials['nis']);
        $birthDate = $credentials['birth_date'];

        $student = PklStudent::with('token')
            ->where('nis', $nis)
            ->whereDate('tgl_lahir', $birthDate)
            ->first();

        if (! $student) {
            return back()->withErrors([
                'nis' => 'Data tidak ditemukan. Pastikan NIS dan tanggal lahir sesuai.',
            ])->withInput();
        }

        $token = $student->token;

        if (! $token) {
            return back()->withErrors([
                'nis' => 'Token belum diterbitkan untuk siswa PKL ini. Hubungi panitia.',
            ])->withInput();
        }

        if ($token->isUsed()) {
            return back()->withErrors([
                'nis' => 'Token sudah digunakan untuk memilih. Hubungi panitia bila ini kesalahan.',
            ])->withInput();
        }

        $request->session()->put(Token::SESSION_KEY, $token->id);
        $request->session()->put('pkl_student_id', $student->id);
        $request->session()->regenerate();

        return redirect()->intended(route('voter.dashboard'));
    }
}
