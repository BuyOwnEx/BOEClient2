import _ from "lodash";
export default function createWebSocketPlugin (socket) {
    return (store) => {
        let sub_tickers = null;

        const tickersPubHandler = (data) => {
            let ticker = _.find(store.state.tickers.tickersList, item => {
                return item.market.toLowerCase() === data.data.ticker.market.toLowerCase() && item.currency.toLowerCase() === data.data.ticker.currency.toLowerCase();
            });
            if (ticker === undefined) {
                return;
            }
            let copy = ticker;
            switch (data.data.action) {
                case 'update_ticker':
                    copy.bid = data.data.ticker.bid_price;
                    copy.ask = data.data.ticker.ask_price;
                    store.commit('tickers/updateSingleTicker', copy);
                    if(ticker.currency.toLowerCase() === this.selectedCurrency.toLowerCase() && ticker.market.toLowerCase() === this.selectedMarket.toLowerCase())
                    {
                        store.commit('trading/setBestAsk', data.data.ticker.ask_price);
                        store.commit('trading/setBestBid', data.data.ticker.bid_price);
                    }
                    break;
                case 'day_change_ticker':
                    copy.previous_day = data.data.ticker.price;
                    store.commit('tickers/updateSingleTicker', copy);
                    break;
                case 'last_deal_ticker':
                    copy.latest = data.data.ticker.price;
                    copy.previous = data.data.ticker.prev_price;
                    store.commit('tickers/updateSingleTicker', copy);
                    break;
                case 'max_day_ticker':
                    copy.max = data.data.ticker.max;
                    store.commit('tickers/updateSingleTicker', copy);
                    break;
                case 'min_day_ticker':
                    copy.min = data.data.ticker.min;
                    store.commit('tickers/updateSingleTicker', copy);
                    break;
                case 'day_volume_ticker':
                    copy.volume = data.data.ticker.volume;
                    store.commit('tickers/updateSingleTicker', copy);
                    break;
            }
        };


        socket.on('data', data => {
            //store.commit('receiveData', data);
            console.log('receiveData', data);
        })
        store.subscribe(mutation => {
            if (mutation.type === 'setPair') {
                //socket.emit('update', mutation.payload)
                console.log('setPair was called by mutation')
            }
            else if (mutation.type === 'setGraphPeriod')
            {
                console.log('setGraphPeriod was called by mutation')
            }
        })
        sub_tickers = socket.subscribe('public:tickers');
        sub_tickers.on('publish', tickersPubHandler);
        socket.connect();
    }
}
