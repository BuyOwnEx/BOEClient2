<script setup>
import {useStore} from "vuex";
import {computed, ref} from "vue";
import _ from "lodash";
import {useRtl} from "vuetify";
import { mdiInformationOutline } from '@mdi/js';
import i18n from '@/Translations/i18n.js';
import moment from "moment";
import BigNumber from "bignumber.js";
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

const store = useStore();
const { isRtl } = useRtl()
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

const show_all_pairs = ref(false);
const items_per_page = ref(10);

const all_deals = computed(() => {
    return store.state.user.deals === null ? [{
        "id":9460373,
        "size":20,
        "price":1.483e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586619025162800
    },{
        "id":9460374,
        "size":10,
        "price":0.8101,
        "side":false,
        "currency":"SFP",
        "market":"USDT",
        "created_at":638586617255506000
    },{
        "id":9460375,
        "size":119,
        "price":1.48e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586616883503900
    },{
        "id":9460376,
        "size":881,
        "price":1.48e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586616687407200
    },{
        "id":9460377,
        "size":665.2,
        "price":1.477e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586613816673000
    },{
        "id":9460378,
        "size":0.036538,
        "price":55185.53,
        "side":true,
        "currency":"BTC",
        "market":"USDT",
        "created_at":638586590243659100
    },{
        "id":9460379,
        "size":0.000235,
        "price":55185.53,
        "side":true,
        "currency":"BTC",
        "market":"USDT",
        "created_at":638586590243606500
    },{
        "id":9460380,
        "size":20,
        "price":1.483e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586619025162800
    },{
        "id":9460381,
        "size":10,
        "price":0.8101,
        "side":false,
        "currency":"SFP",
        "market":"USDT",
        "created_at":638586617255506000
    },{
        "id":9460382,
        "size":119,
        "price":1.48e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586616883503900
    },{
        "id":9460383,
        "size":881,
        "price":1.48e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586616687407200
    },{
        "id":9460384,
        "size":665.2,
        "price":1.477e-5,
        "side":true,
        "currency":"SFP",
        "market":"BTC",
        "created_at":638586613816673000
    },{
        "id":9460385,
        "size":0.036538,
        "price":55185.53,
        "side":true,
        "currency":"BTC",
        "market":"USDT",
        "created_at":638586590243659100
    },{
        "id":9460386,
        "size":0.000235,
        "price":55185.53,
        "side":true,
        "currency":"BTC",
        "market":"USDT",
        "created_at":638586590243606500
    }] : store.state.user.deals;
});

const deals = computed(() => {
    return show_all_pairs.value
        ? all_deals.value
        : _.filter(all_deals.value, {
            currency: props.currency.toUpperCase(),
            market: props.market.toUpperCase(),
        });
});

const format_date = (date) => {
    return moment.utc((date / 10000 - 62135596800000)).utcOffset(moment().utcOffset()).format('YYYY-MM-DD HH:mm:ss')
};

const tickers = computed(() => {
    return store.state.tickers.tickersList;
});

const findAmountScale = (currency, market) => {
    let index = _.findIndex(tickers.value, item => {
        return (
            item.market.toUpperCase() === market.toUpperCase() &&
            item.currency.toUpperCase() === currency.toUpperCase()
        );
    });
    if (index > -1) return tickers.value[index].amount_scale;
    else return 0;
};

const findRateScale = (currency, market) => {
    let index = _.findIndex(tickers.value, item => {
        return (
            item.market.toUpperCase() === market.toUpperCase() &&
            item.currency.toUpperCase() === currency.toUpperCase()
        );
    });
    if (index > -1) return tickers.value[index].rate_scale;
    else return 0;
};

const format_with_scale = (value, scale) => {
    return BigNumber(value).toFixed(scale, 1).toString();
};

const get_volume = (size, price) => {
    return BigNumber(price)
        .times(BigNumber(size))
        .toString();
};

const headers = computed(() => {
    return [
        { title: i18n.global.t('table_header.date'), align: 'start', sortable: true, key: 'created_at' },
        { title: i18n.global.t('table_header.market'), align: 'start', key: 'pair', sortable: true, nowrap: true },
        { title: i18n.global.t('table_header.type'), key: 'side' },
        { title: i18n.global.t('table_header.params'), align: 'center', key: 'params', sortable: false },
        { title: i18n.global.t('table_header.volume'), align: 'end', key: 'volume', sortable: false, nowrap: true },
    ];
});

