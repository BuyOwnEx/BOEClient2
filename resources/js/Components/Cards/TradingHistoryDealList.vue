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
            <div class="trading-history-deal-list__table-header pb-1 mb-1 pt-2">

                <div class="trading-history-deal-list-table-header__item--rate">
                    {{ $t('table_header.rate') }} ({{ market }})
                </div>

                <div class="trading-history-deal-list-table-header__item--amount">
                    {{ $t('table_header.amount') }} ({{ currency }})
                </div>

                <div class="trading-history-deal-list-table-header__item--date text-end">
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
    &__header
        margin-left: 4px
        font-size: 17px !important
</style>
