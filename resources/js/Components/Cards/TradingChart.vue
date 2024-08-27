<script setup>
import _ from "lodash";
import i18n from '@/Translations/i18n.js'
import BigNumber from 'bignumber.js';
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

import { Chart } from 'highcharts-vue';
import Highcharts from 'highcharts';
import loadStock from 'highcharts/modules/stock';
import indicators from 'highcharts/indicators/indicators-all';
import dragPanes from 'highcharts/modules/drag-panes';

import annotationsAdvanced from 'highcharts/modules/annotations-advanced';
import priceIndicator from 'highcharts/modules/price-indicator';
import fullScreen from 'highcharts/modules/full-screen';
import exportingInit from 'highcharts/modules/exporting';
import stockTools from 'highcharts/modules/stock-tools';

import highchartsTheme from 'highcharts/themes/grid-light';
import {computed, onMounted, ref, watch} from "vue";
import {useStore} from "vuex";

loadStock(Highcharts);
indicators(Highcharts);
dragPanes(Highcharts);
annotationsAdvanced(Highcharts);
priceIndicator(Highcharts);
fullScreen(Highcharts);
exportingInit(Highcharts);
stockTools(Highcharts);
highchartsTheme(Highcharts);

const props = defineProps({
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    },
    ohlc: {
        type: Array,
        required: true
    },
    volume: {
        type: Array,
        required: true
    }
});

const store = useStore();

const hcInstance = ref(Highcharts);
let chartInFullscreen = ref(false);
let candle_period = '1m';
const valuesToDisplay = 97;
const maxCandles = 501;
const graph_loading = ref(false);

let xData = props.ohlc;
let yData = props.volume;

const lastPoint = computed(() => {
    return store.state.trading.last_point;
});

const changePeriodEvent = (period) => {
    graph_loading.value = true;
    store.commit('trading/setGraphPeriod', period);
    store.dispatch('trading/getGraphFromServer').then(resp => {
        xData = resp.candlesData;
        yData = resp.volumeData;
        hcInstance.value.charts[0].series.forEach((item, i) => {
            const id = _.get(item, 'options.id');
            if (['main','highcharts-navigator-series'].indexOf(id) !== -1) {
                hcInstance.value.charts[0].series[i].setData(xData, false, false, false);
            }
        });
        hcInstance.value.charts[0].series[1].setData(yData, false);
        if (xData.length > valuesToDisplay)
        {
            hcInstance.value.charts[0].xAxis[0].setExtremes(xData[xData.length - valuesToDisplay - 1].x, xData[xData.length - 1].x);
        }
        else
        {
            hcInstance.value.charts[0].xAxis[0].setExtremes(xData[0].x, xData[xData.length - 1].x);
        }
        graph_loading.value = false;
    });
    return false;
};

const intervals = [
    { index: 0, room: 1, text: '1m', type: 'minute', count: valuesToDisplay, events: { click: () => changePeriodEvent('1m') }},
    { index: 1, room: 5, text: '5m', type: 'minute', count: valuesToDisplay * 5, events: { click: () => changePeriodEvent('5m') }},
    { index: 2, room: 15, text: '15m', type: 'minute', count: valuesToDisplay * 15, events: { click: () => changePeriodEvent('15m') }},
    { index: 3, room: 30, text: '30m', type: 'minute', count: valuesToDisplay * 30, events: { click: () => changePeriodEvent('30m') }},
    { index: 4, room: 60, text: '1h', type: 'hour', count: valuesToDisplay, events: { click: () => changePeriodEvent('1h') }},
    { index: 5, room: 240, text: '4h', type: 'hour', count: valuesToDisplay * 4, events: { click: () => changePeriodEvent('4h') }},
    { index: 6, room: 1440, text: '1d', type: 'day', count: valuesToDisplay, events: { click: () => changePeriodEvent('1d') }},
    { index: 7, room: 10080, text: '1w', type: 'day', count: valuesToDisplay * 7, events: { click: () => changePeriodEvent('1w') }},
];

watch(
    () => lastPoint.value,
    (val,prevVal) => {
        console.log('graph_loading', graph_loading.value);
        console.log('val.x', val.x);
        console.log('prevVal.x', prevVal.x);
        console.log('val.y', val.y);
        console.log('prevVal.y', prevVal.y);
        if(!graph_loading.value && val !== null)
        {
            if (val.x > prevVal.x)
            {
                const shift = hcInstance.value.charts[0].series[0].options.data.length >= maxCandles;
                hcInstance.value.charts[0].series[0].addPoint({
                    x: parseInt(val.x),
                    open: val.close,
                    high: val.close,
                    low: val.close,
                    close: val.close,
                }, false, shift, true);
                hcInstance.value.charts[0].series[1].addPoint({
                    x: parseInt(val.x),
                    y: 0,
                }, true, shift, true);
            }
            else {
                if(val.x === prevVal.x && val.y === prevVal.y && val.close === prevVal.close) console.log('VAL = PREV_VAL');
                const candle = hcInstance.value.charts[0];
                const candlesData = candle.series[0].options.data;
                const volumeData = candle.series[1].options.data;
                candlesData[candlesData.length - 1] = {
                    x: parseInt(val.x),
                    open: BigNumber(val.open).toNumber(),
                    high: BigNumber(val.high).toNumber(),
                    low: BigNumber(val.low).toNumber(),
                    close: BigNumber(val.close).toNumber(),
                };
                volumeData[volumeData.length - 1] = {
                    x: parseInt(val.x),
                    y: BigNumber(val.y).toNumber(),
                };
                candle.series.forEach((item, i) => {
                    const id = _.get(item, 'options.id');
                    if (['main','highcharts-navigator-series'].indexOf(id) !== -1) {
                        candle.series[i].setData(candlesData, false, false, false);
                    }
                });
                candle.series.forEach((item, i) => {
                    const id = _.get(item, 'options.id');
                    if (['volume'].indexOf(id) !== -1) {
                        candle.series[i].setData(volumeData, false, false, false);
                    }
                });
                candle.redraw();
            }
        }
    }
)

