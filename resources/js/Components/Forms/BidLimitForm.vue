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
const volume = ref('0');
const useMargin = ref(false);
const offers = ref([{id:1, percent: 1.5, days: 15, amount: 100000}]);
const leverageLevel = ref(5);

const best_ask = computed(() => {
    return store.state.trading.best_ask;
});

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

const form = ref({
    type: 'LIMIT',
    currency: props.currency.toUpperCase(),
    market: props.market.toUpperCase(),
    side: 0,
    amount: '0',
    rate: BigNumber(best_ask.value).toString(),
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

const activeTicker = computed(() => {
    return store.getters['trading/activeTicker'];
});

const amountScale = computed(() => {
    return parseInt(props.pair.amountScale);
});

const rateScale = computed(() => {
    return parseInt(props.pair.rateScale);
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
        if(BigNumber(form.value.rate).lt(BigNumber(best_ask.value)))
        {
            if(own_fees.value !== null && own_fees.value !== undefined)
            {
                return BigNumber(own_fees.value.maker_fee).div(100);
            }
            else return BigNumber(props.pair.makerFee).div(100);
        }
        else
        {
            if(own_fees.value !== null && own_fees.value !== undefined)
            {
                return BigNumber(own_fees.value.taker_fee).div(100);
            }
            else return BigNumber(props.pair.takerFee).div(100);
        }
    }
    else
    {
        if(BigNumber(form.value.rate).lt(BigNumber(best_ask.value)))
        {
            return BigNumber(props.pair.makerFee).div(100);
        }
        else
        {
            return BigNumber(props.pair.takerFee).div(100);
        }
    }
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
        return BigNumber(110).dp(scale, 1);
    } else return BigNumber(0);
});

const isAdditionalParams = computed(() => {
    return additionalParamsEnabled.value;
});

const useMarginEnabled = computed(() => {
    return Number(form.value.amount) && Number(form.value.rate);
});

