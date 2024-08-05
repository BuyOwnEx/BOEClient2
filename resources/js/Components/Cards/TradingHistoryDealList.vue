<script setup>
import {computed} from "vue";
import _ from "lodash";
import { useStore } from 'vuex';
import BigNumber from "bignumber.js";
import moment from "moment";
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
    },
    pair: {
        type: Object,
        required: true,
    },
});

const historyData = computed(() => {
    return _.map(store.state.trading.history_deal_list, item => {
        return {
            side: item.side,
            price: BigNumber(item.price).toFixed(props.pair.rateScale, 1).toString(),
            size: BigNumber(item.size).toFixed(props.pair.amountScale, 1).toString(),
            date: moment.utc((item.created_at / 10000 - 62135596800000)).utcOffset(moment().utcOffset()).format('YYYY-MM-DD HH:mm:ss')
        };
    });
});
</script>
<template>
    <v-card class="trading-history-deal-list pa-2">
        <v-card-title class="trading-history-deal-list__header component-title pa-0">
            {{ $t('trading.headers.history_deal_list') }}
        </v-card-title>
        <v-card-text class="trading-history-deal-list__content pa-0">
            <div class="trading-history-deal-list__table-header pb-1 mb-1 pt-1">

                <div class="trading-history-deal-list__header-item--rate">
                    {{ $t('table_header.rate') }} ({{ market }})
                </div>

                <div class="trading-history-deal-list__header-item--amount">
                    {{ $t('table_header.amount') }} ({{ currency }})
                </div>

                <div class="trading-history-deal-list__header-item--date">
                    {{ $t('table_header.date') }}
                </div>
            </div>
            <div class="trading-history-deal-list__table-body mt-1">
                <div class="trading-history-deal-list__table-body-item" v-if="historyData.length > 0" v-for="item in historyData" :key="item.id">
                    <div class="trading-history-deal-list__body-item--price text-start">
                        <strong :class="{'text-error': item.side === true, 'text-success': item.side === false}">
                            {{ item.price }}
                        </strong>
                    </div>
                    <div class="trading-history-deal-list__body-item--size text-start">
                        {{ item.size }}
                    </div>
                    <div class="trading-history-deal-list__body-item--date text-end">
                        {{ item.date }}
                    </div>
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>
<style scoped lang="sass">
.trading-history-deal-list
    display: flex
    flex-flow: column
    flex-grow: 1
    &__header
        font-size: 17px !important
        display: flex
        justify-content: space-between
        align-items: center

    &__table-header
        font-size: 0.7rem !important
        font-weight: bold
        display: grid
        grid-template-areas: 'price size date'
        grid-template-columns: minmax(90px, 1fr) minmax(80px, 1fr) minmax(100px, 1fr)
        border-bottom: 1px solid gray

    &__header-item--rate
        padding: 4px
        grid-area: price
        text-transform: uppercase
        white-space: nowrap

    &__header-item--amount
        padding: 4px
        grid-area: size
        text-transform: uppercase
        white-space: nowrap

    &__header-item--date
        font-size: 0.8rem
        padding: 4px
        grid-area: date
        text-align: end
        text-transform: uppercase
        white-space: nowrap

    &__table-body
        display: flex
        flex-flow: column
        overflow-y: auto

    &__table-body-item
        font-size: 0.7rem !important
        display: grid
        grid-template-areas: 'price size date'
        grid-template-columns: minmax(90px, 1fr) minmax(80px, 1fr) minmax(100px, 1fr)

    &__body-item--price
        padding: 4px
        grid-area: price
        white-space: nowrap

    &__body-item--size
        padding: 4px
        grid-area: size
        white-space: nowrap

    &__body-item--date
        font-weight: normal
        font-size: 0.65rem !important
        color: rgb(var(--v-theme-secondary)) !important
        padding: 4px
        grid-area: date
        white-space: nowrap
</style>
