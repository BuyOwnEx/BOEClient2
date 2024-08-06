<script setup>
import BigNumber from 'bignumber.js';
import {computed} from "vue";
import { useStore } from 'vuex';
import _ from "lodash";
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

const store = useStore();

const props = defineProps({
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    }
});

const ticker = computed(() => {
    return store.getters['trading/activeTicker'];
});

const lastPrice = computed(() => {
    return ticker.value ? BigNumber(ticker.value.latest).dp(ticker.value.scale).toString() : 0.0;
});

const dailyChange = computed(() => {
    return ticker.value ? BigNumber(ticker.value.latest).minus(ticker.value.previous_day) : BigNumber(0);
});

const dailyChangePercent = computed(() => {
    let changePercent = 0;
    if (!dailyChange.value.isZero()) {
        if (BigNumber(ticker.value.previous_day).isZero()) {
            changePercent = 100;
        } else {
            changePercent = dailyChange.value
                .multipliedBy(100)
                .div(BigNumber(ticker.value.previous_day))
                .toString();
        }
    }
    return BigNumber(changePercent).dp(2).toString();
});

const dailyHigh = computed(() => {
    return ticker.value ? BigNumber(ticker.value.max).dp(ticker.value.scale).toString() : 0;
});

const dailyLow = computed(() => {
    return ticker.value ? BigNumber(ticker.value.min).dp(ticker.value.scale).toString() : 0;
});

const dailyVolume = computed(() => {
    return ticker.value ? ticker.value.volume : 0;
});

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


</script>
<template>
    <v-card class="trading-market-info pa-2">
        <div class="d-flex justify-space-around" style="width: 100%">
            <div class="trading-market-info__last-price trading-market-info__item">
			<span class="info-value">
				<span>{{ lastPrice }}</span> <span class="currency">{{ market }}</span>
			</span>
                <span class="info-title">
				{{ $t('trading.last_price') }}
			</span>
            </div>

            <div class="trading-market-info__daily trading-market-info__item">
			<span
                class="info-value"
                :class="{'text-success': BigNumber(dailyChange).gt(0), 'text-error': BigNumber(dailyChange).lt(0)}"
            >
				<span>{{ dailyChangePercent }}%</span>
			</span>
                <span class="info-title">
				{{ $t('trading.daily_change') }}
			</span>
            </div>

            <div class="trading-market-info__daily-high trading-market-info__item">
			<span class="info-value">
				{{ dailyHigh }} <span class="currency">{{ market }}</span>
			</span>
                <span class="info-title">{{ $t('trading.daily_high') }}</span>
            </div>

            <div class="trading-market-info__daily-low trading-market-info__item">
			<span class="info-value">
				{{ dailyLow }} <span class="currency">{{ market }}</span>
			</span>
                <span class="info-title">{{ $t('trading.daily_low') }}</span>
            </div>

            <div class="trading-market-info__daily-volume trading-market-info__item">
			<span class="info-value">
				<span>{{ formatWithScaleInAllCurrencies(dailyVolume, market) }} </span> <span class="currency">{{ market }}</span>
			</span>
                <span class="info-title">
				{{ $t('trading.daily_volume') }}
			</span>
            </div>
        </div>
    </v-card>
</template>
<style scoped lang="sass">
.trading-market-info
    display: flex
    flex-flow: row

    &__item
        display: flex
        align-items: center
        justify-content: center
        flex-wrap: wrap
        padding-right: 12px

    .info-value
        font-weight: bold
        font-size: 1.1rem
        padding-right: 4px
    .info-title
        font-weight: normal
        font-size: 11px

@media screen and (min-width: 1500px) and (max-width: 1900px)
    .trading-market-info
        .info-value
            font-size: 14px
        .info-title
            font-size: 10px

@media screen and (min-width: 1382px) and (max-width: 1500px)
    .trading-market-info
        line-height: 15px

        .info-value
            font-size: 12px
        .info-title
            font-size: 9px

        &__item
            padding-right: 2px

@media screen and (min-width: 960px) and (max-width: 1382px)
    .trading-market-info
        .info-value
            font-size: 15px
        .info-title
            font-size: 11px

        &__item
            padding-right: 16px

@media screen and (max-width: 959px)
    .trading-market-info
        line-height: 15px

        .info-value
            font-size: 15px
        .info-title
            font-size: 10px

        &__item
            padding-right: 8px

@media screen and (max-width: 700px)
    .trading-market-info
        line-height: 15px

        .info-value
            font-size: 10px
        .info-title
            font-size: 9px

        &__item
            padding-right: 2px

.v-application--is-rtl
    .trading-market-info
        &__item
            padding-left: 12px
            padding-right: 0
        .info-value
            padding-left: 4px
            padding-right: 0

        @media screen and (min-width: 1382px) and (max-width: 1500px)
            padding-left: 2px
            padding-right: 0
        @media screen and (min-width: 960px) and (max-width: 1382px)
            padding-left: 16px
            padding-right: 0
        @media screen and (max-width: 959px)
            padding-left: 8px
            padding-right: 0
        @media screen and (max-width: 700px)
            padding-left: 2px
            padding-right: 0
</style>
