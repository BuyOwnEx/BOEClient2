<script setup>
import {useStore} from "vuex";
import {computed, ref} from "vue";
import _ from "lodash";
import {useRtl, useTheme} from "vuetify";
import { mdiChevronDown, mdiCancel, mdiInformationOutline } from '@mdi/js';
import i18n from '@/Translations/i18n.js';
import CommonDialog from "@/Components/Dialogs/CommonDialog.vue";
import moment from "moment";
import BigNumber from "bignumber.js";
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

const store = useStore();
const { isRtl } = useRtl()
const theme = useTheme()
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

const all_orders = computed(() => {
    return store.state.user.orders === null ? [{
        "id":282635474,
        "size":1.003457,
        "actual_size":1.003457,
        "price":49598.94,
        "status":"accepted",
        "type":"LIMIT",
        "side":false,
        "offset":null,
        "main_order_id":null,
        "currency":props.currency,
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635475,
        "size":1.1234,
        "actual_size":0.1,
        "price":3598.94,
        "status":"partiallyFilled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635476,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635477,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635478,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635479,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635480,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635481,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635482,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635483,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635484,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635485,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635486,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635487,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    },{
        "id":282635488,
        "size":1.1234,
        "actual_size":0,
        "price":3598.94,
        "status":"filled",
        "type":"LIMIT",
        "side":true,
        "offset":null,
        "main_order_id":null,
        "currency":"ETH",
        "market":props.market,
        "created_at":638591066849453700,
        "updated_at":638591066849453700
    }] : store.state.user.orders;
});

const orders = computed(() => {
    return show_all_pairs.value
        ? all_orders.value
        : _.filter(all_orders.value, {
            currency: props.currency.toUpperCase(),
            market: props.market.toUpperCase(),
        });
});

const isDark = computed(() => {
   return theme.global.current.value.dark
});

const close_payload = computed(() => {
    return {
        market: props.market.toUpperCase(),
        currency: props.currency.toUpperCase(),
        all_pairs: show_all_pairs.value,
    };
});

const orderCancel = (item) => {
    axios.post('/trader/ext/order/cancel', {order: item.id});
};

const orderCancelAll = () => {
    axios.post('/trader/ext/order/cancel_all', close_payload.value);
};
const orderCancelAllSL = () => {
    axios.post('/trader/ext/order/cancel_all_sl', close_payload.value);
};
const orderCancelAllTP = () => {
    axios.post('/trader/ext/order/cancel_all_tp', close_payload.value);
};
const orderCancelAllTS = () => {
    axios.post('/trader/ext/order/cancel_all_ts', close_payload.value);
};

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

const percent = (item) => {
    if (item.status === 'filled')
    {
        return 100;
    }
    else if (item.status === 'partiallyFilled')
    {
        return BigNumber(BigNumber(100).minus(BigNumber(item.actual_size).times(100).div(item.size)).toFixed(2,1)).toNumber();
    }
    else if (item.status === 'accepted') {
        return 0;
    }
    else return 0;
};

const get_percent_color = (item) => {
    if (item.status === 'filled')
    {
        return 'success';
    }
    else if (item.status === 'partiallyFilled')
    {
        return 'warning';
    }
    else if (item.status === 'accepted') {
        return 0;
    }
    else return 'error';
};

const cancel_options = computed(() => {
    return [
        {
            id: 1,
            text: i18n.global.t('trading.order.cancel_all'),
            type: '',
            link: '/trader/ext/order/cancel_all',
            click: () => orderCancelAll(),
        },
        {
            id: 2,
            text: i18n.global.t('trading.order.cancel_by_type', { type: 'SL' }),
            type: 'SL',
            link: '/trader/ext/order/cancel_all_sl',
            click: () => orderCancelAllSL(),
        },
        {
            id: 3,
            text: i18n.global.t('trading.order.cancel_by_type', { type: 'TP' }),
            type: 'TP',
            link: '/trader/ext/order/cancel_all_tp',
            click: () => orderCancelAllTP(),
        },
        {
            id: 4,
            text: i18n.global.t('trading.order.cancel_by_type', { type: 'TS' }),
            type: 'TS',
            link: '/trader/ext/order/cancel_all_ts',
            click: () => orderCancelAllTS(),
        },
    ];
});

const headers = computed(() => {
    return [
        { title: '', key: 'action', align: 'start', sortable: false, maxWidth: '40px' },
        { title: i18n.global.t('table_header.market'), key: 'pair', sortable: false, nowrap: true },
        { title: i18n.global.t('table_header.type'), key: 'side' },
        { title: i18n.global.t('table_header.params'), align: 'start', key: 'params', sortable: false },
        { title: i18n.global.t('table_header.status'), align: 'center', key: 'percent' },
        { title: i18n.global.t('table_header.date'), align: 'end', sortable: true, key: 'created_at' },
    ];
});

const sort_by = ref([{ key: 'created_at', order: 'desc' }])

