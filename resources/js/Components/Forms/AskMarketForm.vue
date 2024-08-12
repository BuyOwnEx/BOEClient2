<script setup>
import { useStore } from 'vuex';
import { computed, ref } from "vue";
import _ from "lodash";
import {usePage} from "@inertiajs/vue3";
import { vMaska } from "maska/vue";
import BigNumber from 'bignumber.js';
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });
import i18n from '@/Translations/i18n.js'
import TradingFormsConfirmDialog from "@/Components/Dialogs/TradingFormsConfirmDialog.vue";

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

const additionalParamsEnabled = ref(false);
const useMargin = ref(false);
const offers = ref([{id:1, percent: 1.5, days: 15, amount: 100}]);
const leverageLevel = ref(5);

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

const select_choose = ref([
    {value: 0, title: i18n.global.t('table_header.amount_full')},
    {value: 1, title: i18n.global.t('table_header.volume')}
]);

const form = ref({
    type: 'MARKET',
    currency: props.currency.toUpperCase(),
    market: props.market.toUpperCase(),
    side: 1,
    base: 0,
    amount: '0',
    sl_rate: '0',
    tp_rate: '0',
    ts_offset: '0',
    offer: null,
})

const isAuth = computed(() => {
    return page.props.auth.user && page.props.auth.user.email_verified_at
});

const conditionalOrdersAvailable = import.meta.env.VITE_CONFIG_PRODUCT_TYPE === 'startPlus' || import.meta.env.VITE_CONFIG_PRODUCT_TYPE === 'full';
const marginAvailable = import.meta.env.VITE_CONFIG_PRODUCT_TYPE === 'full';

const balances = computed(() => {
    return store.state.user.balances;
});

const own_fees = computed(() => {
    return store.state.user.fees
        ? _.find(store.state.user.fees, item => item.currency.toUpperCase() === props.currency.toUpperCase() && item.market.toUpperCase() === props.market.toUpperCase())
        : null;
});

const offers_select = computed(() => {
    return _.map(offers.value, value => {
        return {
            id: value.id,
            name: i18n.global.t('trading.order.offer_option', [
                BigNumber(value.percent).toString(),
                BigNumber(value.days).toString(),
                BigNumber(value.amount).toString(),
                props.market.toUpperCase(),
            ]),
        };
    });
});

const minAmount = computed(() => {
    return BigNumber(props.pair.minAmount).toString();
});

const marginTradingAvailable = computed(() => {
    return props.pair.margin;
});

const fee_currency = computed(() => {
    if (isAuth.value)
    {
        if(own_fees.value !== null && own_fees.value !== undefined)
        {
            return BigNumber(own_fees.value.taker_fee).div(100);
        }
        else return BigNumber(props.pair.takerFee).div(100);
    }
    else
    {
        return BigNumber(props.pair.takerFee).div(100);
    }
});

const amountScale = computed(() => {
    return parseInt(props.pair.amountScale);
});

const rateScale = computed(() => {
    return parseInt(props.pair.rateScale);
});

const feeScale = computed(() => {
    return rateScale.value + amountScale.value + Math.abs(fee_currency.value.e);
});

const fee_visible = computed(() => {
    return fee_currency.value.times(BigNumber(100)).toString();
});

const balance = computed(() => {
    if (isAuth.value) {
        let scale = 8;
        let amount = _.get(balances.value, props.currency.toUpperCase(), 0);
        if (amount.available !== undefined) {
            return BigNumber(amount.available).dp(scale, 1);
        }
        return BigNumber(11).dp(scale, 1);
    } else return BigNumber(11);
});

const useMarginEnabled = computed(() => {
    return Number(form.value.amount);
});

const useCondOrdersEnabled = computed(() => {
    return Number(form.value.amount);
});

const selectedOffer = computed(() => {
    if(form.value.offer !== null) return offers.value !== null ? _.find(offers.value, item => item.id === form.value.offer) : null
    else return {}
});

const setAmount = () => {
    if(form.value.base === 0)
    {
        form.value.amount = balance.value
            .times(percent.value)
            .div(100)
            .decimalPlaces(amountScale.value, 1)
            .toString();
        fee_amount.value = BigNumber(form.value.amount)
            .times(BigNumber(best_bid.value))
            .times(fee_currency.value)
            .decimalPlaces(feeScale.value, 1)
            .toString();
    }
    else {
        form.value.amount = balance.value
            .times(BigNumber(best_bid.value))
            .times(percent.value)
            .div(100)
            .decimalPlaces(amountScale.value, 1)
            .toString();
        fee_amount.value = BigNumber(form.value.amount)
            .times(fee_currency.value)
            .decimalPlaces(feeScale.value, 1)
            .toString();
    }
};

