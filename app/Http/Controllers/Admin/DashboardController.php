<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use App\Models\Token;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with election statistics.
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

        $totalPaslon = $paslonStats->count();
        $totalVoters = Token::whereNotNull('used_at')->count();
        $totalTokens = Token::count();
        $unusedTokens = $totalTokens - $totalVoters;
        $leadingPaslon = $paslonStats->sortByDesc('votes_count')->first();

        $chartData = $paslonStats->map(function (Paslon $paslon) {
            return [
                'label' => 'Paslon ' . $paslon->order_number,
                'value' => $paslon->votes_count,
                'name' => $paslon->display_name,
            ];
        });

        return view('admin.dashboard', [
            'paslonStats' => $paslonStats,
            'totalPaslon' => $totalPaslon,
            'totalVoters' => $totalVoters,
            'unusedTokens' => $unusedTokens,
            'leadingPaslon' => $leadingPaslon,
            'chartData' => $chartData,
        ]);
    }
}
