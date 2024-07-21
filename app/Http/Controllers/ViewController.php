<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class ViewController extends Controller
{
    public function getTradingView(Request $request, $market = null, $currency = null)
    {
        if (!isset($market)) {
            return Inertia::render('404', [
                'user' => $request->user()
            ]);
        }
        if (!isset($currency)) {
            return Inertia::render('404', [
                'user' => $request->user()
            ]);
        }
        $validator = Validator::make(['market'=>$market,'currency'=>$currency], [
            'market' => 'required|alpha_num|min:2|max:10',
            'currency' => 'required|alpha_num|min:2|max:10',
        ]);
        if ($validator->fails()) {
            return Inertia::render('404', [
                'user' => $request->user()
            ]);
        }
        $find_pair = Arr::where(app('all-pairs'), function ($value, $key) use ($currency, $market){
            return $value['currency'] === mb_strtoupper($currency) && $value['market'] === mb_strtoupper($market);
        });
        if(!$find_pair)
        {
            return Inertia::render('404', [
                'user' => $request->user()
            ]);
        }
        return Inertia::render('Trading', [
            'user' => $request->user(),
            'currency'=>$currency,
            'market'=>$market
        ]);
    }
    public function getApiView(Request $request)
    {
        return Inertia::render('Api', [
            'user' => $request->user()
        ]);
    }
    public function getFeesView(Request $request)
    {
        return Inertia::render('Fees', [
            'user' => $request->user()
        ]);
    }
    public function getStatusView(Request $request)
    {
        return Inertia::render('Status', [
            'user' => $request->user()
        ]);
    }
}
