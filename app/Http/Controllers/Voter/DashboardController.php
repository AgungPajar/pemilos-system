<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use App\Models\Token;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the voter dashboard with paslon list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function __invoke(Request $request)
    {
        $token = $request->attributes->get('authToken');

        if (! $token instanceof Token) {
            $tokenId = $request->session()->get(Token::SESSION_KEY);
            $token = $tokenId ? Token::find($tokenId) : null;

            if (! $token || $token->isUsed()) {
                $request->session()->forget(Token::SESSION_KEY);

                return redirect()
                    ->route('voter.login')
                    ->withErrors(['code' => 'Sesi token tidak valid, silakan login kembali.']);
            }
        }

        $paslons = Paslon::orderBy('order_number')->get();

        return view('voter.dashboard', [
            'paslons' => $paslons,
            'token' => $token,
        ]);
    }
}
