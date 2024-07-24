<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class ViewController extends Controller
{
    public function getMainView(Request $request)
    {
        return to_route('trading');
    }
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
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'currency'=>$currency,
            'market'=>$market
        ]);
    }
    public function getOverviewView(Request $request)
    {
        return Inertia::render('Overview', [
            'user' => $request->user()
        ]);
    }
    public function getPolicyView(Request $request)
    {
        return Inertia::render('Policy', [
            'user' => $request->user()
        ]);
    }
    public function getTermsView(Request $request)
    {
        return Inertia::render('Terms', [
            'user' => $request->user()
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
    public function getContactsView(Request $request)
    {
        return Inertia::render('Contacts', [
            'user' => $request->user()
        ]);
    }
    public function get2FAView(Request $request)
    {
        if (session('2fa:user:id')) {
            return Inertia::render('2FA', [
                'user' => $request->user()
            ]);
        }
        return to_route('auth.login');
    }
    public function forget2FAView(Request $request)
    {
        return Inertia::render('2FAForget', [
            'user' => $request->user()
        ]);
    }
    //================ AUTH views========================
    public function getBalanceView(Request $request)
    {
        return Inertia::render('Balance', [
            'user' => $request->user()
        ]);
    }
    public function getTransactionsView(Request $request)
    {
        return Inertia::render('Transactions', [
            'user' => $request->user()
        ]);
    }
    public function getFiatTransactionsView(Request $request)
    {
        return Inertia::render('FiatTransactions', [
            'user' => $request->user()
        ]);
    }
    public function getTransfersView(Request $request)
    {
        return Inertia::render('Transfers', [
            'user' => $request->user()
        ]);
    }
    public function getOrdersView(Request $request)
    {
        return Inertia::render('Orders', [
            'user' => $request->user()
        ]);
    }
    public function getDealsView(Request $request)
    {
        return Inertia::render('Deals', [
            'user' => $request->user()
        ]);
    }
    public function getRefPaymentsView(Request $request)
    {
        return Inertia::render('RefPayments', [
            'user' => $request->user()
        ]);
    }
    public function getSupportView(Request $request)
    {
        return Inertia::render('Support', [
            'user' => $request->user()
        ]);
    }
    public function getProfileView(Request $request)
    {
        return Inertia::render('Profile', [
            'user' => $request->user()
        ]);
    }
    public function getNotificationsView(Request $request)
    {
        return Inertia::render('Notifications', [
            'user' => $request->user()
        ]);
    }

}
