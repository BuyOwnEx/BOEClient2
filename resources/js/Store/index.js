import { createStore, createLogger } from 'vuex'
import tickers from '@/Store/modules/tickers';
import trading from '@/Store/modules/trading';

const debug = true;

export default createStore({
    modules: {
        tickers,
        trading
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})
