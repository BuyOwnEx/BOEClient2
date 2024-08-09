<script setup>
import {useDisplay} from "vuetify";
import {computed, ref} from "vue";
import {useStore} from "vuex";
import TradingFormBuy from "@/Components/Cards/TradingFormBuy.vue";
import TradingFormSell from "@/Components/Cards/TradingFormSell.vue";
import {usePage} from "@inertiajs/vue3";
const { mobile } = useDisplay();
const store = useStore();
const page = usePage();
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

const isAuth = computed(() => {
    return page.props.auth.user && page.props.auth.user.email_verified_at
});

const best_ask = computed(() => {
    return store.state.trading.best_ask;
});

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

const marginAvailable = import.meta.env.VITE_CONFIG_PRODUCT_TYPE === 'full';

const marginTradingAvailable = computed(() => {
    return props.pair.margin;
});

const selectedTab = ref(0);
const selectedFormTab = ref(0);

</script>
<template>
    <v-card class="trading-forms">
        <v-row v-if="!mobile && !isAuth" class="pa-1" no-gutters>
            <v-col class="trading-forms__form trading-forms__form--buy" cols="12" md="6">
                <v-card-title class="trading-forms__header component-title pa-0">
					<span>
						{{ $t('trading.order.direction.buy') }}
						{{ currency }}
					</span>
                    <div class="trading-forms__best">
                        <span> {{ $t('trading.forms.best_ask') }}: {{ best_ask }} </span>
                    </div>
                </v-card-title>
                <v-card-text class="trading-forms__content pa-0">
                    <TradingFormBuy :currency="currency" :market="market" :pair="pair" />
                </v-card-text>
            </v-col>

            <v-col class="trading-forms__form trading-forms__form--sell" cols="12" md="6">
                <v-card-title class="trading-forms__header component-title pa-0">
					<span>
						{{ $t('trading.order.direction.sell') }}
						{{ currency }}
					</span>
                    <div class="trading-forms__best">
                        <span>{{ $t('trading.forms.best_bid') }}: {{ best_bid }}</span>
                    </div>
                </v-card-title>
                <v-card-text class="trading-forms__content pa-0">
                    <TradingFormSell :currency="currency" :market="market" :pair="pair" />
                </v-card-text>
            </v-col>
        </v-row>

        <div class="own-lists-tabs-wrapper fill-height" v-else-if="!mobile && isAuth">
            <v-tabs v-model="selectedFormTab" color="primary" density="compact" :hide-slider="true">
                <v-tab value="1" size="small" :hide-slider="true">
                    {{ $t('trading.headers.make_order') }}
                </v-tab>
                <v-tab value="2" size="small" :hide-slider="true">
                    {{ $t('trading.headers.own_active_order_list') }}
                </v-tab>
                <v-tab value="3" size="small" :hide-slider="true">
                    {{ $t('trading.headers.own_history_deal_list') }}
                </v-tab>
                <v-tab v-if="marginAvailable && marginTradingAvailable" value="4" size="small" :hide-slider="true">
                    {{ $t('trading.headers.own_active_position_list') }}
                </v-tab>
            </v-tabs>
            <v-tabs-window v-model="selectedFormTab">
                <v-tabs-window-item value="1" class="fill-height">
                    <v-row class="pa-1 fill-height" no-gutters>
                        <v-col class="trading-forms__form trading-forms__form--buy" cols="12" md="6">
                            <v-card-title class="trading-forms__header component-title pa-0">
                                <span>
                                    {{ $t('trading.order.direction.buy') }}
                                    {{ currency }}
                                </span>
                                <div class="trading-forms__best">
                                    <span> {{ $t('trading.forms.best_ask') }}: {{ best_ask }} </span>
                                </div>
                            </v-card-title>
                            <v-card-text class="trading-forms__content pa-0">
                                <TradingFormBuy :currency="currency" :market="market" :pair="pair" />
                            </v-card-text>
                        </v-col>

                        <v-col class="trading-forms__form trading-forms__form--sell" cols="12" md="6">
                            <v-card-title class="trading-forms__header component-title pa-0">
                                <span>
                                    {{ $t('trading.order.direction.sell') }}
                                    {{ currency }}
                                </span>
                                <div class="trading-forms__best">
                                    <span>{{ $t('trading.forms.best_bid') }}: {{ best_bid }}</span>
                                </div>
                            </v-card-title>
                            <v-card-text class="trading-forms__content pa-0">
                                <TradingFormSell :currency="currency" :market="market" :pair="pair" />
                            </v-card-text>
                        </v-col>
                    </v-row>
                </v-tabs-window-item>
                <!--                        <OwnActiveOrderList :currency="currency" :market="market" />-->

                <v-tabs-window-item value="2" class="fill-height">
                    {{ $t('text-1.1')}}
                </v-tabs-window-item>
                <!--                        <OwnHistoryDealList :currency="currency" :market="market" />-->

                <v-tabs-window-item value="3" class="fill-height">
                    {{ $t('text-1.2')}}
                </v-tabs-window-item >

                <v-tabs-window-item v-if="marginAvailable && marginTradingAvailable" value="4" class="fill-height">
                    <!--                        <OwnActivePositionList :currency="currency" :market="market" />-->
                    {{ $t('text-1.3')}}
                </v-tabs-window-item>

            </v-tabs-window>
        </div>


        <!--	mobile version	-->
        <div v-else>
            <v-tabs v-model="selectedTab" grow>
                <v-tab :key="1">
                    {{ $t('trading.order.direction.buy') }}
                </v-tab>
                <v-tab :key="2">
                    {{ $t('trading.order.direction.sell') }}
                </v-tab>
            </v-tabs>

            <v-tabs-window v-model="selectedTab" class="py-1 px-1" touchless>
                <v-tabs-window-item value="1">
                    <TradingFormBuy :currency="currency" :market="market" :pair="pair" />
                </v-tabs-window-item>
                <v-tabs-window-item value="2">
                    <TradingFormSell :currency="currency" :market="market" :pair="pair" />
                </v-tabs-window-item>
            </v-tabs-window>
        </div>
    </v-card>
</template>
<style scoped lang="sass">
.v-window
    height: calc(100% - 36px)
    :deep(.v-window__container)
        height: 100%
.trading-forms
    display: flex
    flex-grow: 1
    flex-flow: column

    &__form
        display: flex
        flex-flow: column
        flex-grow: 1
        &--buy
            padding-right: 8px !important
        &--sell
            padding-left: 8px !important

    &__header
        display: flex
        justify-content: space-between
        margin-left: 4px

    &__best
        font-weight: normal
        font-size: 12px
        text-transform: none
        opacity: 0.6
        align-content: center

    &__content
        display: flex
        flex-flow: column
        flex-grow: 1

.v-application--is-rtl
    .trading-forms
        &__form
            &--buy
                padding-right: 0
                padding-left: 8px !important
            &--sell
                padding-left: 0
                padding-right: 8px !important

@media screen and (min-width: 960px)
    .trading-forms
        font-weight: normal

@media screen and (max-width: 959px)
    .trading-forms
        &__sell
            padding-top: 2rem
</style>
