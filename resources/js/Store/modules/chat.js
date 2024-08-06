const state = () => ({
    limit: 50,
    messages: [
        {message: 'Hello', login: 'BOETest', ts: 1257894000},
        {message: 'wtf?', login: 'Admin', ts: 1257914000},
        {message: 'What are you doing here? What are you doing here? What are you doing here? What are you doing here? What are you doing here?', login: 'Admin', ts: 1257944000}
    ],
});

const getters = {

};

const mutations = {
    setHistoryMessages(state, list) {
        state.messages = list;
    },
    addMessage(state, item) {
        state.messages.push(item);
        if (state.messages > state.limit) {
            state.messages.shift();
        }
    },
}

const actions = {

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
    modules: {

    }
}
