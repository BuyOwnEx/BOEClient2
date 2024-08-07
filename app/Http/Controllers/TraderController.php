<?php

namespace App\Http\Controllers;

use App\Http\Requests\Confirm2FARequest;
use App\Http\Requests\ValidateSecretRequest;
use App\Library\APIToken;
use App\Library\BuyOwnExClientAPI;
use App\Library\SumSubAPI;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;

class TraderController extends Controller
{
    public function getCaptchaConfig(Request $request)
    {
        if(mb_strtoupper($request->captcha_type) === 'GEETEST')
        {
            return [
                'captcha_id' => config('captcha.id'),
                'product' => config('captcha.product'),
                'language' => config('captcha.lang')
            ];
        }
        elseif (mb_strtoupper($request->captcha_type) === 'CLOUDFLARE')
        {
            return [
                'captcha_id' => config('captcha.id'),
                'language' => config('captcha.lang')
            ];
        }
        else return false;
    }
    public function setLocale(Request $request)
    {
        if (in_array($request->lang, config('app.locales'))) {   # Проверяем, что у пользователя выбран доступный язык
            App::setLocale($request->lang);
            session(['locale' =>$request->lang]);
            if($request->user())
            {
                $request->user()->language = $request->lang;
                $request->user()->save();
            }
            return back()->with('flash', [
                'success' => true,
                'message'=>'Language is changed to '.$request->lang
            ]);
        }
        else {
            return back()->with('flash', [
                'success' => false,
                'message'=>'Language is not supported'
            ]);
        }
    }
    public function postValidateToken(ValidateSecretRequest $request)
    {
        //get user id and create cache key
        $userId = $request->session()->pull('2fa:user:id');
        $key = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);

