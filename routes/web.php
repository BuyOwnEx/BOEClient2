<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('main');*/

Route::get('/', 'ViewController@getMainView')->name('main');
Route::get('trading/{market}/{currency}', 'ViewController@getTradingView')->name('trading');
Route::get('overview', 'ViewController@getOverviewView')->name('overview');
Route::get('policy', 'ViewController@getPolicyView')->name('policy');
Route::get('terms', 'ViewController@getTermsView')->name('terms');
Route::get('api', 'ViewController@getApiView')->name('api');
Route::get('fees', 'ViewController@getFeesView')->name('fees');
Route::get('status', 'ViewController@getStatusView')->name('status');
Route::get('contacts', 'ViewController@getContactsView')->name('contacts');
Route::get('2fa', 'ViewController@get2FAView')->name('2fa');
Route::get('forget_2fa','ViewController@forget2FAView')->name('forget_2fa');

Route::get('captcha', 'TraderController@getCaptchaConfig');
Route::post('set_locale', 'TraderController@setLocale');
Route::post('2fa_validate', ['middleware' => 'throttle:5', 'uses' => 'TraderController@postValidateToken']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Trading');
    })->name('trading');
    Route::get('balance', 'ViewController@getBalanceView')->name('balance');
    Route::get('transactions', 'ViewController@getTransactionsView')->name('transactions');
    Route::get('fiat_transactions', 'ViewController@getFiatTransactionsView')->name('fiat_transactions');
    Route::get('transfers', 'ViewController@getTransfersView')->name('transfers');
    Route::get('orders', 'ViewController@getOrdersView')->name('orders');
    Route::get('deals', 'ViewController@getDealsView')->name('deals');
    Route::get('ref_payments', 'ViewController@getRefPaymentsView')->name('referrals');
    Route::get('support', 'ViewController@getSupportView')->name('support');
    Route::get('profile', 'ViewController@getProfileView')->name('profile');
    Route::get('notifications', 'ViewController@getNotificationsView')->name('notifications');

    Route::group(['prefix' => 'trader'], function () {
        Route::get('2fa_generate', 'TraderController@generateTwoFactor');

        Route::get('tickets', 'TicketController@getAllTickets');
        Route::get('ticket/comments', 'TicketController@getAllTicketComments');
        Route::get('ticket/priorities', 'TicketController@getPriorities');
        Route::get('ticket/statuses', 'TicketController@getStatuses');
        Route::get('ticket/tags', 'TicketController@getTags');
        Route::post('ticket/create', 'TicketController@createTicket')->middleware('check_block_status');
        Route::post('ticket/comment/add', 'TicketController@addComment')->middleware('check_block_status');
        Route::post('ticket/close', 'TicketController@closeTicket')->middleware('check_block_status');

        Route::post('2fa_enable', ['middleware' => ['throttle:5','check_block_status'], 'uses' => 'TraderController@enableTwoFactorReady']);
        Route::post('2fa_disable', ['middleware' => ['throttle:5','check_block_status'], 'uses' => 'TraderController@disableTwoFactorReady']);

        Route::group(['prefix' => 'ext'], function () {
            Route::get('tickers', 'TraderController@getTickers')->name('tickers');
            Route::get('market_data', 'TraderController@getMarketData')->name('market_data');
            Route::get('depth', 'TraderController@getDepth')->name('depth');
            Route::get('history/deals', 'TraderController@HistoryDealList')->name('history_deal_list');
            Route::get('graph', 'TraderController@getChart')->name('chart');
            Route::get('crypto_currencies', 'TraderController@getCryptoCurrencies')->name('crypto_currencies');
            Route::get('fiat_currencies', 'TraderController@getFiatCurrencies')->name('fiat_currencies');
            Route::get('all_currencies', 'TraderController@getAllCurrencies')->name('all_currencies');
            Route::get('all_fiat_platforms', 'TraderController@getAllFiatPlatforms')->name('all_fiat_platforms');
            Route::get('all_fiat_fees', 'TraderController@getAllFiatFees')->name('all_fiat_fees');
            Route::get('all_banks', 'TraderController@getAllBanks')->name('all_banks');
            Route::get('health', 'TraderController@getHealth')->name('health');
            Route::get('get_offer_list', 'TraderController@getOfferList')->name('offer_list');

            Route::get('token', 'JWTController@getOwnToken')->name('token');
            Route::post('private', 'JWTController@getPrivateToken')->name('private');
            Route::post('refresh', 'JWTController@refreshOwnToken')->name('refresh');

            Route::get('balances', 'TraderController@getBalances')->name('balances');
            Route::get('orders', 'TraderController@getOrders')->name('orders');
            Route::get('deals', 'TraderController@getDeals')->name('deals');
            Route::get('fees', 'TraderController@getOwnFees')->name('own_fees');
            Route::get('positions', 'TraderController@getPositions')->name('positions');

            Route::get('balance/all-withdrawals', 'TraderController@getWithdrawalList')->name('withdrawal_list');
            Route::get('balance/all-fiat-withdrawals', 'TraderController@getFiatWithdrawalList')->name('fiat_withdrawal_list');
            Route::get('block/status', 'TraderController@getBlockStatus')->name('get_block_status');
            Route::get('verify/status', 'TraderController@getVerifyStatus')->name('get_verify_status');
            Route::get('status', 'TraderController@getStatus')->name('get_status');
            Route::get('notification/status', 'TraderController@getNotificationStatus')->name('get_notification_status');
            Route::get('all_orders', 'TraderController@getAllOrders')->name('all_orders');
            Route::get('all_deals', 'TraderController@getAllDeals')->name('all_deals');
            Route::get('all_transactions', 'TraderController@getAllTransactions')->name('all_transactions');
            Route::get('all_fiat_transactions', 'TraderController@getAllFiatTransactions')->name('all_fiat_transactions');
            Route::get('all_transfers', 'TraderController@getAllTransfers')->name('all_transfers');
            Route::get('all_ref_payments', 'TraderController@getAllRefPayments')->name('all_ref_payments');
            Route::get('all_followers', 'TraderController@getAllFollowers')->name('all_followers');
            Route::get('all_notifications', 'TraderController@getAllNotifications')->name('all_notifications');

            Route::get('all_referral_types', 'TraderController@getAllReferralTypes')->name('all_referral_types');
            Route::get('get_address', 'TraderController@getAddress')->name('get_address');
            Route::get('sumsub_token', 'TraderController@getSumSubToken')->name('sumsub_token');
            Route::get('api_tokens', 'TraderController@getAPITokens')->name('api_tokens');
            Route::get('kyc_request', 'TraderController@getKYCRequest')->name('kyc_request');
            Route::get('kyc_kontur_data', 'TraderController@getKYCKonturData')->name('kyc_kontur_data');
            Route::get('verification_bank_details', 'TraderController@getVerificationBankDetails')->name('verification_bank_details');
            Route::get('replenish_bank_details', 'TraderController@getReplenishBankDetails')->name('replenish_bank_details');

            Route::get('get_image', 'TraderController@getKYCImage')->name('kyc_image');

            Route::middleware('check_block_status')->group(function () {
                Route::post('order/limit', 'TraderController@makeOrder')->name('limit_order');
                Route::post('order/market', 'TraderController@makeOrder')->name('market_order');
                Route::post('order/cancel', 'TraderController@cancelOrder')->name('cancel_order');
                Route::post('order/cancel_all', 'TraderController@cancelAllOrders')->name('cancel_all_orders');
                Route::post('order/cancel_all_sl', 'TraderController@cancelAllSLOrders')->name('cancel_all_sl_orders');
                Route::post('order/cancel_all_tp', 'TraderController@cancelAllTPOrders')->name('cancel_all_tp_orders');
                Route::post('order/cancel_all_ts', 'TraderController@cancelAllTSOrders')->name('cancel_all_ts_orders');

                Route::post('position/close', 'TraderController@closePosition')->name('close_position');
                Route::post('position/close_all', 'TraderController@closeAllPositions')->name('close_all_positions');
                Route::post('position/close_all_long', 'TraderController@closeAllLongPositions')->name('close_all_long_positions');
                Route::post('position/close_all_short', 'TraderController@closeAllShortPositions')->name('close_all_short_positions');
                Route::post('position/deposit', 'TraderController@depositPosition')->name('deposit_position');

                Route::post('transfer/trade', 'TraderController@transferToTradeWallet')->name('transfer_to_trade');
                Route::post('transfer/safe', 'TraderController@transferToSafeWallet')->name('transfer_to_safe');

                Route::post('withdraw/crypto/request', 'TraderController@withdrawCryptoRequest')->name('withdraw_crypto_request');
                Route::post('withdraw/fiat/request', 'TraderController@withdrawFiatRequest')->name('withdraw_fiat_request');
                Route::post('withdraw/crypto/confirm', 'TraderController@withdrawCryptoConfirm')->name('withdraw_crypto_confirm');
                Route::post('withdraw/fiat/confirm', 'TraderController@withdrawFiatConfirm')->name('withdraw_fiat_confirm');
                Route::post('withdraw/crypto/cancel', 'TraderController@withdrawCryptoCancel')->name('withdraw_crypto_cancel');
                Route::post('withdraw/fiat/cancel', 'TraderController@withdrawFiatCancel')->name('withdraw_fiat_cancel');

                Route::post('email/change/request', 'TraderController@emailChangeRequest')->name('email_change_request');
                Route::post('email/change/confirm', 'TraderController@emailChangeConfirm')->name('email_change_confirm');

                Route::post('notification/status', 'TraderController@setNotificationStatus')->name('set_notification_status');

                Route::post('notification/read', 'TraderController@setNotificationRead')->name('set_notification_read');
                Route::post('notification/delete', 'TraderController@deleteNotification')->name('delete_notification');
                Route::post('notifications/read_all', 'TraderController@setNotificationsReadAll')->name('set_notifications_read_all');
                Route::post('notifications/delete_all', 'TraderController@deleteAllNotifications')->name('delete_all_notifications');

                Route::post('message/send', 'TraderController@sendMessage')->name('send_message');

                Route::post('set_referral_type', 'TraderController@setReferralType')->name('set_referral_type');

                Route::post('validate_address', 'TraderController@validateAddress')->name('validate_address');

                Route::post('new_api_token', 'TraderController@newAPIToken')->name('new_api_token');
                Route::post('edit_api_token', 'TraderController@editAPIToken')->name('edit_api_token');
                Route::post('delete_api_token', 'TraderController@deleteAPIToken')->name('delete_api_token');
                Route::post('delete_all_api_tokens', 'TraderController@deleteAllAPITokens')->name('delete_all_api_tokens');

                Route::post('kyc_kontur_ind_request', 'TraderController@sendKYCKonturIndRequest')->name('send_kyc_kontur_ind_request');
                Route::post('kyc_kontur_comp_request', 'TraderController@sendKYCKonturCompRequest')->name('send_kyc_kontur_comp_request');
                Route::post('kyc_request', 'TraderController@sendKYCRequest')->name('send_kyc_request');
                Route::post('kyc_fix', 'TraderController@sendKYCFix')->name('send_kyc_fix');
                Route::post('kyc_payment', 'TraderController@setKYCPayment')->name('set_kyc_payment');

                Route::post('notify_fiat_qr_replenish', 'TraderController@NotifyFiatQRReplenish')->name('notify_fiat_qr_replenish');
                Route::post('notify_fiat_invoice_replenish', 'TraderController@NotifyFiatInvoiceReplenish')->name('notify_fiat_invoice_replenish');
            });

        });
    });
});