const sendAskMarket = () => {
    if (!form.value.amount) {
        //this.pushErrorNotification(_, 'incorrect');
        return;
    }

    let payload = additionalParamsEnabled.value ? form.value : _.omit(form.value, ['sl_rate', 'tp_rate', 'ts_offset']);
    if (marginTradingAvailable.value) {
        form.value.margin = useMargin.value ? 1 : 0;
    } else form.value.margin = 0;
    axios.post('/trader/ext/order/market', payload).then(response => {
        if (response.data.success === true) {
            console.log(response.data);
        }
    });
};

const findCurrencyScale = (currency) => {
    const currencies = store.state.trading.all_currencies;
    const index = _.findIndex(currencies.data, item => {
        return item.currency.toUpperCase() === currency.toUpperCase();
    });
    if (index > -1) return currencies.data[index].scale;
};
const formatWithScaleInAllCurrencies = (value, currency) => {
    const scale = findCurrencyScale(currency.toUpperCase());
    if (scale) {
        return BigNumber(value).toFixed(scale, 1);
    } else {
        return BigNumber(value).toFixed(2, 1);
    }
};
const percent = ref(0);
const fee_amount = ref('0');

const amount_options = {
    mask: '9.'+"#".repeat(props.pair.amountScale),
    tokens: {
        9: { pattern: /[0-9]/, multiple: true }
    }
};
const rate_options = {
    mask: '9.'+"#".repeat(props.pair.rateScale),
    tokens: {
        9: { pattern: /[0-9]/, multiple: true }
    }
};
const volume_options = {
    mask: '9.'+"#".repeat(props.pair.rateScale+props.pair.amountScale),
    tokens: {
        9: { pattern: /[0-9]/, multiple: true }
    }
};
const unmaskedAmount = ref('0')
const unmaskedSL = ref('0')
const unmaskedTP = ref('0')
const unmaskedTS = ref('0')

defineExpose({ unmaskedAmount, unmaskedSL, unmaskedTP, unmaskedTS });

const baseChanged = () => {
    form.value.amount = '0';
    percent.value = 0;
    fee_amount.value = '0';
};

const updatePercentAmountChanged = (event) => {
    if (!useMarginEnabled.value) useMargin.value = false;
    if(event.detail.masked !== '')
    {
        if(form.value.base === 0)
        {
            percent.value =
                BigNumber(event.detail.masked)
                    .times(100)
                    .div(balance.value)
                    .decimalPlaces(0, 1)
                    .toNumber();
            fee_amount.value = BigNumber(event.detail.masked)
                .times(BigNumber(best_bid.value))
                .times(fee_currency.value)
                .decimalPlaces(feeScale.value, 1)
                .toString();
        }
        else {
            percent.value =
                BigNumber(event.detail.masked)
                    .div(BigNumber(best_bid.value))
                    .times(100)
                    .div(balance.value)
                    .decimalPlaces(0, 1)
                    .toNumber();
            fee_amount.value = BigNumber(event.detail.masked)
                .times(fee_currency.value)
                .decimalPlaces(feeScale.value, 1)
                .toString();
        }
    }
    else {
        percent.value = 0;
        fee_amount.value = '0';
    }
}

const resetOffer = () => {
    if(!useMargin.value) form.value.offer = null
};

const isSetSL = computed(() => {
    return form.value.sl_rate !== '0' && form.value.sl_rate !== '';
});
const isSetTP = computed(() => {
    return form.value.tp_rate !== '0' && form.value.tp_rate !== '';
});
const isSetTS = computed(() => {
    return form.value.ts_offset !== '0' && form.value.ts_offset !== '';
});

const showCondParams = ref(false);

