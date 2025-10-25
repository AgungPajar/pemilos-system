<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use App\Models\Token;

class VoteSummaryController extends Controller
{
    /**
     * Display vote statistics per paslon.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function __invoke()
    {
        $paslonStats = Paslon::withCount([
            'tokens as votes_count' => function ($query) {
                $query->whereNotNull('used_at');
            },
        ])->orderBy('order_number')->get();

        $totalTokens = Token::count();
        $totalVoted = Token::whereNotNull('used_at')->count();
        $unusedTokens = $totalTokens - $totalVoted;

        return view('admin.tokens.summary', [
            'paslonStats' => $paslonStats,
            'totalTokens' => $totalTokens,
            'totalVoted' => $totalVoted,
            'unusedTokens' => $unusedTokens,
        ]);
    }
}
