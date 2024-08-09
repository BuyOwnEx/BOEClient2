<script setup>
import CommonDialog from "@/Components/Dialogs/CommonDialog.vue";
import {computed} from "vue";
import i18n from '@/Translations/i18n.js'
const props = defineProps({
    orderType: {
        type: String,
        validator(value) {
            return ['limit', 'market'].indexOf(value) !== -1;
        },
    },
    actionType: {
        type: String,
        validator(value) {
            return ['buy', 'sell'].indexOf(value) !== -1;
        },
    },
    amount: {
        type: [Number, String],
        required: true,
    },
    price: {
        type: [Number, String],
        required: false,
        default: 0,
    },
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    },
    isAdditionalParams: {
        type: Boolean,
        default: false,
    },
    stopLoss: {
        type: [Number, String],
        default: null,
    },
    takeProfit: {
        type: [Number, String],
        default: null,
    },
    trailingStop: {
        type: [Number, String],
        default: null,
    },
    isLeverage: {
        type: Boolean,
        required: false,
        default: false,
    },
    leverageOffer: {
        type: Object,
        required: false,
        default: null,
    },
    leverageLevel: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(['confirm']);

const isMarginOrder = computed(() => {
    return props.isLeverage;
});

const isLimit = computed(() => {
    return props.orderType === 'limit';
});
const isMarket = computed(() => {
    return props.orderType === 'market';
});
const isBuy = computed(() => {
    return props.actionType === 'buy';
});
const isSell = computed(() => {
    return props.actionType === 'sell';
});

const isSetSL = computed(() => {
    return props.stopLoss !== '0' && props.stopLoss !== '';
});
const isSetTP = computed(() => {
    return props.takeProfit !== '0' && props.takeProfit !== '';
});
const isSetTS = computed(() => {
    return props.trailingStop !== '0' && props.trailingStop !== '';
});

const isAnyAdditionalParamExist = computed(() => {
    return isSetSL.value || isSetTP.value || isSetTS.value;
});
const isMultiplyAdditionalParams = computed(() => {
    const isFirstAndSecond = isSetSL.value && isSetTP.value;
    const isFirstAndThird = isSetSL.value && isSetTS.value;
    const isSecondAndThird = isSetTP.value && isSetTS.value;
    const isAll = isSetSL.value && isSetTP.value && isSetTS.value;
    return isFirstAndSecond || isFirstAndThird || isSecondAndThird || isAll;
});
const getTypeColor = computed(() => {
    if (isBuy.value) return 'success';
    else return 'error';
});
const buyLimitText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_limit_order', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
    });
});
const buyMarketText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_market_order', {
        amount: props.amount,
        currency: props.currency,
        market: props.market,
    });
});
const buyLimitParamsText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_limit_order_with_params', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
    });
});
const buyMarketParamsText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_market_order_with_params', {
        amount: props.amount,
        currency: props.currency,
        market: props.market,
    });
});
const buyStopLossText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_stop_loss_text', {
        stopLoss: props.stopLoss,
        market: props.market,
    });
});
const buyTakeProfitText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_take_profit_text', {
        takeProfit: props.takeProfit,
        market: props.market,
    });
});
const buyTrailingStopText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_trailing_stop_text', {
        trailingStop: props.trailingStop,
        market: props.market,
    });
});

const buyLimitLeverageText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_limit_leverage', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
        leverageLevel: props.leverageLevel,
        offerPercent: props.leverageOffer?.percent || 0,
        offerDays: props.leverageOffer?.days || 0,
    });
});
const buyMarketLeverageText = computed(() => {
    return i18n.global.t('trading.forms.dialog.buy_market_leverage', {
        amount: props.amount,
        currency: props.currency,
        leverageLevel: props.leverageLevel,
        offerPercent: props.leverageOffer?.percent || 0,
        offerDays: props.leverageOffer?.days || 0,
    });
});
const sellLimitLeverageText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_limit_leverage', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
        leverageLevel: props.leverageLevel,
        offerPercent: props.leverageOffer?.percent || 0,
        offerDays: props.leverageOffer?.days || 0,
    });
});
const sellMarketLeverageText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_market_leverage', {
        amount: props.amount,
        currency: props.currency,
        leverageLevel: props.leverageLevel,
        offerPercent: props.leverageOffer?.percent,
        offerDays: props.leverageOffer?.days,
    });
});

