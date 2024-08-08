<script setup>
import {useStore} from 'vuex';
import {computed, ref} from "vue";
import {useDisplay} from "vuetify";
import AskLimitForm from "@/Components/Forms/AskLimitForm.vue";
import AskMarketForm from "@/Components/Forms/AskMarketForm.vue";


const props = defineProps({
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    },
    pair: {
        type: Object,
        required: true,
    },
});

const store = useStore();
const {mobile} = useDisplay();

const best_ask = computed(() => {
    return store.state.trading.best_ask;
});

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

const type = ref('limit');

</script>
<template>
    <div class="trading-form-sell">
        <div class="trading-form-sell__actions align-start">
            <v-radio-group v-model="type" inline hide-details mandatory density="compact" row>
                <v-radio class="mr-2" color="primary" label="LIMIT" value="limit" :ripple="false"/>
                <v-radio class="mr-2" color="primary" label="MARKET" value="market" :ripple="false"/>
            </v-radio-group>

            <div v-if="mobile" class="trading-forms__best-wrapper">
                <div class="trading-forms__best">
                    <span> {{ $t('trading.forms.best_ask') }}: {{ best_ask }} </span>
                </div>
                <div class="trading-forms__best">
                    <span>{{ $t('trading.forms.best_bid') }}: {{ best_bid }}</span>
                </div>
            </div>
        </div>

        <div class="trading-form-sell__content">
            <keep-alive v-if="type === 'limit'">
                <AskLimitForm :currency="currency" :market="market" :pair="pair" />
            </keep-alive>

            <keep-alive v-if="type === 'market'">
                <AskMarketForm :currency="currency" :market="market" :pair="pair" />
            </keep-alive>
        </div>
    </div>
</template>
<style scoped lang="sass">
.trading-form-sell
    display: flex
    flex-flow: column
    flex-grow: 1
    &__content
        display: flex
        flex-flow: column
        flex-grow: 1
    &__best
        font-weight: normal
        font-size: 11px
        text-transform: none
        opacity: 0.6
    &__best-wrapper
        display: inline-flex
        gap: 12px
        float: right
        padding-top: 8px
        @media screen and (max-width: 599px)
            display: inline-block
            flex-flow: column
            padding-top: 0

.trading-form-sell .v-radio-group
    margin: 0
    padding-top: 4px
    padding-bottom: 4px
    font-size: 14px
    letter-spacing: normal
    max-width: 100%
    text-align: left

.v-application--is-rtl
    .trading-form-sell
        .trading-forms
            &__best-wrapper
                float: left
</style>
