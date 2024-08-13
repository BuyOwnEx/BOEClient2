<script setup>
import {useStore} from "vuex";
import {computed, ref} from "vue";
import _ from "lodash";
import {useRtl} from "vuetify";
import { mdiChevronDown } from '@mdi/js';
import i18n from '@/Translations/i18n.js';
import CommonDialog from "@/Components/Dialogs/CommonDialog.vue";
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
const items_per_page = ref(-1);

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
        "actual_size":1.1,
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
        return BigNumber(100).toFixed(2).toString() + ' %';
    }
    else if (item.status === 'partiallyFilled')
    {
        return BigNumber(100).minus(BigNumber(item.actual_size).times(100).div(item.size)).toFixed(2).toString() + ' %';
    }
    else if (item.status === 'accepted') {
        return BigNumber(0).toFixed(2).toString() + ' %';
    }
    else return '-';
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
        { title: i18n.global.t('table_header.date'), align: 'start', sortable: true, key: 'date' },
        { title: i18n.global.t('table_header.market'), key: 'pair', sortable: false },
        { title: i18n.global.t('table_header.type'), key: 'side' },
        { title: i18n.global.t('table_header.amount'), key: 'size', sortable: false },
        { title: i18n.global.t('table_header.rate'), key: 'price', sortable: false },
        { title: i18n.global.t('table_header.volume'), key: 'volume', sortable: false },
        { title: i18n.global.t('table_header.filled'), key: 'percent' },
        { title: i18n.global.t('table_header.status'), key: 'status', sortable: false },
        { title: i18n.global.t('table_header.actions'), key: 'action', align: 'center', sortable: false },
    ];
});

</script>
<template>
    <v-data-table
        :headers="headers"
        :items="orders"
        :items-per-page="items_per_page"
        :items-per-page-options="[]"
        density="compact"
        :header-props="{height: '30px', style: 'font-size: 0.625rem'}"
        height="100%"
        style="height: 100%"
    >
        <template v-slot:top>
            <div class="d-flex justify-space-between">
                <v-switch
                    v-model="show_all_pairs"
                    :class="[isRtl.value ? 'mr-4' : 'ml-4']"
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
                            :class="[isRtl.value ? 'mr-auto' : 'ml-auto']"
                            color="primary"
                            variant="text"
                            tile
                            size="small"
                        >
                            {{ $t('common.cancel') }}
                            <v-icon end color="primary" size="small">{{ mdiChevronDown }}</v-icon>
                        </v-btn>
                    </template>

                    <v-list density="compact">
                        <CommonDialog v-for="item in cancel_options" :key="item.id" @confirm="item.click()">
                            <template #default>
                                <v-list-item link>
                                    <v-list-item-title>{{ item.text }}</v-list-item-title>
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
        <template #item.date="{ item }">
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

            <span v-if="item.type === 'STOPLOSS'" class="own-active-order-list__type own-active-order-list__type--sl">
						SL
					</span>

            <span v-if="item.type === 'TAKEPROFIT'" class="own-active-order-list own-active-order-list__type--tp">
						TP
					</span>

            <span v-if="item.type === 'TRAILINGSTOP'" class="own-active-order-list own-active-order-list__type--ts">
						TS
					</span>
        </template>

        <template #item.size="{ item }">
            {{ format_with_scale(item.size, findAmountScale(currency, market)) }}
            {{ item.currency.toUpperCase() }}
        </template>

        <template #item.price="{ item }">
            {{ format_with_scale(item.price, findRateScale(currency, market)) }}
            {{ item.market.toUpperCase() }}
        </template>

        <template #item.volume="{ item }">
            {{ get_volume(item.size, item.price) }}
            {{ item.market.toUpperCase() }}
        </template>

        <template #item.percent="{ item }"> {{ percent(item) }} </template>

        <template #item.status="{ item }">
					<span class="text-success" v-if="item.status === 'accepted'">
						{{ $t('trading.order.status.accepted') }}
					</span>
            <span class="text-warning" v-else>
						{{ $t('trading.order.status.partiallyFilled') }}
					</span>
        </template>

        <template #item.action="{ item }">
            <CommonDialog @confirm="orderCancel(item)">
                <template #default>
                    <v-btn color="primary" variant="outlined" tile size="x-small">
                        {{ $t('common.cancel') }}
                    </v-btn>
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

<style scoped>

</style>
