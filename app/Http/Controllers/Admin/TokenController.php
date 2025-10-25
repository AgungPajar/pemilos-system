<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    /**
     * Display a listing of tokens and generation controls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $tokensQuery = Token::with('paslon')->latest();

        if ($status === 'used') {
            $tokensQuery->whereNotNull('used_at');
        } elseif ($status === 'unused') {
            $tokensQuery->whereNull('used_at');
        }

        $tokens = $tokensQuery->paginate(20)->withQueryString();

        return view('admin.tokens.index', [
            'tokens' => $tokens,
            'status' => $status,
        ]);
    }

    /**
     * Store newly generated tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:200'],
            'note' => ['nullable', 'string', 'max:50'],
        ]);

        $createdTokens = [];

        for ($i = 0; $i < $data['amount']; $i++) {
            $createdTokens[] = Token::create([
                'code' => $this->generateUniqueCode(),
                'note' => $data['note'],
            ]);
        }

        $codes = collect($createdTokens)->pluck('code')->implode(', ');

        return redirect()
            ->route('admin.tokens.index')
            ->with('success', "{$data['amount']} token berhasil dibuat.")
            ->with('new_tokens', $codes);
    }

    /**
     * Remove the specified token from storage.
     *
     * @param  \App\Models\Token  $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Token $token)
    {
        if ($token->isUsed()) {
            return redirect()
                ->route('admin.tokens.index')
                ->withErrors(['general' => 'Token tidak dapat dihapus karena sudah digunakan.']);
        }

        if ($token->pklStudent) {
            return redirect()
                ->route('admin.tokens.index')
                ->withErrors(['general' => 'Token tidak dapat dihapus karena terhubung dengan siswa PKL.']);
        }

        $token->delete();

        return redirect()->route('admin.tokens.index')->with('success', 'Token berhasil dihapus.');
    }

    /**
     * Display printable token list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function print(Request $request)
    {
        $status = $request->query('status');

        $tokensQuery = Token::with('paslon')->orderBy('code');

        if ($status === 'used') {
            $tokensQuery->whereNotNull('used_at');
        } elseif ($status === 'unused') {
            $tokensQuery->whereNull('used_at');
        }

        return view('admin.tokens.print', [
            'tokens' => $tokensQuery->get(),
            'status' => $status,
        ]);
    }

    /**
     * Generate a unique alphanumeric token code.
     *
     * @return string
     */
    protected function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Token::where('code', $code)->exists());

        return $code;
    }
}
