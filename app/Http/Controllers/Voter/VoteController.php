<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use App\Models\Token;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Store the selected vote for the authenticated token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $token = $request->attributes->get('authToken');

        if (! $token instanceof Token) {
            return redirect()
                ->route('voter.login')
                ->withErrors(['code' => 'Sesi token tidak valid, silakan login kembali.']);
        }

        if ($token->isUsed()) {
            $request->session()->forget(Token::SESSION_KEY);

            return redirect()
                ->route('voter.login')
                ->withErrors(['code' => 'Token ini sudah digunakan untuk memilih.']);
        }

        $data = $request->validate([
            'paslon_id' => ['required', 'exists:paslons,id'],
        ]);

        $paslon = Paslon::findOrFail($data['paslon_id']);

        $token->paslon_id = $paslon->id;
        $token->used_at = now();
        $token->used_ip = $request->ip();
        $token->used_user_agent = $request->userAgent();
        $token->save();

        $request->session()->forget([Token::SESSION_KEY, 'pkl_student_id']);
        $request->session()->put('voted_paslon_name', $paslon->display_name);

        return redirect()->route('voter.thanks');
    }
}