</script>
<template>
    <v-data-table
        v-model:sort-by="sort_by"
        :headers="headers"
        :items="orders"
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
                <v-menu transition="slide-y-transition" content-class="small-text-menu" location="bottom">
                    <template #activator="{ props }">
                        <v-btn
                            v-bind="props"
                            :class="[isRtl.value ? 'ml-2' : 'mr-2']"
                            color="primary"
                            variant="text"
                            tile
                            size="small"
                            style="font-size: 11px"
                        >
                            {{ $t('common.cancel') }}
                            <v-icon end color="primary" size="small">{{ mdiChevronDown }}</v-icon>
                        </v-btn>
                    </template>

                    <v-list density="compact">
                        <CommonDialog v-for="item in cancel_options" :header-color="'error'" :confirm-color="'error'" :key="item.id" @confirm="item.click()">
                            <template #default>
                                <v-list-item link>
                                    <v-list-item-title style="font-size: 11px;">{{ item.text }}</v-list-item-title>
                                </v-list-item>
                            </template>
                            <template #title>
                                {{ $t('trading.order.cancel_title') }}
                            </template>
                            <template #content>
                                {{ $t('trading.order.cancel_many', { type: item.type }) }}
                            </template>
                        </CommonDialog>
                    </v-list>
                </v-menu>
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
            <span v-if="item.side === false">
                <strong class="text-success">
                    {{ $t('trading.order.direction.buy') }}
                </strong>
            </span>
            <span v-else>
                <strong class="text-error">
                    {{ $t('trading.order.direction.sell') }}
                </strong>
            </span>
            <span v-if="item.type === 'STOPLOSS'" class="own-active-order-list__type own-active-order-list__type--sl">SL</span>
            <span v-if="item.type === 'TAKEPROFIT'" class="own-active-order-list own-active-order-list__type--tp">TP</span>
            <span v-if="item.type === 'TRAILINGSTOP'" class="own-active-order-list own-active-order-list__type--ts">TS</span>
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
                                <span class="mr-2">{{ $t('table_header.status') }}:</span>
                                <span :class="isDark ? 'text-green-darken-4' : 'text-green-lighten-1'" v-if="item.status === 'accepted'"> {{ $t('trading.order.status.accepted') }}</span>
                                <span class="text-warning" v-else>{{ $t('trading.order.status.partiallyFilled') }}</span>
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

        <template #item.percent="{ item }">
            <v-tooltip location="top">
                <template v-slot:activator="{ props }">
                    <v-progress-linear
                        v-bind="props"
                        :buffer-value="percent(item)"
                        :buffer-color="get_percent_color(item)"
                        :buffer-opacity="isDark ? 0.5 : 0.9"
                        bg-color="grey"
                        height="10"
                    >
                        <template v-slot:default>
                            <strong class="pt-1 pb-1">{{ percent(item) }} %</strong>
                        </template>
                    </v-progress-linear>
                </template>
                <template v-slot:default>
                    <div class="d-flex flex-column" style="font-size: 0.645rem">
                        <span class="d-flex justify-space-between text-no-wrap">
                            <span class="mr-2">{{ $t('table_header.status') }}:</span>
                            <span :class="isDark ? 'text-green-darken-4' : 'text-green-lighten-1'" v-if="item.status === 'accepted'"> {{ $t('trading.order.status.accepted') }}</span>
                            <span :class="isDark ? 'text-green-darken-4' : 'text-green-lighten-1'" v-else-if="item.status === 'filled'"> {{ $t('trading.order.status.filled') }}</span>
                            <span class="text-warning" v-else>{{ $t('trading.order.status.partiallyFilled') }}</span>
                        </span>
                        <span class="d-flex justify-space-between text-no-wrap">
                            <span class="mr-2">{{ $t('table_header.actual_size') }}:</span>
                            <span><b>{{ format_with_scale(item.actual_size, findAmountScale(currency, market)) }}</b> {{ item.currency.toUpperCase() }}</span>
                        </span>
                        <span class="d-flex justify-space-between text-no-wrap">
                            <span class="mr-2">{{ $t('table_header.actual_volume') }}:</span>
                            <span><b>{{ get_volume(item.actual_size, item.price) }}</b> {{ item.market.toUpperCase() }}</span>
                        </span>
                    </div>

                </template>
            </v-tooltip>
        </template>

        <template #item.action="{ item }">
            <CommonDialog @confirm="orderCancel(item)" :header-color="'error'" :confirm-color="'error'">
                <template #default>
                    <v-tooltip :text="$t('common.cancel')" location="top">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props" color="error" size="small" style="cursor: pointer">{{ mdiCancel }}</v-icon>
                        </template>
                    </v-tooltip>
                </template>
                <template #title>
                    {{ $t('trading.order.cancel_title') }}
                </template>
                <template #content>
                    {{ $t('trading.order.cancel_one') }}
                </template>
            </CommonDialog>
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
