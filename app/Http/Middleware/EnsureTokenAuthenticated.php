<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Illuminate\Http\Request;

class EnsureTokenAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $isPklSession = $request->session()->has('pkl_student_id');
        $tokenId = $request->session()->get(Token::SESSION_KEY);

        if (! $tokenId) {
            $request->session()->forget('pkl_student_id');
            return redirect()
                ->route($isPklSession ? 'pkl.login' : 'voter.login')
                ->withErrors(['code' => 'Silakan masuk menggunakan token terlebih dahulu.']);
        }

        $token = Token::find($tokenId);

        if (! $token || $token->isUsed()) {
            $request->session()->forget([Token::SESSION_KEY, 'pkl_student_id']);

            return redirect()
                ->route($isPklSession ? 'pkl.login' : 'voter.login')
                ->withErrors(['code' => 'Token tidak tersedia atau sudah digunakan.']);
        }

        // Share token instance for downstream consumers.
        $request->attributes->set('authToken', $token);

        return $next($request);
    }
}
