<script setup>
import { useStore } from 'vuex';
import {computed, ref} from "vue";
import {
    mdiSend
} from '@mdi/js'

const store = useStore();

const messages = computed(() => {
    return store.state.chat.messages;
});

const messageText = ref('');

const sendMessage = () => {
    if (messageText.value.trim() === '') {
        messageText.value = '';
        return;
    }
    axios.post('/trader/ext/message/send', {
        message: messageText.value,
    })
    .then(response => {
        if (response.data.success === true) {
            messageText.value = '';
        }
    });
};

</script>
<template>
    <v-card class="trading-chat pa-2">
        <v-card-title class="trading-chat__header component-title pa-0">
            {{ $t('trading.headers.chat') }}
        </v-card-title>
        <v-card-text class="trading-chat__content pa-0">
            <v-list class="trading-chat-content" flat>
                <v-list-item class="trading-chat-message" v-for="msg in messages" :key="msg.ts" :message="msg">
                    <v-list-item-title class="trading-chat-message__title text--disabled">
                        <span class="trading-chat-message__author"> {{ msg.login }}: </span>
                        <span class="trading-chat-message__date"> {{ new Date(msg.ts * 1000).toLocaleTimeString() }} </span>
                    </v-list-item-title>
                    <div class="trading-chat-message__text">
                        {{ msg.message }}
                    </div>
                </v-list-item>
            </v-list>
        </v-card-text>
        <v-card-actions class="pa-0" style="min-height: unset;">
            <v-form class="trading-chat-add-message" @submit.prevent="sendMessage">
                <input class="d-none" autocomplete="off" name="hidden" type="text" />
                <v-text-field
                    v-model="messageText"
                    class="trading-chat-add-message__field pr-1"
                    hide-details
                    variant="underlined"
                    single-line
                    density="compact"
                    tile
                    :label="$t('trading.your_message')"
                    maxlength="512"
                    counter="512"
                    autocomplete="off"
                />
                <v-btn
                    class="trading-chat-add-message__send"
                    color="primary"
                    type="submit"
                    density="compact"
                    :disabled="messageText.value === ''"
                    variant="plain"
                    size="small"
                >
                    <v-icon>
                        {{ mdiSend }}
                    </v-icon>
                </v-btn>
            </v-form>
        </v-card-actions>
    </v-card>
</template>
<style scoped lang="scss">
.trading-chat {
    display: flex;
    flex-grow: 1;
    flex-flow: column;
    position: relative;
}

.text--disabled {
    color: #00000061 !important;
}
.v-theme--dark .text--disabled {
    color: #ffffff80 !important;
}

.trading-chat-content {
    position: absolute;
    top: 30px;
    left: 8px;
    right: 4px;
    bottom: 40px;
    margin-right: 5px;

    overflow-y: scroll;
    mask-image: linear-gradient(to top, transparent, black), linear-gradient(to left, transparent 6px, black 6px);
    mask-size: 100% 20000px;
    mask-position: left bottom;
    -webkit-mask-size: 100% 20000px;
    -webkit-mask-position: left bottom;
    transition: mask-position 0.3s, -webkit-mask-position 0.3s;

    &:hover {
        mask-position: left top;
        -webkit-mask-position: left top;
    }
}

.v-application--is-rtl .trading-chat-content {
    margin-right: 0;
    margin-left: 5px;
    mask-image: linear-gradient(to top, transparent, black), linear-gradient(to right, transparent 6px, black 6px);
}


.trading-chat-message {
    padding-left: 0 !important;
    padding-right: 4px;
    padding-inline: 4px !important;
    padding-bottom: 0;
    padding-top: 0;

    &__title {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: lowercase;
    }
    &__date {
        font-size: 0.65rem;
    }
    &__text {
        font-size: 0.7rem;
        word-break: break-word;
    }
}
.v-application--is-rtl .trading-chat-message {
    padding-left: 4px !important;
    padding-right: 0 !important;
}


.trading-chat-add-message {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-top: auto;
    width: 100%;
}

.v-application--is-rtl {
    .trading-chat-add-message {
        &__send i {
            transform: scaleX(-1) !important;
        }
    }
}
</style>