const useCondOrdersEnabled = computed(() => {
    return Number(form.value.amount) && Number(form.value.rate);
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

const setAmount = () => {
    if (form.value.rate !== '' && form.value.rate !== '0') {
        form.value.amount = balance.value
            .div(BigNumber(form.value.rate))
            .div(100)
            .times(percent.value)
            .decimalPlaces(amountScale.value, 1)
            .toString();
        volume.value = BigNumber(form.value.rate)
            .times(form.value.amount)
            .decimalPlaces(amountScale.value + rateScale.value, 1)
            .toString();
        fee_amount.value = BigNumber(form.value.amount)
            .times(fee_currency.value)
            .decimalPlaces(props.pair.amountScale, 1)
            .toString();

    } else {
        form.value.amount = '0';
        volume.value = '0';
        fee_amount.value = '0';
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
const unmaskedRate = ref('0')
const unmaskedVolume = ref('0')
const unmaskedSL = ref('0')
const unmaskedTP = ref('0')
const unmaskedTS = ref('0')

defineExpose({ unmaskedAmount, unmaskedRate, unmaskedVolume, unmaskedSL, unmaskedTP, unmaskedTS });

const updateVolumeAmountChanged = (event) => {
    if (!useMarginEnabled.value) useMargin.value = false;
    if(event.detail.masked !== '')
    {
        volume.value = BigNumber(event.detail.masked)
            .times(BigNumber(form.value.rate))
            .decimalPlaces(props.pair.amountScale+props.pair.rateScale, 1)
            .toString();
        percent.value = BigNumber(event.detail.masked)
            .times(100)
            .times(BigNumber(form.value.rate))
            .div(balance.value)
            .decimalPlaces(0, 1)
            .toNumber();
        fee_amount.value = BigNumber(event.detail.masked)
            .times(fee_currency.value)
            .decimalPlaces(props.pair.amountScale, 1)
            .toString();
    }
    else {
        volume.value = '0';
        percent.value = 0;
        fee_amount.value = '0';
    }
}
const updateVolumeRateChanged = (event) => {
    if (!useMarginEnabled.value) useMargin.value = false;
    if(event.detail.masked !== '')
    {
        volume.value = BigNumber(event.detail.masked)
            .times(BigNumber(form.value.amount))
            .decimalPlaces(props.pair.amountScale+props.pair.rateScale, 1)
            .toString();
        percent.value = BigNumber(event.detail.masked)
            .times(100)
            .times(BigNumber(form.value.amount))
            .div(balance.value)
            .decimalPlaces(0, 1)
            .toNumber();
    }
    else {
        volume.value = '0';
        percent.value = 0;
    }
}
const updateAmountVolumeChanged = (event) => {
    if (!useMarginEnabled.value) useMargin.value = false;
    if(event.detail.masked !== '')
    {
        form.value.amount = BigNumber(event.detail.masked)
            .div(form.value.rate)
            .decimalPlaces(props.pair.amountScale, 1)
            .toString();
        percent.value =
            BigNumber(form.value.rate)
                .times(100)
                .times(BigNumber(form.value.amount))
                .div(balance.value)
                .decimalPlaces(0, 1)
                .toNumber();
        fee_amount.value = BigNumber(form.value.amount)
            .times(fee_currency.value)
            .decimalPlaces(props.pair.amountScale, 1)
            .toString();
    }
    else {
        form.value.amount = '0';
        percent.value = 0;
        fee_amount.value = '0';
    }
}

const resetOffer = () => {
    if(!useMargin.value) form.value.offer = null
};
</script>
<template>
    <form class="blf" @submit.prevent="sendBidLimit">
        <input name="type" type="hidden" value="LIMIT" />
        <input name="currency" type="hidden" :value="currency" />
        <input name="market" type="hidden" :value="market" />
        <input name="side" type="hidden" value="0" />

        <div class="d-flex flex-grow-0 flex-column">
            <div style="display: inline-block; position: relative; height: 100%">
                <Transition mode="out-in" name="slide-left" style="position: relative; height: 100%; width: 100%">
                    <div style="position: absolute; height: 100%; width: 100%" v-if="!additionalParamsEnabled && !useMargin">
                        <v-text-field
                            v-model="form.rate"
                            ref="bid_limit_rate"
                            :label="$t('table_header.rate_per') + ' ' + currency.toUpperCase()"
                            type="text"
                            variant="outlined"
                            density="compact"
                            rounded="0"
                            hide-details
                            v-maska:unmaskedRate.masked=rate_options
                            @maska="updateVolumeRateChanged"
                        >
                            <template #append-inner>
                                <span class="button-currency-text">{{ market.toUpperCase() }}</span>
                            </template>
                        </v-text-field>

                        <v-text-field
                            v-model="form.amount"
                            ref="bid_limit_amount"
                            :label="$t('table_header.amount')"
                            type="text"
                            variant="outlined"
                            density="compact"
                            hide-details
                            rounded="0"
                            class="mt-2"
                            v-maska:unmaskedAmount.masked=amount_options
                            @maska="updateVolumeAmountChanged"
                        >
                            <template #append-inner>
                                <span class="button-currency-text">{{ currency.toUpperCase() }}</span>
                            </template>
                        </v-text-field>

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
                            <v-text-field
                                v-model="volume"
                                ref="bid_limit_volume"
                                :label="$t('table_header.volume')"
                                type="text"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="0"
                                v-maska:unmaskedVolume.masked=volume_options
                                @maska="updateAmountVolumeChanged"
                            >
                                <template #append-inner>
                                    <span class="button-currency-text">{{ market.toUpperCase() }}</span>
                                </template>
                            </v-text-field>
                            <div class="blf__text-field-hint">
                                {{ $t('table_header.fee') }}: {{ fee_visible }}% ~ {{ fee_amount }} {{ currency.toUpperCase() }}
                            </div>
                        </div>
                    </div>
                    <div style="position: absolute; height: 100%; width: 100%" v-else-if="additionalParamsEnabled && !useMargin">
                        <div v-if="!useMargin && conditionalOrdersAvailable && additionalParamsEnabled" class="blf__params">
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
                                ref="bid_limit_tp_rate"
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
                                ref="bid_limit_ts_offset"
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

        <div>
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

                <div class="blf__footer__available text-center mt-1">
                    {{ $t('trading.order.available') }}
                    <span class="blf__footer__balance">
						{{ formatWithScaleInAllCurrencies(balance, market) }}
					</span>
                    {{ market.toUpperCase() }}
                </div>
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
::v-deep(.v-field__input) {
    min-height: 32px !important;
    padding-top: 4px;
    padding-bottom: 4px;
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
