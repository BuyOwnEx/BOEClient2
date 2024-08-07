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
const volume = ref(0);
const useMargin = ref(false);
const offers = ref([]);
const leverageLevel = ref(5);

const form = ref({
    type: 'LIMIT',
    currency: props.currency.toUpperCase(),
    market: props.market.toUpperCase(),
    side: 0,
    amount: '0',
    rate: '0',
    sl_rate: null,
    tp_rate: null,
    ts_offset: null,
    offer: null,
})

const best_ask = computed(() => {
    return store.state.trading.best_ask;
});

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

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

const activeTicker = computed(() => {
    return store.getters['trading/activeTicker'];
});

const markets = computed(() => {
    return _.get(store.state.tickers.markets, props.market.toUpperCase(), null);
});

const selectedMarket = computed(() => {
    if (markets.value === null) {
        return null;
    }
    let market = _.find(markets.value, item => item.currency.toUpperCase() === this.currency.toUpperCase());
    return market === undefined ? null : market;
});

const amountScale = computed(() => {
    return selectedMarket.value === null ? 2 : parseInt(selectedMarket.value.amountScale);
});

const rateScale = computed(() => {
    return selectedMarket.value === null ? 2 : parseInt(selectedMarket.value.rateScale);
});

const minAmount = computed(() => {
    return selectedMarket.value === null ? 0 : parseInt(selectedMarket.value.minAmount);
});

const marginTradingAvailable = computed(() => {
    return selectedMarket.value === null ? false : selectedMarket.value.margin;
});

const fee_currency = computed(() => {
    if(selectedMarket.value !== null)
    {
        if (isAuth.value) {
            if(form.value.rate < best_ask.value) {
                if(own_fees.value !== null && own_fees.value !== undefined)
                {
                    return BigNumber(own_fees.value.maker_fee).div(100);
                }
                else return BigNumber(selectedMarket.value.makerFee).div(100);
            }
            else {
                if(own_fees.value !== null && own_fees.value !== undefined)
                {
                    return BigNumber(own_fees.value.taker_fee).div(100);
                }
                else return BigNumber(selectedMarket.value.takerFee).div(100);
            }
        }
        else
        {
            if(form.value.rate < best_ask.value) {
                return BigNumber(selectedMarket.value.makerFee).div(100);
            }
            else return BigNumber(selectedMarket.value.takerFee).div(100);
        }
    }
    else return BigNumber(0);
});

const fee_visible = computed(() => {
    return fee_currency.value.times(BigNumber(100)).toString();
});

const balance = computed(() => {
    if (isAuth.value) {
        let scale = 8;
        let amount = _.get(balances.value, props.market.toUpperCase(), 0);
        if (amount.available !== undefined) {
            return BigNumber(amount.available).dp(scale, 1);
        }
        return BigNumber(0).dp(scale, 1);
    } else return BigNumber(0);
});

const isAdditionalParams = computed(() => {
    return additionalParamsEnabled.value;
});

const useMarginEnabled = computed(() => {
    return Number(form.value.amount);
});

const selectedOffer = computed(() => {
    const selectedOfferID = form.value.offer;
    return offers.value?.find(item => item.id === selectedOfferID)
});

const validateNumber = (evt) => {
    let charCode = evt.which ? evt.which : evt.keyCode;
    if (
        charCode > 31 &&
        (charCode < 48 || charCode > 57) &&
        (charCode < 96 || charCode > 105) &&
        charCode !== 46 &&
        charCode !== 35 &&
        charCode !== 36 &&
        charCode !== 37 &&
        charCode !== 39 &&
        evt.key !== '.'
    )
    {
        evt.preventDefault();
    }
    else {
        return true;
    }
};

const setAmount = (percents = 100) => {
    if (form.value.rate !== '' && form.value.rate !== '0') {
        form.value.amount = balance.value
            .div(BigNumber(form.value.rate))
            .div(100)
            .times(percents)
            .decimalPlaces(amountScale.value, 1)
            .toString();
    } else {
        form.value.amount = '0';
    }
};

