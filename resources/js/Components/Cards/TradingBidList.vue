<script setup>
import TradingOrdersTooltip from "@/Components/Tooltips/TradingOrdersTooltip.vue";
import TradingOrderFill from "@/Components/Cards/TradingOrderFill.vue";
import {computed} from "vue";
import { useStore } from 'vuex';
import _ from "lodash";
import BigNumber from "bignumber.js";
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

const bidList = computed(() => {
    return _.map(store.state.trading.bid_list, item => {
        return {
            side: item.side,
            price: BigNumber(item.price).toFixed(props.pair.rateScale, 1).toString(),
            size: BigNumber(item.actual_size).toFixed(props.pair.amountScale, 1).toString(),
            volume: BigNumber(item.price).times(BigNumber(item.actual_size)).toString()
        };
    });
});
</script>
<template>
    <v-card class="trading-bid-list pa-2">
        <v-card-title class="trading-bid-list__header component-title pa-0">
            {{ $t('trading.headers.bid_orders') }}
        </v-card-title>

        <v-card-text class="trading-bid-list__content pa-0">
            <div class="trading-bid-list-table-absolute-wrapper">
                <div class="trading-bid-list__content__table">
                    <div class="trading-bid-list__table-header pb-1 mb-1 pt-1">
                        <div class="trading-bid-list__header-item--price">
                            {{ $t('table_header.rate') }} ({{ market }})
                        </div>

                        <div class="trading-bid-list__header-item--size">
                            {{ $t('table_header.amount') }} ({{ currency }})
                        </div>

                        <div class="trading-bid-list__header-item--volume">
                            {{ $t('table_header.volume') }}
                        </div>
                    </div>
                    <div class="trading-bid-list__table-body mt-1">
                        <div class="trading-bid-list__table-body-item" v-if="bidList.length > 0" v-for="(item, itemIndex) in bidList" :key="item.id">
                            <div class="trading-bid-list__body-item--row">
                                <div class="trading-bid-list__body-item--price text-start">
                                    <strong class="text-success">
                                        {{ item.price }}
                                    </strong>
                                </div>
                                <div class="trading-bid-list__body-item--size text-start">
                                    {{ item.size }}
                                </div>
                                <div class="trading-bid-list__body-item--volume text-end">
                                    {{ item.volume }}
                                </div>
                            </div>
                            <TradingOrderFill :item-index="itemIndex" :volume="item.volume" type="bid" />
                        </div>
                    </div>
                </div>
            </div>
            <TradingOrdersTooltip />
        </v-card-text>
    </v-card>
</template>
<style scoped lang="sass">
.trading-bid-list
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
        grid-template-areas: 'price size volume'
        grid-template-columns: minmax(90px, 1fr) minmax(80px, 1fr) minmax(100px, 1fr)
        border-bottom: 1px solid gray

    &__header-item--price
        padding: 4px
        grid-area: price
        text-transform: uppercase
        white-space: nowrap

    &__header-item--size
        padding: 4px
        grid-area: size
        text-transform: uppercase
        white-space: nowrap

    &__header-item--volume
        font-size: 0.8rem
        padding: 4px
        grid-area: volume
        text-align: end
        text-transform: uppercase
        white-space: nowrap

    &__table-body
        display: flex
        flex-flow: column
        overflow-y: auto
        overflow-x: hidden

    &__table-body-item
        font-size: 0.7rem !important
        display: flex
        position: relative

    &__body-item--row
        display: grid
        grid-template-areas: 'price size volume'
        grid-template-columns: minmax(90px, 1fr) minmax(80px, 1fr) minmax(100px, 1fr)
        height: 100%
        width: 100%
        z-index: 2
        line-height: 20px

    &__body-item--price
        padding: 4px
        grid-area: price
        white-space: nowrap

    &__body-item--size
        padding: 4px
        grid-area: size
        white-space: nowrap

    &__body-item--volume
        font-weight: normal
        font-size: 0.65rem !important
        padding: 4px
        grid-area: volume
        white-space: nowrap
        position: relative
</style>