const sellLimitText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_limit_order', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
    });
});
const sellMarketText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_market_order', {
        amount: props.amount,
        currency: props.currency,
        market: props.market,
    });
});
const sellLimitParamsText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_limit_order_with_params', {
        amount: props.amount,
        price: props.price,
        currency: props.currency,
        market: props.market,
    });
});
const sellMarketParamsText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_market_order_with_params', {
        amount: props.amount,
        currency: props.currency,
        market: props.market,
    });
});

const sellStopLossText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_stop_loss_text', {
        stopLoss: props.stopLoss,
        market: props.market,
    });
});
const sellTakeProfitText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_take_profit_text', {
        takeProfit: props.takeProfit,
        market: props.market,
    });
});
const sellTrailingStopText = computed(() => {
    return i18n.global.t('trading.forms.dialog.sell_trailing_stop_text', {
        trailingStop: props.trailingStop,
        market: props.market,
    });
});

const confirm = () => {
    emit('confirm');
};
</script>
<template>
    <CommonDialog :confirm-color="getTypeColor" :header-color="getTypeColor" @confirm="confirm">
        <template #default>
            <slot></slot>
        </template>

        <template #title>
            {{ $t('trading.forms.dialog.title') }}
        </template>

        <template #content>
            <div>
                <div v-if="!isMarginOrder">
                    <div v-if="isBuy && isLimit && !isAnyAdditionalParamExist" v-html="buyLimitText" />
                    <div v-else-if="isBuy && isMarket && !isAnyAdditionalParamExist" v-html="buyMarketText" />
                    <div v-else-if="isBuy && isLimit && isAnyAdditionalParamExist" v-html="buyLimitParamsText" />
                    <div v-else-if="isBuy && isMarket && isAnyAdditionalParamExist" v-html="buyMarketParamsText" />

                    <div v-if="isSell && isLimit && !isAnyAdditionalParamExist" v-html="sellLimitText" />
                    <div v-else-if="isSell && isMarket && !isAnyAdditionalParamExist" v-html="sellMarketText" />
                    <div v-else-if="isSell && isLimit && isAnyAdditionalParamExist" v-html="sellLimitParamsText" />
                    <div v-else-if="isSell && isMarket && isAnyAdditionalParamExist" v-html="sellMarketParamsText" />
                </div>

                <div v-else>
                    <div v-if="isBuy && isLimit" v-html="buyLimitLeverageText" />
                    <div v-if="isBuy && isMarket" v-html="buyMarketLeverageText" />

                    <div v-if="isSell && isLimit" v-html="sellLimitLeverageText" />
                    <div v-if="isSell && isMarket" v-html="sellMarketLeverageText" />
                </div>

                <ul v-if="isAnyAdditionalParamExist" class="trading-forms-confirm-dialog__add-params-list">
                    <li v-if="isBuy && isSetSL" v-html="buyStopLossText" />
                    <li v-if="isBuy && isSetTP" v-html="buyTakeProfitText" />
                    <li v-if="isBuy && isSetTS" v-html="buyTrailingStopText" />

                    <li v-if="isSell && isSetSL" v-html="sellStopLossText" />
                    <li v-if="isSell && isSetTP" v-html="sellTakeProfitText" />
                    <li v-if="isSell && isSetTS" v-html="sellTrailingStopText" />
                </ul>

                <small v-if="isMultiplyAdditionalParams" class="text-secondary pt-1">
                    * {{ $t('trading.forms.dialog.only_one_conditional_orders_trigger') }}
                </small>
            </div>
        </template>
    </CommonDialog>
</template>
<style scoped lang="sass">
.trading-forms-confirm-dialog
    &__add-params-list
        padding-top: 8px
        padding-left: 16px !important
</style>

