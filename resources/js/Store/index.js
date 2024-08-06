import { createStore, createLogger } from 'vuex'
import tickers from '@/Store/modules/tickers';
import trading from '@/Store/modules/trading';
import chat from '@/Store/modules/chat';

const debug = true;

export default createStore({
    modules: {
        tickers,
        trading,
        chat
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})