const sendBidLimit = () => {
    console.log(form.value);
    if (!form.value.amount) {
        //this.pushErrorNotification(_, 'incorrect');
        return;
    }

    let payload = additionalParamsEnabled.value ? form.value : _.omit(form.value, ['sl_rate', 'tp_rate', 'ts_offset']);
    if (marginTradingAvailable.value) {
        form.value.margin = useMargin.value ? 1 : 0;
    } else form.value.margin = 0;
    axios.post('/trader/ext/order/limit', payload).then(response => {
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

</script>
<template>
    <form class="blf" @submit.prevent="sendBidLimit">
        <input name="type" type="hidden" value="LIMIT" />
        <input name="currency" type="hidden" :value="currency" />
        <input name="market" type="hidden" :value="market" />
        <input name="side" type="hidden" value="0" />

        <div>
            <v-text-field
                v-model="form.amount"
                ref="bid_limit_amount"
                :label="$t('trading.order.direction.buy')"
                type="text"
                variant="outlined"
                density="compact"
                hide-details
                rounded="0"
                v-maska:form.amount.masked=amount_options
            >
                <template #append-inner>
                    <span class="button-currency-text">{{ currency.toUpperCase() }}</span>
                </template>
            </v-text-field>

            <div class="blf__percents">
                <v-btn :input-value="form.amount === '25'" variant="text" size="small" outlined tile @click="setAmount(25)">
                    <span>25 %</span>
                </v-btn>
                <v-btn :input-value="form.amount === '50'" variant="text" size="small" outlined tile @click="setAmount(50)">
                    <span>50 %</span>
                </v-btn>
                <v-btn :input-value="form.amount === '75'" variant="text" size="small" outlined tile @click="setAmount(75)">
                    <span>75 %</span>
                </v-btn>
                <v-btn :input-value="form.amount === '100'" variant="text" size="small" outlined tile @click="setAmount(100)">
                    <span>100 %</span>
                </v-btn>
            </div>

            <v-text-field
                v-model="form.rate"
                ref="bid_limit_rate"
                :label="$t('table_header.rate_per') + ' ' + currency.toUpperCase()"
                type="text"
                variant="outlined"
                density="compact"
                rounded="0"
                hide-details
                v-maska:form.rate.masked=rate_options
            >
                <template #append-inner>
                    <span class="button-currency-text">{{ market.toUpperCase() }}</span>
                </template>
            </v-text-field>

            <div class="blf__volume">
                <v-text-field
                    v-model="volume"
                    ref="bid_limit_volume"
                    :label="$t('table_header.volume')"
                    type="text"
                    variant="outlined"
                    density="compact"
                    hide-details
                    rounded="0"
                    @keydown="validateNumber($event)"
                >
                    <template #append-inner>
                        <span class="button-currency-text">{{ currency.toUpperCase() }}</span>
                    </template>
                </v-text-field>
                <div class="blf__text-field-hint">
                    {{ $t('trading.order.pay_limit_buy', [fee_visible]) + ' ' + currency.toUpperCase() }}
                </div>
            </div>

            <div class="blf__margin" v-if="useMargin && marginAvailable">
                <v-select
                    v-model="form.offer"
                    :items="offers_select"
                    :label="$t('trading.order.offer')"
                    item-text="name"
                    item-value="id"
                    density="compact"
                    hide-details
                />
            </div>

            <div v-if="!useMargin && conditionalOrdersAvailable" class="blf__params">
                <v-text-field
                    v-model="form.sl_rate"
                    ref="bid_limit_sl_rate"
                    :label="$t('trading.order.sl_rate')"
                    :disabled="!additionalParamsEnabled"
                    type="text"
                    variant="outlined"
                    density="compact"
                    hide-details
                    rounded="0"
                    @keydown="validateNumber($event)"
                >
                    <template #append-inner>
							<span class="button-currency-text">
								{{ market.toUpperCase() }}
							</span>
                    </template>
                </v-text-field>

                <v-text-field
                    v-model="form.tp_rate"
                    style="margin-top: 6px"
                    ref="bid_limit_tp_rate"
                    :label="$t('trading.order.tp_rate')"
                    :disabled="!additionalParamsEnabled"
                    type="text"
                    variant="outlined"
                    density="compact"
                    hide-details
                    rounded="0"
                    @keydown="validateNumber($event)"
                >
                    <template #append-inner>
							<span class="button-currency-text">
								{{ market.toUpperCase() }}
							</span>
                    </template>
                </v-text-field>

                <v-text-field
                    v-model="form.ts_offset"
                    style="margin-top: 6px"
                    ref="bid_limit_ts_offset"
                    :label="$t('trading.order.ts_offset')"
                    :disabled="!additionalParamsEnabled"
                    type="text"
                    variant="outlined"
                    density="compact"
                    hide-details
                    rounded="0"
                    @keydown="validateNumber($event)"
                >
                    <template #append-inner>
                        <span class="button-currency-text">{{ market.toUpperCase() }}</span>
                    </template>
                </v-text-field>
            </div>
        </div>

        <div>
            <div class="blf__switch">
                <div v-if="marginTradingAvailable && marginAvailable" class="d-flex justify-start">
                    <v-switch
                        v-model="useMargin"
                        :label="$t('trading.order.use_margin')"
                        :disabled="!useMarginEnabled"
                        hide-details
                        left
                        density="compact"
                        inset
                    />
                </div>
                <div v-if="!useMargin && conditionalOrdersAvailable" class="d-flex justify-start">
                    <v-switch
                        v-model="additionalParamsEnabled"
                        :label="$t('trading.order.use_additional_params')"
                        hide-details
                        left
                        flat
                        :ripple="false"
                        density="compact"
                    />
                </div>
            </div>
            <div class="blf__footer mt-2">
                <TradingFormsConfirmDialog
                    order-type="limit"
                    action-type="buy"
                    :amount="form.amount"
                    :price="form.rate"
                    :currency="currency"
                    :market="market"
                    :is-additional-params="additionalParamsEnabled"
                    :stop-loss="form.sl_rate"
                    :take-profit="form.tp_rate"
                    :trailing-stop="form.ts_offset"
                    :is-leverage="useMargin"
                    :leverage-offer="selectedOffer"
                    :leverage-level="leverageLevel"
                    @confirm="sendBidLimit"
                >
                    <v-btn color="success" height="24" block @click="sendBidLimit">
                        {{ $t('trading.order.direction.buy') }}
                    </v-btn>
                </TradingFormsConfirmDialog>

                <div class="blf__footer__available text-center">
                    {{ $t('trading.order.available') }}
                    <span class="blf__footer__balance" @click="setAmount(100)">
						{{ formatWithScaleInAllCurrencies(balance, market) }}
					</span>
                    {{ market.toUpperCase() }}
                </div>
            </div>
        </div>
    </form>
</template>
<style scoped lang="scss">
.blf {
    display: flex;
    flex-flow: column;
    flex-grow: 1;
    justify-content: space-between;

    &__volume {

        margin: 6px 0;
    }

    &__percents {
        display: grid;
        grid-gap: 2px;
        grid-template-columns: repeat(4, 1fr);
        margin-top: 4px;
        margin-bottom: 4px;
    }

    &__text-field-hint {
        line-height: 1;
        font-size: 0.75rem;
        padding: 0 12px;
        opacity: 0.6;
    }

    &__margin {
        padding: 0 4px;
    }

    &__footer {
        &__available {
            font-size: 11px;
        }
        &__balance {
            cursor: pointer;
        }
    }
}
.v-field {
    ::v-deep &__input {
        min-height: 32px !important;
        padding-top: 4px;
        padding-bottom: 4px;
    }
}
</style>