let options = {
    time: {
        timezoneOffset: new Date().getTimezoneOffset()
    },
    chart: {
        style: {
            fontFamily: '"Quicksand", "Roboto", sans-serif',
            letterSpacing: '0.0071428571em',
            fontSize: '11px',
        },
        zooming: {
            mouseWheel: false
        },
        spacing: [10, 10, 10, 10],
        events: {
            load() {
                console.log('Chart was loaded!');
                //this.redraw();
            },
        },
    },
    credits: {
        enabled: false,
    },
    navigator: {
        height: 25,
        margin: 2,
        maskFill: 'rgba(102,133,194,0.3)',
        handles: {
            enabled: true,
        },
        enabled: true,
    },
    scrollbar: {
        enabled: false,
        height: 1 / 1,
        barBackgroundColor: 'transparent',
        barBorderColor: 'transparent',
        buttonArrowColor: 'transparent',
        buttonBackgroundColor: 'transparent',
        buttonBorderColor: 'transparent',
    },
    rangeSelector: {
        allButtonsEnabled: true,
        enabled: true,
        buttonTheme: {
            fill: '#f0f0f0',
            states: {
                select: {
                    fill: '#dedede',
                }
            }
        },
        buttons: intervals,
        selected: 0,
        inputEnabled: false,
    },
    xAxis: {
        overscroll: 60 * 1000, // изначально одна минута
        resize: {
            enabled: false,
        },
        range: valuesToDisplay * 60 * 1000, // изначально данные показываем за 97 минут
    },
    yAxis: [
        {
            labels: {
                align: 'right',
                x: -3,
                formatter() {
                    return BigNumber(this.value).toString();
                },
            },
            title: {
                text: i18n.global.t('table_header.rate') + ', ' + props.market,
            },
            height: '75%',
            lineWidth: 1,
            floor: 0,
            offset: 35,
            resize: {
                enabled: true,
                lineWidth: 1,
            },
        },
        {
            id: 'volume',
            labels: {
                align: 'right',
                x: -3,
            },
            title: {
                text: i18n.global.t('table_header.volume') + ', ' + props.currency,
            },
            top: '75%',
            height: '25%',
            offset: 35,
            floor: 0,
            lineWidth: 1,
        },
    ],
    tooltip: {
        split: true,
    },
    series: [
        {
            type: 'candlestick',
            id: 'main',
            name: i18n.global.t('table_header.rate') + ', ' + props.market,
            data: xData,
            yAxis: 0,
            xAxis: 0,
            color: '#ef476f',
            lineColor: '#ef476f',
            upColor: '#06d6a0',
            upLineColor: '#06d6a0',
            tooltip: {
                pointFormatter() {
                    return (
                        '<span style="color:' +
                        this.color +
                        '">●</span> <b> ' +
                        this.series.name +
                        '</b><br/>Open: ' +
                        BigNumber(this.open).toString() +
                        '<br/>High: ' +
                        BigNumber(this.high).toString() +
                        '<br/>Low: ' +
                        BigNumber(this.low).toString() +
                        '<br/>Close: ' +
                        BigNumber(this.close).toString() +
                        '<br/>'
                    );
                },
            },
        },
        {
            type: 'column',
            id: 'volume',
            linkedTo: 'main',
            name: i18n.global.t('table_header.volume') + ', ' + props.currency,
            data: yData,
            yAxis: 1
        },
    ],
    navigation: {
        annotationsOptions: {
            controlPointOptions: {
                style: {
                    strokeWidth: 1
                }
            }
        },
        buttonOptions: {
            height: 34,
            width: 38,
            symbolSize: 20,
            symbolX: 19,
            symbolY: 17,
            symbolStrokeWidth: 2,
            x: -15,
        },
    },
    stockTools: {
        gui: {
            definitions: {
                typeChange: {
                    items: ['typeCandlestick', 'typeLine', 'typeOHLC'],
                },
            },
            iconsURL: '/resources/js/assets/images/highcharts/',
        },
    },
};

onMounted(() => {
    store.commit('trading/setGraphPeriod', candle_period);
});

</script>
<template>
    <v-card class="pa-1">
        <chart :constructor-type="'stockChart'" :options="options" :highcharts="hcInstance" class="trading-chart" style="height: 100%; width: 100%; position: absolute" />
    </v-card>
</template>