const sort_by = ref([{ key: 'created_at', order: 'desc' }])

</script>
<template>
    <v-data-table
        v-model:sort-by="sort_by"
        :headers="headers"
        :items="deals"
        :items-per-page="items_per_page"
        :items-per-page-options="[]"
        density="compact"
        :header-props="{height: '30px', style: 'font-size: 0.625rem; padding: 0 8px'}"
        :row-props="{style: 'font-size: 0.625rem'}"
        :cell-props="{style: 'padding: 0 8px'}"
        height="100%"
        style="height: 100%"
    >
        <template v-slot:top>
            <div class="d-flex justify-space-between align-center">
                <v-switch
                    v-model="show_all_pairs"
                    :class="[isRtl.value ? 'mr-2' : 'ml-2']"
                    :label="$t('trading.show_other_pairs')"
                    hide-details
                    left
                    flat
                    :ripple="false"
                    density="compact"
                    color="primary"
                />
                <v-spacer></v-spacer>
            </div>
        </template>

        <template #item.created_at="{ item }">
            <span class="table-date">
                {{ format_date(item.created_at) }}
            </span>
        </template>

        <template #item.pair="{ item }">
            <span> {{ item.currency.toUpperCase() }}/{{ item.market.toUpperCase() }} </span>
        </template>

        <template #item.side="{ item }">
            <span v-if="item.side === true">
                <strong class="text-success">
                    {{ $t('trading.order.direction.buy') }}
                </strong>
            </span>
            <span v-else>
                <strong class="text-error">
                    {{ $t('trading.order.direction.sell') }}
                </strong>
            </span>
        </template>

        <template #item.params="{ item }">
            <div class="d-flex align-center">
                <v-tooltip location="top">
                    <template v-slot:activator="{ props }">
                        <v-icon v-bind="props" color="primary" size="small">{{mdiInformationOutline}}</v-icon>
                    </template>
                    <template v-slot:default>
                        <div class="d-flex flex-column" style="font-size: 0.645rem">
                            <span class="d-flex justify-space-between text-no-wrap">
                                <span class="mr-2">{{ $t('table_header.date') }}:</span>
                                <span>{{ format_date(item.created_at) }}</span>
                            </span>
                            <span class="d-flex justify-space-between text-no-wrap">
                                <span class="mr-2">{{ $t('table_header.amount') }}:</span>
                                <span><b>{{ format_with_scale(item.size, findAmountScale(currency, market)) }}</b> {{ item.currency.toUpperCase() }}</span>
                            </span>
                            <span class="d-flex justify-space-between text-no-wrap">
                                <span class="mr-2">{{ $t('table_header.rate') }}:</span>
                                <span><b>{{ format_with_scale(item.price, findRateScale(currency, market)) }}</b> {{ item.market.toUpperCase() }}</span>
                            </span>
                            <span class="d-flex justify-space-between text-no-wrap">
                                <span class="mr-2">{{ $t('table_header.volume') }}:</span>
                                <span><b>{{ get_volume(item.size, item.price) }}</b> {{ item.market.toUpperCase() }}</span>
                            </span>
                        </div>
                    </template>
                </v-tooltip>
                <div class="d-flex flex-column ml-2">
                    <span class="text-no-wrap"><span>{{ $t('table_header.amount') }}: </span>{{ format_with_scale(item.size, findAmountScale(currency, market)) }} {{ item.currency.toUpperCase() }}</span>
                    <span class="text-no-wrap"><span>{{ $t('table_header.rate') }}: </span>{{ format_with_scale(item.price, findRateScale(currency, market)) }} {{ item.market.toUpperCase() }}</span>
                </div>
            </div>
        </template>

        <template #item.volume="{ item }">
            <span><b>{{ get_volume(item.size, item.price) }}</b> {{ item.market.toUpperCase() }}</span>
        </template>
    </v-data-table>
</template>

<style scoped lang="scss">
.v-switch {
    ::v-deep(.v-label) {
        font-size: 0.6875rem;
    }
}
::v-deep(.v-data-table-footer) {
    padding: 2px;
    line-height: 15px;
    font-size: 11px;
}
::v-deep(.v-data-table-footer__items-per-page) {
    display: none;
}

::v-deep(.v-pagination) {
    .v-btn {
        height: 24px;
        width: 24px;
    }
}
</style>

