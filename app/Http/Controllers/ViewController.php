<?php

namespace App\Http\Controllers;



use App\Library\BuyOwnExClientAPI;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class ViewController extends Controller
{
    public function getMainView(Request $request)
    {
        return to_route('trading',['currency' => config('app.default-currency'), 'market' => config('app.default-market')]);
    }
    public function getTradingView(Request $request, $currency = null, $market = null)
    {
        if ($request->user() && ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route('verification.notice'));
        }
        if (!isset($market)) {
            return Inertia::render('404');
        }
        if (!isset($currency)) {
            return Inertia::render('404');
        }
        $validator = Validator::make(['market'=>$market,'currency'=>$currency], [
            'market' => 'required|alpha_num|min:2|max:10',
            'currency' => 'required|alpha_num|min:2|max:10',
        ]);
        if ($validator->fails()) {
            return Inertia::render('404');
        }
        $find_pair = Arr::where(app('all-pairs'), function ($value) use ($currency, $market){
            return $value['currency'] === mb_strtoupper($currency) && $value['market'] === mb_strtoupper($market);
        });
        if(!$find_pair)
        {
            return Inertia::render('404');
        }
        try
        {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            $tickers = $api->tickers();
            $graph = $api->candlesticks($currency, $market);
            $history_deals = $api->history_deals($currency, $market);
            $depth = $api->depth($currency, $market);
            Log::info($depth);
            return Inertia::render('Trading', [
                'currency'=>$currency,
                'market'=>$market,
                'pair'=>$find_pair,
                'tickers'=>$tickers->getData()->data,
                'history_deals'=>$history_deals->getData()->data,
                'graph'=>$graph->getData()->data,
                'depth'=>$depth->getData(),
                'all_currencies'=>app('all-currencies')['data']
            ]);
        } catch (\Exception $e) {
            return Inertia::render('500', ['message' => $e->getMessage()]);
        }
    }
    public function getOverviewView(Request $request)
    {
        return Inertia::render('Overview');
    }
    public function getPolicyView(Request $request)
    {
        return Inertia::render('Policy');
    }
    public function getTermsView(Request $request)
    {
        return Inertia::render('Terms');
    }
    public function getApiView(Request $request)
    {
        return Inertia::render('Api');
    }
    public function getFeesView(Request $request)
    {
        return Inertia::render('Fees');
    }
    public function getStatusView(Request $request)
    {
        return Inertia::render('Status');
    }
    public function getContactsView(Request $request)
    {
        return Inertia::render('Contacts');
    }
    public function get2FAView(Request $request)
    {
        if (session('2fa:user:id')) {
            return Inertia::render('2FA');
        }
        return to_route('login');
    }
    public function forget2FAView(Request $request)
    {
        return Inertia::render('2FAForget');
    }
    //================ AUTH views========================
    public function getBalanceView(Request $request)
    {
        return Inertia::render('Balance');
    }
    public function getTransactionsView(Request $request)
    {
        return Inertia::render('Transactions');
    }
    public function getFiatTransactionsView(Request $request)
    {
        return Inertia::render('FiatTransactions');
    }
    public function getTransfersView(Request $request)
    {
        return Inertia::render('Transfers');
    }
    public function getOrdersView(Request $request)
    {
        return Inertia::render('Orders');
    }
    public function getDealsView(Request $request)
    {
        return Inertia::render('Deals');
    }
    public function getRefPaymentsView(Request $request)
    {
        return Inertia::render('RefPayments');
    }
    public function getSupportView(Request $request)
    {
        return Inertia::render('Support');
    }
    public function getProfileView(Request $request)
    {
        return Inertia::render('Profile');
    }
    public function getNotificationsView(Request $request)
    {
        return Inertia::render('Notifications');
    }

}
