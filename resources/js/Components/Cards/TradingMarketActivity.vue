<script setup>
import {computed} from "vue";
import { useStore } from 'vuex';
import { mdiArrowUp, mdiArrowDown } from '@mdi/js';
import _ from "lodash";
import BigNumber from "bignumber.js";
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

const store = useStore();

const tickersList = computed(() => {
    return _.map(store.state.tickers.tickersList, item => {
        let change = BigNumber(item.latest).minus(BigNumber(item.previous_day));
        let changePercent = 0;
        if (!change.isZero()) {
            if (BigNumber(item.previous_day).isZero()) {
                changePercent = 100;
            } else {
                changePercent = change
                    .multipliedBy(100)
                    .div(BigNumber(item.previous_day))
                    .dp(2)
                    .toNumber();
            }
        }
        return {
            market: item.market,
            currency: item.currency,
            trend: change > 0 ? 'positive' : change < 0 ? 'negative' : 0,
            change: change.dp(item.rate_scale).toNumber(),
            changePercent: changePercent,
        };
    });
});

const activityList = computed(() => {
    const positive = _.chain(tickersList.value)
        .filter(item => {
            return item.trend === 'positive' || item.trend === 0;
        })
        .sortBy(item => {
            return item.changePercent * -1;
        })
        .slice(0, 3)
        .map((item, key) => {
            item['stars'] = 3 - key;
            return item;
        })
        .value();
    const negative = _.chain(tickersList.value)
        .filter(item => {
            return item.trend === 'negative';
        })
        .sortBy(item => {
            return item.changePercent;
        })
        .slice(0, 3)
        .map((item, key) => {
            item['stars'] = 3 - key;
            return item;
        })
        .value();
    return _.union(positive, negative);
});

</script>
<template>
    <v-card class="trading-market-activity pa-2">
        <v-card-title class="trading-market-activity__header component-title pa-0">
            {{ $t('trading.headers.market_activity') }}
        </v-card-title>
        <v-card-text class="trading-market-activity__content pa-0 mt-1">
            <v-table density="compact">
                <template #wrapper>
                    <tbody>
                    <tr v-for="item in activityList" :key="item.id">
                        <td class="trading-market-activity__item--pair pa-1">{{ item.currency }}/{{ item.market }}</td>

                        <td class="trading-market-activity__item--percent pa-1" :class="{'text-success': item.trend === 'positive','text-error': item.trend === 'negative'}">
                            <span> {{ item.changePercent }}% </span>
                        </td>

                        <td class="trading-market-activity__trend text-center pa-1">
                            <v-icon v-if="item.trend === 'positive'" color="success" size="small">
                                {{ mdiArrowUp }}
                            </v-icon>
                            <v-icon v-else-if="item.trend === 'negative'" color="error" size="small">
                                {{ mdiArrowDown }}
                            </v-icon>
                            <span v-else> — </span>
                        </td>
                    </tr>

                    <tr v-if="activityList.length === 0">
                        <td colspan="5" class="text-center">
                            {{ $t('trading.no_activity') }}
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-table>
        </v-card-text>
    </v-card>
</template>
<style scoped lang="sass">
.v-table
    font-size: 11px !important
.trading-market-activity
    display: flex
    flex-grow: 1
    flex-flow: column
    &__header
        margin-left: 4px
        font-size: 17px !important
    &__content
        display: flex
        flex-grow: 1
        flex-flow: column
    &__trend
        width: 1%
</style>