const setCondParams = () => {
    additionalParamsEnabled.value = false;
    showCondParams.value = !!(isSetSL.value || isSetTP.value || isSetTS.value);
};
const resetCondParams = () => {
    form.value.sl_rate = '0';
    form.value.tp_rate = '0';
    form.value.ts_offset = '0';
    additionalParamsEnabled.value = false;
};
</script>
<template>
    <form class="blf" @submit.prevent="sendAskMarket">
        <input name="type" type="hidden" value="MARKET" />
        <input name="currency" type="hidden" :value="currency" />
        <input name="market" type="hidden" :value="market" />
        <input name="side" type="hidden" value="1" />
        <input name="base" type="hidden" :value="form.base" />

        <div class="d-flex flex-grow-0 flex-column">
            <div class="d-inline-block position-relative fill-height">
                <Transition mode="out-in" name="slide-left" class="position-relative fill-height" style="width: 100%">
                    <div class="fill-height" style="position: absolute; width: 100%" v-if="!additionalParamsEnabled && !useMargin">
                        <div class="d-flex align-baseline">
                            <v-select
                                v-model="form.base"
                                :items="select_choose"
                                variant="underlined"
                                color="primary"
                                density="compact"
                                flat
                                tile
                                hide-details
                                single-line
                                class="flex-shrink-1 flex-grow-0"
                                @update:modelValue="baseChanged"
                            >
                                <template v-slot:selection="{ item, index }">
                                    <span class="text-overline">{{ item.title }}</span>
                                </template>
                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" density="compact" slim :ripple="false" tile variant="plain">
                                        <template v-slot:title>
                                            <span class="text-overline">{{ item.title }}</span>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-select>
                            <v-text-field
                                v-model="form.amount"
                                :label="form.base === 0 ? $t('table_header.amount') : $t('table_header.volume')"
                                type="text"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="0"
                                class="mt-2 flex-shrink-0 flex-grow-1"
                                v-maska:unmaskedAmount.masked="form.base === 0 ? amount_options : volume_options"
                                @maska="updatePercentAmountChanged"
                            >
                                <template #append-inner>
                                    <span class="button-currency-text">{{ form.base === 0 ? currency.toUpperCase() : market.toUpperCase() }}</span>
                                </template>
                            </v-text-field>
                        </div>

                        <v-slider
                            v-model="percent"
                            min="0"
                            max="100"
                            step="1"
                            :ticks="[0,25,50,75,100]"
                            show-ticks="always"
                            tile
                            track-size="2"
                            color="primary"
                            thumb-color="primary"
                            thumb-size="10"
                            thumb-label
                            :ripple="false"
                            hide-details
                            @update:modelValue="setAmount"
                        ></v-slider>

                        <div class="blf__volume">
                            <div class="blf__text-field-hint">
                                {{ $t('table_header.fee') }}: {{ fee_visible }}% ~ {{ fee_amount }} {{ market.toUpperCase() }}
                            </div>
                            <div class="mt-2">
                                <div class="blf__text-field-hint" v-if="showCondParams && isSetSL">
                                    + SL-MARKET: <span class="text-success text-uppercase text-[14px]">{{ form.sl_rate }} {{ market.toUpperCase() }}</span>
                                </div>
                                <div class="blf__text-field-hint" v-if="showCondParams && isSetTP">
                                    + TP-MARKET: <span class="text-success text-uppercase text-[14px]">{{ form.tp_rate }} {{ market.toUpperCase() }}</span>
                                </div>
                                <div class="blf__text-field-hint" v-if="showCondParams && isSetTS">
                                    + TS-MARKET: <span class="text-success text-uppercase text-[14px]">{{ form.ts_offset }} {{ market.toUpperCase() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fill-height" style="position: absolute; width: 100%" v-else-if="additionalParamsEnabled && !useMargin">
                        <div v-if="!useMargin && conditionalOrdersAvailable && additionalParamsEnabled" class="blf__params">
                            <div class="mb-4 text-start pl-1">
                                <span class="text-[10px] text-disabled" style="font-size: 10px;">
                                    {{ $t('trading.forms.main_sell_order') }}
                                    <span class="text-success text-uppercase text-[14px]" v-if="form.base === 0">{{form.amount}} {{ currency.toUpperCase() }}</span>
                                    <span class="text-success text-uppercase text-[14px]" v-else>{{ currency.toUpperCase() }}</span>
                                    {{ $t('trading.forms.for_market_price') }}
                                    <span v-if="form.base === 1">{{ $t('trading.forms.for_volume') }} <span class="text-success text-uppercase text-[14px]">{{form.amount}} {{ market.toUpperCase() }}</span></span>
                                </span>
                            </div>

                            <v-text-field
                                v-model="form.sl_rate"
                                :label="$t('trading.order.sl_rate')"
                                :disabled="!additionalParamsEnabled"
                                type="text"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="0"
                                v-maska:unmaskedSL.masked=rate_options
                            >
                                <template #append-inner>
							<span class="button-currency-text">
								{{ market.toUpperCase() }}
							</span>
                                </template>
                            </v-text-field>

                            <v-text-field
                                v-model="form.tp_rate"
                                :label="$t('trading.order.tp_rate')"
                                :disabled="!additionalParamsEnabled"
                                type="text"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="0"
                                class="mt-3"
                                v-maska:unmaskedTP.masked=rate_options
                            >
                                <template #append-inner>
							<span class="button-currency-text">
								{{ market.toUpperCase() }}
							</span>
                                </template>
                            </v-text-field>

                            <v-text-field
                                v-model="form.ts_offset"
                                :label="$t('trading.order.ts_offset')"
                                :disabled="!additionalParamsEnabled"
                                type="text"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="0"
                                class="mt-3"
                                v-maska:unmaskedTS.masked=rate_options
                            >
                                <template #append-inner>
                                    <span class="button-currency-text">{{ market.toUpperCase() }}</span>
                                </template>
                            </v-text-field>
                            <div class="d-flex justify-space-around">
                                <v-btn
                                    variant="text"
                                    size="x-small"
                                    color="primary"
                                    class="mt-2 align-center"
                                    tile
                                    @click="setCondParams"
                                >
                                    {{ $t('common.apply') }}
                                </v-btn>
                                <v-btn
                                    variant="text"
                                    size="x-small"
                                    color="secondary"
                                    class="mt-2 align-center"
                                    tile
                                    @click="resetCondParams"
                                >
                                    {{ $t('common.reset') }}
                                </v-btn>
                            </div>
                        </div>
                    </div>
                    <div style="position: absolute; height: 100%; width: 100%" v-else-if="!additionalParamsEnabled && useMargin">
                        <div class="blf__margin" v-if="useMargin && marginAvailable">
                            <v-select
                                v-model="form.offer"
                                :items="offers_select"
                                :label="$t('trading.order.offer')"
                                item-title="name"
                                item-value="id"
                                variant="underlined"
                                density="compact"
                                color="primary"
                                hide-details
                                :list-props="{ density: 'compact'}"
                            />
                        </div>
                    </div>
                </Transition>
            </div>
            <div class="blf__switch">
                <div v-if="!useMargin && conditionalOrdersAvailable " class="d-flex justify-start">
                    <v-switch
                        v-model="additionalParamsEnabled"
                        :label="$t('trading.order.use_additional_params')"
                        :disabled="!useCondOrdersEnabled"
                        hide-details
                        left
                        flat
                        :ripple="false"
                        density="compact"
                        color="primary"
                    />
                </div>
                <div v-if="marginTradingAvailable && marginAvailable && !additionalParamsEnabled" class="d-flex justify-start">
                    <v-switch
                        v-model="useMargin"
                        :label="$t('trading.order.use_margin')"
                        :disabled="!useMarginEnabled"
                        hide-details
                        left
                        flat
                        :ripple="false"
                        density="compact"
                        color="primary"
                        @update:modelValue="resetOffer"
                    />
                </div>
            </div>
        </div>

        <div class="blf__footer mt-2">
            <TradingFormsConfirmDialog
                order-type="market"
                action-type="sell"
                :amount="form.amount"
                :currency="currency"
                :market="market"
                :is-additional-params="additionalParamsEnabled.value"
                :stop-loss="form.sl_rate"
                :take-profit="form.tp_rate"
                :trailing-stop="form.ts_offset"
                :is-leverage="useMargin.value"
                :leverage-offer="selectedOffer.value"
                :leverage-level="leverageLevel"
                @confirm="sendAskMarket"
            >
                <v-btn color="error" height="24" block>
                    {{ $t('common.sell') }}
                </v-btn>
            </TradingFormsConfirmDialog>

            <div class="blf__footer__available text-center mt-1">
                {{ $t('trading.order.available') }}
                <span class="blf__footer__balance">
						{{ formatWithScaleInAllCurrencies(balance, currency) }}
					</span>
                {{ currency.toUpperCase() }}
            </div>
        </div>
    </form>
</template>
<style scoped lang="scss">
.slide-left-enter-active,
.slide-left-leave-active {
    transition: all 0.25s ease-out;
}
.slide-left-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.slide-left-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
.v-slider {
    font-size: 10px;
}
.blf {
    display: flex;
    flex-flow: column;
    flex-grow: 1;
    justify-content: space-between;

    &__volume {
        margin: 8px 0 0;
    }

    &__switch {
        margin-top: 4px;
    }

    &__percents {
        display: grid;
        grid-gap: 2px;
        grid-template-columns: repeat(4, 1fr);
        margin-top: 6px;
        margin-bottom: 8px;
    }

    &__text-field-hint {
        line-height: 1;
        font-size: 0.75rem;
        padding: 0 6px;
        opacity: 0.6;
        margin-top: 4px;
    }

    &__margin {
        padding: 0 4px;
    }

    &__footer {
        &__available {
            font-size: 11px;
        }
    }
}
.v-text-field {
    ::v-deep(.v-field__input) {
        min-height: 32px !important;
        padding-top: 4px;
        padding-bottom: 4px;
    }
}
.v-select {
    ::v-deep(.v-field__input) {
        min-height: 32px !important;
        padding-top: 4px;
        padding-left: 4px;
    }
    ::v-deep(.v-field__append-inner)
    {
        padding-top: 4px;
    }
}
.v-switch {
    ::v-deep(.v-selection-control) {
        min-height: 28px !important;
    }
    ::v-deep(.v-label) {
        font-size: 11px;
    }
}
</style>
