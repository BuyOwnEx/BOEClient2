export default function createWebSocketPlugin (socket) {
    return (store) => {
        let sub_tickers = null;
        let sub_order_book = null;
        let sub_depth = null;
        let sub_history_deals = null;
        let sub_graph = null;

        const tickersPubHandler = (data) => {
            switch (data.data.action) {
                case 'update_ticker':
                    store.commit('tickers/updateAskBid', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        bid: data.data.ticker.bid_price,
                        ask: data.data.ticker.ask_price
                    });
                    if(data.data.ticker.currency.toLowerCase() === store.state.trading.selectedCurrency.toLowerCase() && data.data.ticker.market.toLowerCase() === store.state.trading.selectedMarket.toLowerCase())
                    {
                        store.commit('trading/setBestAsk', data.data.ticker.ask_price);
                        store.commit('trading/setBestBid', data.data.ticker.bid_price);
                    }
                    break;
                case 'day_change_ticker':
                    store.commit('tickers/updateDayChange', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        previous_day: data.data.ticker.price
                    });
                    break;
                case 'last_deal_ticker':
                    store.commit('tickers/updateLastDeal', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        latest: data.data.ticker.price,
                        previous: data.data.ticker.prev_price
                    });
                    break;
                case 'max_day_ticker':
                    store.commit('tickers/updateMax', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        max: data.data.ticker.max
                    });
                    break;
                case 'min_day_ticker':
                    store.commit('tickers/updateMin', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        min: data.data.ticker.min
                    });
                    break;
                case 'day_volume_ticker':
                    store.commit('tickers/updateVolume', {
                        currency: data.data.ticker.currency,
                        market: data.data.ticker.market,
                        volume: data.data.ticker.volume
                    });
                    break;
            }
        };
        const orderBookPubHandler = (data) => {
            if (data.data.side) {
                store.commit('trading/setAskList', data.data.levels);
            } else {
                store.commit('trading/setBidList', data.data.levels);
            }
        };
        const updateDepthPubHandler = (data) => {
            store.commit('trading/setAskAmountDepth', data.data.depth.ask_amount);
            store.commit('trading/setBidAmountDepth', data.data.depth.bid_amount);
            store.commit('trading/setAskVolumeDepth', data.data.depth.ask_vol);
            store.commit('trading/setBidVolumeDepth', data.data.depth.bid_vol);
        };
        const historyDealPubHandler = (data) => {
            store.commit('trading/addDealToHistoryDealList', data.data);
        };
        const graphPubHandler = (data) => {
            if (data.data.action === 'addPoint') {
                store.commit('trading/addPoint', data.data.point);
            }
            if (data.data.action === 'updatePoint') {
                store.commit('trading/updatePoint', data.data.point);
            }
        };

        /*socket.on('data', data => {
            //store.commit('receiveData', data);
            console.log('receiveData', data);
        })*/
        store.subscribe(mutation => {
            if (mutation.type === 'trading/setPair') {
                //socket.emit('update', mutation.payload)
                console.log('setPair was called by mutation');
                if(sub_order_book)
                {
                    sub_order_book.unsubscribe();
                    sub_order_book.removeAllListeners();
                   store.commit('trading/resetOrderBook');
                }
                if(sub_history_deals)
                {
                    sub_history_deals.unsubscribe();
                    sub_history_deals.removeAllListeners();
                    store.commit('trading/resetHistoryDealList');
                }
                if(sub_depth)
                {
                    sub_depth.unsubscribe();
                    sub_depth.removeAllListeners();
                }

                sub_tickers = socket.subscribe('public:tickers');
                sub_order_book = socket.subscribe('public:order_book_' + store.state.trading.selectedCurrency.toLowerCase() + '_' + store.state.trading.selectedMarket.toLowerCase());
                sub_depth = socket.subscribe('public:update_depth_' + store.state.trading.selectedCurrency.toLowerCase() + '_' + store.state.trading.selectedMarket.toLowerCase());
                sub_history_deals = socket.subscribe('public:history_deals_' + store.state.trading.selectedCurrency.toLowerCase() + '_' + store.state.trading.selectedMarket.toLowerCase());

                sub_tickers.on('publish', tickersPubHandler);
                sub_order_book.on('publish', orderBookPubHandler);
                sub_depth.on('publish', updateDepthPubHandler);
                sub_history_deals.on('publish', historyDealPubHandler);

                socket.connect();
            }
            else if (mutation.type === 'trading/setGraphPeriod')
            {
                if(sub_graph)
                {
                    sub_graph.unsubscribe();
                    sub_graph.removeAllListeners();
                }
                sub_graph = socket.subscribe('public:graph_' + store.state.trading.selectedPeriod + '_' + store.state.trading.selectedCurrency.toLowerCase() + '_' + store.state.trading.selectedMarket.toLowerCase());
                sub_graph.on('publish', graphPubHandler);
                console.log('setGraphPeriod was called by mutation')
            }
        })
    }
}
