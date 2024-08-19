import { createStore, createLogger } from 'vuex'
import tickers from '@/Store/modules/tickers';
import trading from '@/Store/modules/trading';
import chat from '@/Store/modules/chat';
import user from '@/Store/modules/user';
import Centrifuge from 'centrifuge';
import createWebSocketPlugin from "@/Plugins/socket.js";
const debug = false;

const socket = new Centrifuge(import.meta.env.VITE_WS_SERVER, {
    debug: true,
    subscribeEndpoint: '/trader/ext/private',
    /*subscribeHeaders: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },*/
    refreshEndpoint: '/trader/ext/refresh',
    /*refreshHeaders: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },*/
    refreshAttempts: 3,
    onRefreshFailed: () => {
        window.location.href = '/';
    },
});

const plugin = createWebSocketPlugin(socket)

export default createStore({
    modules: {
        tickers,
        trading,
        chat,
        user
    },
    strict: debug,
    plugins: debug ? [createLogger(), plugin] : [plugin]
})
