<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use Illuminate\Http\Request;

class PaslonDetailController extends Controller
{
    public function show(Request $request, Paslon $paslon)
    {
        $token = $request->session()->get('voter_token');

        return view('voter.paslon-detail', compact('paslon', 'token'));
    }
}
