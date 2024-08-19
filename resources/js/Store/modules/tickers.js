import _ from "lodash";
const state = () => ({
    tickersList: null,
    markets: null
});

const getters = {

};

const mutations = {
    setTickersList(state, list) {
        state.tickersList = list;
    },
    updateAskBid(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].ask = data.ask;
            state.tickersList[index].bid = data.bid;
        }
    },
    updateDayChange(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].previous_day = data.previous_day;
        }
    },
    updateLastDeal(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].latest = data.latest;
            state.tickersList[index].previous = data.previous;
        }
    },
    updateMax(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].max = data.max;
        }
    },
    updateMin(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].min = data.min;
        }
    },
    updateVolume(state, data) {
        let index = _.findIndex(state.tickersList, item => {
            return item.market.toLowerCase() === data.market.toLowerCase() && item.currency.toLowerCase() === data.currency.toLowerCase();
        });
        if (index > -1)
        {
            state.tickersList[index].volume = data.volume;
        }
    },
    updateSingleTicker(state, ticker) {
        if (state.tickersList === null)
        {
            state.tickersList = [ticker];
            return;
        }
        let list = state.tickersList;
        let findIndex = _.findIndex(list, (item) => {
            return item.currency === ticker.currency && item.market === ticker.market;
        });
        if (findIndex !== -1) {
            list[i] = ticker;
            state.tickersList = list;
        }
    },
    setMarketData(state, list) {
        state.markets = list;
    },
}

const actions = {
    getTickersListFromServer({ commit }) {
        return new Promise((resolve, reject) => {
            axios.get('/trader/ext/tickers')
                .then(response => {
                    commit('setTickersList', response.data.data);
                    resolve();
                }).catch(error => {
                console.log(error);
                reject();
            });
        });
    },
    getMarketDataFromServer({ commit }) {
        return new Promise((resolve, reject) => {
            axios.get('/trader/ext/market_data')
                .then(response => {
                    commit('setMarketData', response.data.data);
                    resolve();
                }).catch(error => {
                console.log(error);
                reject();
            });
        });
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
    modules: {

    }
}
