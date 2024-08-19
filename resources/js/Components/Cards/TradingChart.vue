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

const intervals = [
    { room: 1, range: '1m' },
    { room: 5, range: '5m' },
    { room: 15, range: '15m' },
    { room: 30, range: '30m' },
    { room: 60, range: '1h' },
    { room: 240, range: '4h' },
    { room: 1440, range: '1d' },
    { room: 10080, range: '1w' },
];

const store = useStore();

const hcInstance = ref(Highcharts);
let chartInFullscreen = ref(false);
let candle_period = ref('1m');
const valuesToDisplay = ref(97);
const maxCandles = ref(501);

let xData = props.ohlc;
let yData = props.volume;

const selectedPeriodObject = computed(() => {
    const period = candle_period.value;
    return _.find(intervals, item => {
        return item.range === period.toLowerCase();
    });
})

const candle_room = computed(() => {
    return selectedPeriodObject.value.room;
})

const candlePeriodIndex = computed(() => {
    return selectedPeriodObject.value.index;
})

const lastPoint = computed(() => {
    return store.getters['trading/lastPoint'];
});

watch(
    () => lastPoint.value,
    (val,prevVal) => {
        if (val.x > prevVal.x)
        {
            const shift = hcInstance.value.charts[0].series[0].options.data.length >= maxCandles.value;
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
            const graphsToProcess = ['main'];
            candle.series.forEach((item, i) => {
                const id = _.get(item, 'options.id', undefined);
                if (['main','highcharts-navigator-series'].indexOf(id) !== -1) {
                    candle.series[i].setData(candlesData, false, false, false);
                }
            });
            candle.series.forEach((item, i) => {
                const id = _.get(item, 'options.id', undefined);
                if (['volume'].indexOf(id) !== -1) {
                    candle.series[i].setData(volumeData, false, false, false);
                }
            });
            candle.redraw();
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
        buttons: [
            {
                type: 'minute',
                index: 0,
                room: 1,
                count: 97,
                text: '1m',
                events: {
                    click: e => {
                        candle_period.value = '1m';
                        store.commit('trading/setGraphPeriod', candle_period.value);
                        store.dispatch('trading/getGraphFromServer').then(resp => {
                            hcInstance.value.charts[0].series.forEach((item, i) => {
                                const id = _.get(item, 'options.id', undefined);
                                if (['main','highcharts-navigator-series'].indexOf(id) !== -1) {
                                    hcInstance.value.charts[0].series[i].setData(xData.value, false, false, false);
                                }
                            });
                            hcInstance.value.charts[0].series[1].setData(yData.value, false);
                            if (xData.value.length > valuesToDisplay.value)
                            {
                                hcInstance.value.charts[0].xAxis[0].setExtremes(xData.value[xData.value.length - valuesToDisplay.value - 1].x, xData.value[xData.value.length - 1].x);
                            }
                            else
                            {
                                hcInstance.value.charts[0].xAxis[0].setExtremes(xData.value[0].x, xData.value[xData.value.length - 1].x);
                            }
                        });
                        return false;
                    },
                },
            },
            {
                type: 'minute',
                index: 1,
                room: 5,
                count: 97 * 5,
                text: '5m',
                events: {
                    click: e => {
                        candle_period.value = '5m';
                        store.commit('trading/setGraphPeriod', candle_period.value);
                        store.dispatch('trading/getGraphFromServer').then(resp => {
                            hcInstance.value.charts[0].series.forEach((item, i) => {
                                const id = _.get(item, 'options.id', undefined);
                                if (['main','highcharts-navigator-series'].indexOf(id) !== -1) {
                                    hcInstance.value.charts[0].series[i].setData(xData.value, false, false, false);
                                }
                            });
                            if (xData.value.length > valuesToDisplay.value)
                            {
                                hcInstance.value.charts[0].xAxis[0].setExtremes(xData.value[xData.value.length - valuesToDisplay.value - 1].x, xData.value[xData.value.length - 1].x);
                            }
                            else
                            {
                                hcInstance.value.charts[0].xAxis[0].setExtremes(xData.value[0].x, xData.value[xData.value.length - 1].x);
                            }
                        });
                        return false;
                    },
                },
            },
            {
                type: 'minute',
                index: 2,
                room: 15,
                count: 97 * 15,
                text: '15m',
                events: {
                    click: () => {
                        candle_period.value = '15m';
                        return false;
                    },
                },
            },
            {
                type: 'minute',
                index: 3,
                room: 30,
                count: 97 * 30,
                text: '30m',
                events: {
                    click: () => {
                        candle_period.value = '30m';
                        return false;
                    },
                },
            },
            {
                type: 'hour',
                index: 4,
                room: 60,
                count: 97,
                text: '1h',
                events: {
                    click: () => {
                        candle_period.value = '1h';
                        return false;
                    },
                },
            },
            {
                type: 'hour',
                index: 5,
                room: 240,
                count: 97 * 4,
                text: '4h',
                events: {
                    click: () => {
                        candle_period.value = '4h';
                        return false;
                    },
                },
            },
            {
                type: 'day',
                index: 6,
                room: 1440,
                count: 97,
                text: '1d',
                events: {
                    click: () => {
                        candle_period.value = '1d';
                        return false;
                    },
                },
            },
            {
                type: 'day',
                index: 7,
                room: 10080,
                count: 97 * 7,
                text: '1w',
                events: {
                    click: () => {
                        candle_period.value = '1w';
                        return false;
                    },
                },
            },
        ],
        selected: 0,
        inputEnabled: false,
    },
    xAxis: {
        overscroll: candle_room.value * 60 * 1000, // изначально одна минута
        resize: {
            enabled: false,
        },
        range: valuesToDisplay.value * candle_room.value * 60 * 1000, // изначально данные показываем за 97 минут
        events: {
            setExtremes: _.debounce(e => {
                console.log(e.min);
                console.log(e.max);
                console.log(e.trigger);
                hcInstance.value.charts[0].redraw();
                hcInstance.value.charts[0].reflow();
            }, 100),
        },
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

/*const initGraphDataHandler = () => {
    const data = {
        xData: store.state.trading.candlesData,
        yData: store.state.trading.volumeData,
    };
    //const candle = this.$refs.candle;
    const graphsToProcess = ['main'];
    hcInstance.value.charts[0].series.forEach((item, i) => {
        const id = _.get(item, 'options.id', undefined);
        if (graphsToProcess.indexOf(id) !== -1) {
            hcInstance.value.charts[0].series[i].setData(data.xData, false, false, false);
        }
    });
    hcInstance.value.charts[0].series[1].setData(data.yData, false);
    const overscroll = Math.round(candle_room.value) * 60 * 1000;
    hcInstance.value.charts[0].xAxis[0].update({
        range: valuesToDisplay.value * candle_room.value * 60 * 1000,
        overscroll,
    });
    if (data.xData.length > valuesToDisplay.value)
        hcInstance.value.charts[0].xAxis[0].setExtremes(data.xData[data.xData.length - valuesToDisplay.value - 1].x, data.xData[data.xData.length - 1].x);
    else
        hcInstance.value.charts[0].xAxis[0].setExtremes(data.xData[0].x, data.xData[data.xData.length - 1].x);
    setTimeout(() => {
        hcInstance.value.charts[0].redraw();
        hcInstance.value.charts[0].reflow();
    }, 200);
};*/

onMounted(() => {
    store.commit('trading/setGraphPeriod', candle_period.value);
});

</script>
<template>
    <v-card class="pa-1">
        <chart :constructor-type="'stockChart'" :options="options" :highcharts="hcInstance" class="trading-chart" style="height: 100%; width: 100%; position: absolute" />
    </v-card>
</template>