        //login and redirect user
        Auth::loginUsingId($userId);
        return response()->json([
            'auth' => auth()->check(),
            'intended' => $this->redirectPath(),
        ]);
    }

    public function generateTwoFactor(Request $request)
    {
        if(Auth::user())
        {
            $request->session()->put('2fa:user:id', Auth::id());
            if(!Auth::user()->g2fa)
            {
                $secret = $this->generateSecret();
                Auth::user()->g2fa_secret = Crypt::encrypt($secret);
                Auth::user()->save();

                $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

                $imageDataUri = $google2fa->getQRCodeInline(
                    $request->getHttpHost(),
                    Auth::user()->email,
                    $secret,
                    200
                );
                return ['success' => true, 'secret' => $secret, 'image' => $imageDataUri];
            }
            else {
                return ['success' => true];
            }
        }
        else {
            return ['success' => false, 'message' => __('app.auth.un_authed')];
        }
    }
    public function enableTwoFactorReady(ValidateSecretRequest $request)
    {
        $user = $request->user();

        $userId = $request->session()->pull('2fa:user:id');
        $key    = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);

        //encrypt and then save secret
        $user->g2fa = true;
        $user->save();
        return ['success'=> true];
    }
    public function disableTwoFactorReady(ValidateSecretRequest $request)
    {
        $userId = $request->session()->pull('2fa:user:id');
        $key    = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);
        $user = User::findOrFail($userId);
        $user->g2fa = false;
        $user->save();
        return ['success'=> true];
    }


    public function getTickers()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->tickers();
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getMarketData()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->market_data();
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getDepth(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->depth($request->currency, $request->market);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function HistoryDealList(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->history_deals($request->currency, $request->market);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getChart(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->candlesticks($request->currency, $request->market, $request->range);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getCryptoCurrencies()
    {
        try {
            return Cache::remember('crypto_currencies', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->crypto_currencies();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getFiatCurrencies()
    {
        try {
            return Cache::remember('fiat_currencies', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->fiat_currencies();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllCurrencies()
    {
        try {
            return Cache::remember('all_currencies', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->all_currencies();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getPairs()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_pairs()->getOriginalContent();
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllFiatPlatforms()
    {
        try {
            return Cache::remember('all_fiat_platforms', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->all_fiat_platforms();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllFiatFees()
    {
        try {
            return Cache::remember('all_fiat_fees', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->all_fiat_fees();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllBanks()
    {
        try {
            return Cache::remember('all_banks', 60, function (){
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->all_banks();
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getHealth()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->health();
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getBalances()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->balances(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getOrders()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->orders(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getDeals()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->deals(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getOwnFees()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->own_fees(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getPositions()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->positions(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function makeOrder(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->makeOrder(
                Auth::id(),
                $request->type,
                $request->currency,
                $request->market,
                $request->side,
                $request->amount,
                isset($request->rate) ? $request->rate : null,
                isset($request->base) ? $request->base : null,
                isset($request->sl_rate) ? $request->sl_rate : null,
                isset($request->tp_rate) ? $request->tp_rate : null,
                isset($request->ts_offset) ? $request->ts_offset : null,
                isset($request->margin) ? $request->margin : 0,
                isset($request->offer) ? $request->offer : 0
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function cancelOrder(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->cancelOrder(
                Auth::id(),
                $request->order
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function cancelAllOrders(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->cancelAllOrders(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function cancelAllSLOrders(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->cancelAllSLOrders(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function cancelAllTPOrders(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->cancelAllTPOrders(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function cancelAllTSOrders(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->cancelAllTSOrders(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getOfferList(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getOffers(
                $request->currency,
                isset($request->market) ? $request->market : null,
                $request->amount,
                $request->side,
                $request->type
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function closePosition(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->closePosition(
                Auth::id(),
                $request->position
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function closeAllPositions(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->closeAllPositions(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function closeAllLongPositions(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->closeAllLongPositions(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function closeAllShortPositions(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->closeAllShortPositions(
                Auth::id(),
                $request->currency,
                $request->market,
                $request->all_pairs
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function depositPosition(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->depositPosition(
                Auth::id(),
                $request->position,
                $request->currency,
                $request->market,
                $request->amount
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getWithdrawalList(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->activeWithdrawals(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getFiatWithdrawalList(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->activeFiatWithdrawals(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function transferToTradeWallet(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->transferToTradeWallet(
                Auth::id(),
                $request->currency,
                $request->amount
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function transferToSafeWallet(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->transferToSafeWallet(
                Auth::id(),
                $request->currency,
                $request->amount
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function emailChangeRequest(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->emailChangeRequest(
                Auth::id(),
                $request->email
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function emailChangeConfirm(Confirm2FARequest $request)
    {
        try {
            //use cache to store token to blacklist
            Cache::add(Auth::id().':'.$request->totp, true, 4);
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->emailChangeConfirm(
                Auth::id(),
                $request->code_old_email,
                $request->code_new_email
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getBlockStatus()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getBlockStatus(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getVerifyStatus()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getVerifyStatus(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getStatus()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getStatus(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getNotificationStatus()
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getNotificationStatus(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function setNotificationStatus(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->setNotificationStatus(
                Auth::id(),
                $request->status
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function setNotificationRead(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->setNotificationRead(
                Auth::id(),
                $request->notification_id
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function deleteNotification(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->deleteNotification(
                Auth::id(),
                $request->notification_id
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function setNotificationsReadAll(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->setNotificationsReadAll(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function deleteAllNotifications(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->deleteAllNotifications(
                Auth::id()
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }

    public function withdrawCryptoRequest(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->withdrawCryptoRequest(
                Auth::id(),
                $request->currency,
                $request->amount,
                $request->address,
                $request->platform_id,
                $request->reason_id ?? null
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function withdrawFiatRequest(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->notifyFiatInvoiceWithdraw(
                Auth::id(),
                $request->currency,
                $request->amount,
                $request->inn,
                $request->bic,
                $request->acc,
                $request->gateway_id
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function withdrawCryptoConfirm(Confirm2FARequest $request)
    {
        try {
            //use cache to store token to blacklist
            Cache::add(Auth::id().':'.$request->totp, true, 4);
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->withdrawCryptoConfirm(
                Auth::id(),
                $request->code
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function withdrawFiatConfirm(Confirm2FARequest $request)
    {
        try {
            //use cache to store token to blacklist
            Cache::add(Auth::id().':'.$request->totp, true, 4);
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->withdrawFiatConfirm(
                Auth::id(),
                $request->code
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function withdrawCryptoCancel(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->withdrawCryptoCancel(
                Auth::id(),
                $request->id
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function withdrawFiatCancel(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->withdrawFiatCancel(
                Auth::id(),
                $request->id
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function sendMessage(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->sendMessage(
                Auth::id(),
                $request->message
            );
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllOrders(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_orders(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllDeals(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_deals(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllTransactions(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_transactions(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllFiatTransactions(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_fiat_transactions(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllTransfers(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_transfers(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllRefPayments(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_ref_payments(Auth::id(), $request->page, $request->itemsPerPage, $request->sortBy, $request->sortDesc, $request->mustSort, $request->multiSort, $request->filters);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllFollowers(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_followers(Auth::id(), $request->name);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllNotifications(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_notifications(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAllReferralTypes(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->all_referral_types(Auth::id());
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function setReferralType(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->set_referral_type(Auth::id(),$request->id);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAddress(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->get_address(Auth::id(),$request->currency, $request->platform_id);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function validateAddress(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->validate_address(Auth::id(), $request->currency, $request->address, $request->platform_id);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getSumSubToken(Request $request)
    {
        try {
            $api = new SumSubAPI(config('kyc.api-public-key'), config('kyc.api-secret-key'));
            return $api->getAccessToken($request->user()->name);
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function newAPIToken(Request $request)
    {
        try {
            Sanctum::usePersonalAccessTokenModel('App\PersonalAccessToken');
            $token = $request->user()->createToken($request->name, $request->abilities);
            return ['success'=>true, 'data'=>$token];
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function editAPIToken(Request $request)
    {
        try {
            $token = PersonalAccessToken::findOrFail($request->id);
            $token->abilities = $request->abilities;
            $token->save();
            return ['success'=>true, 'data'=>$token];
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getAPITokens(Request $request)
    {
        try {
            Sanctum::usePersonalAccessTokenModel('App\PersonalAccessToken');
            $tokens = $request->user()->tokens;
            return ['success'=>true, 'abilities'=>APIToken::$abilities, 'data'=>$tokens];
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function deleteAPIToken(Request $request)
    {
        try {
            Sanctum::usePersonalAccessTokenModel('App\PersonalAccessToken');
            $request->user()->tokens()->where('id', $request->id)->delete();
            return ['success'=>true];
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function deleteAllAPITokens(Request $request)
    {
        try {
            Sanctum::usePersonalAccessTokenModel('App\PersonalAccessToken');
            $request->user()->tokens()->delete();
            return ['success'=>true];
        } catch (\Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }

    public function sendKYCKonturIndRequest(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'fio' => 'required|string|min:1|max:256',
            'birthday' => 'required|date_format:"Y-m-d"',
            'passport_number' => 'required|string|min:10|max:11',
            'ind_inn' => 'nullable|string|size:12',
            'file_ps' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ws' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ts' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
        ], [], [
            'fio' => __('kyc.kontur.validation.fio'),
            'birthday' => __('kyc.kontur.validation.birthday'),
            'passport_number' => __('kyc.kontur.validation.passport_number'),
            'ind_inn' => __('kyc.kontur.validation.ind_inn'),
            'file_ps' => __('kyc.file_ps'),
            'file_ws' => __('kyc.file_ws'),
            'file_ts' => __('kyc.file_ts'),
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->getMessages(),
            ],422);
        }
        else
        {
            try
            {
                $path_ps = Storage::putFile('verifications/'.config('app.client_id').'/'.Auth::id(), $request->file('file_ps'));
                $path_ws = Storage::putFile('verifications/'.config('app.client_id').'/'.Auth::id(), $request->file('file_ws'));
                $path_ts = Storage::putFile('verifications/'.config('app.client_id').'/'.Auth::id(), $request->file('file_ts'));
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->kycKonturIndRequest(
                    Auth::id(),
                    $request->fio,
                    $request->birthday,
                    $request->passport_number,
                    $request->ind_inn,
                    $path_ps,
                    $path_ws,
                    $path_ts
                );
            }
            catch (Exception $e)
            {
                return ['success'=>false, 'message'=>$e->getMessage()];
            }

        }
    }
    public function sendKYCKonturCompRequest(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'comp_inn' => 'nullable|string|size:10',
            'edo_id' => 'required|string|min:35|max:50',
            'file_doc' => 'required|file|mimes:pdf|extensions:pdf|max:2048',
        ], [], [
            'comp_inn' => __('kyc.kontur.validation.comp_inn'),
            'edo_id' => __('kyc.kontur.validation.edo_id'),
            'file_doc' => __('kyc.file_doc'),
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->getMessages(),
            ],422);
        }
        else
        {
            try
            {
                $path_doc = Storage::putFile('verifications/'.config('app.client_id').'/'.Auth::id(), $request->file('file_doc'));
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->kycKonturCompRequest(
                    Auth::id(),
                    $request->comp_inn,
                    $request->edo_id,
                    $path_doc
                );
            }
            catch (Exception $e)
            {
                return ['success'=>false, 'message'=>$e->getMessage()];
            }

        }
    }
    public function sendKYCRequest(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:64',
            'second_name' => 'required|string|min:1|max:64',
            'surname' => 'required|string|min:1|max:64',
            'sex' => 'required|string|in:male,female',
            'birthday' => 'required|date_format:"Y-m-d"',
            'birthday_place' => 'required|string|min:1|max:128',
            'passport_no' => 'required|string|min:5|max:20',
            'passport_place' => 'required|string|min:5|max:255',
            'passport_date' => 'required|date_format:"Y-m-d"',
            'address' => 'required|string|min:10|max:512',
            'file_ps' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ws' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ts' => 'required|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
        ], [], [
            'first_name' => __('kyc.first_name'),
            'second_name' => __('kyc.second_name'),
            'surname' => __('kyc.surname'),
            'sex' => __('kyc.sex'),
            'birthday' => __('kyc.birthday'),
            'birthday_place' => __('kyc.birthday_place'),
            'passport_no' => __('kyc.passport_no'),
            'passport_place' => __('kyc.passport_place'),
            'passport_date' => __('kyc.passport_date'),
            'address' => __('kyc.address'),
            'file_ps' => __('kyc.file_ps'),
            'file_ws' => __('kyc.file_ws'),
            'file_ts' => __('kyc.file_ts'),
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->getMessages(),
            ],422);
        }
        else
        {
            try{
                $path_ps = Storage::putFile('verifications/'.config('app.api-secret-key').'/'.Auth::id(), $request->file('file_ps'));
                $path_ws = Storage::putFile('verifications/'.config('app.api-secret-key').'/'.Auth::id(), $request->file('file_ws'));
                $path_ts = Storage::putFile('verifications/'.config('app.api-secret-key').'/'.Auth::id(), $request->file('file_ts'));
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                return $api->verificationRequest(
                    Auth::id(),
                    $request->first_name,
                    $request->second_name,
                    $request->surname,
                    $request->sex,
                    $request->birthday,
                    $request->birthday_place,
                    $request->passport_no,
                    $request->passport_place,
                    $request->passport_date,
                    $request->address,
                    env('APP_URL').'/'.$path_ps,
                    env('APP_URL').'/'.$path_ws,
                    env('APP_URL').'/'.$path_ts,
                );
            }
            catch (Exception $e)
            {
                return ['success'=>false, 'message'=>$e->getMessage()];
            }

        }
    }
    public function sendKYCFix(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:64',
            'second_name' => 'required|string|min:1|max:64',
            'surname' => 'required|string|min:1|max:64',
            'sex' => 'required|string|in:male,female',
            'birthday' => 'required|date_format:"Y-m-d"',
            'birthday_place' => 'required|string|min:1|max:128',
            'passport_no' => 'required|string|min:5|max:20',
            'passport_place' => 'required|string|min:5|max:255',
            'passport_date' => 'required|date_format:"Y-m-d"',
            'address' => 'required|string|min:10|max:512',
            'file_ps' => 'nullable|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ws' => 'nullable|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
            'file_ts' => 'nullable|file|image|mimes:jpeg,png|max:2048|dimensions:min_width=500,min_height=500,max_width=4160,max_height=4160',
        ], [], [
            'first_name' => __('kyc.first_name'),
            'second_name' => __('kyc.second_name'),
            'surname' => __('kyc.surname'),
            'sex' => __('kyc.sex'),
            'birthday' => __('kyc.birthday'),
            'birthday_place' => __('kyc.birthday_place'),
            'passport_no' => __('kyc.passport_no'),
            'passport_place' => __('kyc.passport_place'),
            'passport_date' => __('kyc.passport_date'),
            'address' => __('kyc.address'),
            'file_ps' => __('kyc.file_ps'),
            'file_ws' => __('kyc.file_ws'),
            'file_ts' => __('kyc.file_ts'),
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->getMessages(),
            ],422);
        }
        else
        {
            try{
                $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
                $kyc = $api->getVerification(
                    Auth::id()
                );
                if (isset($request->file_ps))
                {
                    $path_ps = Storage::putFile('verifications/'.Auth::id(), $request->file('file_ps'));
                    $path_ps = env('APP_URL').'/'.$path_ps;
                }
                else
                    $path_ps = $kyc->getData()->data->file_ps;
                if (isset($request->file_ws))
                {
                    $path_ws = Storage::putFile('verifications/'.Auth::id(), $request->file('file_ws'));
                    $path_ws = env('APP_URL').'/'.$path_ws;
                }
                else
                    $path_ws = $kyc->getData()->data->file_ws;
                if (isset($request->file_ts))
                {
                    $path_ts = Storage::putFile('verifications/'.Auth::id(), $request->file('file_ts'));
                    $path_ts = env('APP_URL').'/'.$path_ts;
                }
                else
                    $path_ts = $kyc->getData()->data->file_ts;

                return $api->verificationRequest(
                    Auth::id(),
                    $request->first_name,
                    $request->second_name,
                    $request->surname,
                    $request->sex,
                    $request->birthday,
                    $request->birthday_place,
                    $request->passport_no,
                    $request->passport_place,
                    $request->passport_date,
                    $request->address,
                    $path_ps,
                    $path_ws,
                    $path_ts,
                );
            }
            catch (Exception $e)
            {
                return ['success'=>false, 'message'=>$e->getMessage()];
            }
        }
    }
    public function setKYCPayment(Request $request) {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->verificationPayment(
                Auth::id()
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function NotifyFiatQRReplenish(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->notifyFiatQRReplenish(
                Auth::id(),
                $request->currency,
                $request->bank_id,
                $request->amount,
                $request->gateway_id
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function NotifyFiatInvoiceReplenish(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->notifyFiatInvoiceReplenish(
                Auth::id(),
                $request->currency,
                $request->amount,
                $request->bank_id,
                $request->inn,
                $request->bic,
                $request->acc,
                $request->gateway_id
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getKYCKonturData(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getKYCKonturData(
                Auth::id()
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getKYCRequest(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getVerification(
                Auth::id()
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getKYCImage(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            $kyc = $api->getVerification(
                Auth::id()
            );
            switch ($request->type){
                case 'ps':
                    $kyc_file = $kyc->getData()->data->file_ps;
                    if ($kyc_file === $request->path)
                        $file=Storage::get(explode(env('APP_URL'),$kyc_file)[1]);
                    break;
                case 'ts':
                    $kyc_file = $kyc->getData()->data->file_ts;
                    if ($kyc_file === $request->path)
                        $file=Storage::get(explode(env('APP_URL'),$kyc_file)[1]);
                    break;
                case 'ws':
                    $kyc_file = $kyc->getData()->data->file_ws;
                    if ($kyc_file === $request->path)
                        $file=Storage::get(explode(env('APP_URL'),$kyc_file)[1]);
                    break;
                default:
                    return response()->file(public_path().'/images/file-not-found.png');
                    break;
            }
            return response($file);
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getVerificationBankDetails(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getVerificationBankDetails(
                Auth::id()
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
    public function getReplenishBankDetails(Request $request)
    {
        try {
            $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
            return $api->getReplenishBankDetails(
                Auth::id()
            );
        }
        catch (Exception $e)
        {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }
}
