import { createStore, createLogger } from 'vuex'
import tickers from '@/Store/modules/tickers';
import trading from '@/Store/modules/trading';
import chat from '@/Store/modules/chat';
import user from '@/Store/modules/user';

const debug = true;

export default createStore({
    modules: {
        tickers,
        trading,
        chat,
        user
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})
