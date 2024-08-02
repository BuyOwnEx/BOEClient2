<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import TradingTickersList from "@/Components/Cards/TradingTickersList.vue";
import TradingChart from "@/Components/Cards/TradingChart.vue";
import TradingHistoryDealList from "@/Components/Cards/TradingHistoryDealList.vue";
import TradingChat from "@/Components/Cards/TradingChat.vue";
import TradingAskList from "@/Components/Cards/TradingAskList.vue";
import TradingForms from "@/Components/Cards/TradingForms.vue";
import TradingBidList from "@/Components/Cards/TradingBidList.vue";
import TradingMarketActivity from "@/Components/Cards/TradingMarketActivity.vue";
import TradingOwnLists from "@/Components/Cards/TradingOwnLists.vue";
import { useDisplay } from "vuetify";
import {computed, ref} from "vue";
import { useStore } from 'vuex';
import TradingMarketInfo from "@/Components/Cards/TradingMarketInfo.vue";
import { mdiChartBar, mdiTable, mdiBook, mdiForum, mdiChartBox, mdiFormSelect, mdiViewList } from '@mdi/js'
import TradingAskBidLastPrice from "@/Components/Cards/TradingAskBidLastPrice.vue";
import BigNumber from 'bignumber.js';
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });

const props = defineProps({
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    },
    tickers: {
        type: Array,
        required: true,
    },
    graph: {
        type: Array,
        required: true,
    },
    all_currencies: {
        type: Array,
        required: true,
    },
});
let { width, mobile } = useDisplay();

const showActivity = computed(() => {
    return width.value >= 1769;
})
const showChat = computed(() => {
    return width.value >= 1383;
})
const showHistory = computed(() => {
    return width.value >= 1383;
})
let selectedTab = ref(0);

const store = useStore();

let ohlc = [];
let volumes = [];

for (let i = 0; i < props.graph.length; i++) {
    ohlc.push({
        x: parseInt(props.graph[i].x),
        open: BigNumber(props.graph[i].open).toNumber(),
        high: BigNumber(props.graph[i].high).toNumber(),
        low: BigNumber(props.graph[i].low).toNumber(),
        close: BigNumber(props.graph[i].close).toNumber(),
    });
    volumes.push({
        x: parseInt(props.graph[i].x),
        y: BigNumber(props.graph[i].volume).toNumber(),
    });
}
ohlc = ohlc.sort((a, b) => {
    return a.x - b.x;
});
volumes = volumes.sort((a, b) => {
    return a.x - b.x;
});

store.commit('tickers/setTickersList', props.tickers);
store.commit('trading/setAllCurrenciesList', props.all_currencies);
store.commit('trading/setGraphData', {candlesData: ohlc, volumeData: volumes});

</script>

<template>
    <Head :title="currency+'/'+market" />

    <MainLayout>
        <div v-if="!mobile" class="trading__desktop" :class="[$page.props.auth.user && $page.props.auth.user.email_verified_at ? 'logged' : '']">
            <TradingTickersList class="trading__desktop__tickers" :currency="currency" :market="market" />

            <TradingMarketInfo class="trading__desktop__info" :currency="currency" :market="market" />

            <TradingChart class="trading__desktop__chart" :currency="currency" :market="market" />

            <TradingHistoryDealList v-if="showHistory" class="trading__desktop__history" :market="market" :currency="currency" />

            <TradingChat v-if="showChat" class="trading__desktop__chat" />

            <TradingAskList class="trading__desktop__ask-list" :currency="currency" :market="market" />

            <TradingForms class="trading__desktop__forms" :currency="currency" :market="market" />

            <TradingBidList class="trading__desktop__bid-list" :currency="currency" :market="market" />

            <TradingMarketActivity v-if="showActivity" class="trading__desktop__market-activity" />

            <TradingOwnLists
                v-if="$page.props.auth.user && $page.props.auth.user.email_verified_at"
                class="trading__desktop__own-lists-tabs-wrapper"
                :currency="currency"
                :market="market"
            />
        </div>
        <div v-else class="trading__mobile">
            <v-bottom-navigation class="mt-1" style="position: relative" v-model="selectedTab" mode="shift" grow color="primary" base-color="on-surface">
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiChartBar }}</v-icon>
                    <span>{{ $t('trading.headers.chart') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiTable }}</v-icon>
                    <span>{{ $t('trading.headers.tickers') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiBook }}</v-icon>
                    <span>{{ $t('trading.headers.history') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiForum }}</v-icon>
                    <span>{{ $t('trading.headers.chat') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiChartBox }}</v-icon>
                    <span>{{ $t('trading.headers.orders') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small">
                    <v-icon>{{ mdiFormSelect }}</v-icon>
                    <span>{{ $t('trading.headers.forms') }}</span>
                </v-btn>
                <v-btn class="pl-1 pr-1" size="x-small" v-if="$page.props.auth.user && $page.props.auth.user.email_verified_at">
                    <v-icon>{{ mdiViewList }}</v-icon>
                    <span>{{ $t('trading.headers.lists') }}</span>
                </v-btn>
            </v-bottom-navigation>

            <TradingChart
                v-show="selectedTab === 0"
                class="trading__mobile__chart"
                :currency="currency"
                :market="market"
            />
            <TradingTickersList
                v-if="selectedTab === 1"
                class="trading__mobile__tickers"
                :currency="currency"
                :market="market"
            />

            <TradingHistoryDealList
                v-if="selectedTab === 2"
                class="trading__mobile__history"
                :market="market"
                :currency="currency"
            />

            <TradingChat
                v-if="selectedTab === 3"
                class="trading__mobile__chat"
            />

            <div
                v-if="selectedTab === 4"
                class="trading__mobile__orders d-flex flex-column"
            >
                <TradingBidList
                    class="trading__mobile__orders__bid flex-grow-1"
                    :currency="currency"
                    :market="market"
                />
                <TradingAskBidLastPrice
                    class="trading__mobile__orders__last-price"
                    :market="market"
                />
                <TradingAskList
                    class="trading__mobile__orders__ask flex-grow-1"
                    :currency="currency"
                    :market="market"
                />
            </div>

            <TradingForms
                v-if="selectedTab === 5"
                class="trading__mobile__forms"
                :currency="currency"
                :market="market"
            />

            <TradingOwnLists
                v-if="selectedTab === 6 && ($page.props.auth.user && $page.props.auth.user.email_verified_at)"
                class="trading__mobile__own-lists-tabs-wrapper"
                :currency="currency"
                :market="market"
            />
        </div>
    </MainLayout>
</template>
<style scoped lang="scss">
$grid-height: calc(100vh - 64px - 40px - 8px); // 64px - header, 40px - footer, 8px - (pa-1 + grid-gap)
.trading {
    &__desktop {
        width: 100%;
        height: $grid-height;
        display: grid;
        grid-template-areas:
            'tickers info info info info history'
			'tickers chart chart chart chart history'
			'chat bid forms forms ask history'
			'chat bid forms forms ask history'
			'activity bid forms forms ask history';
        grid-gap: 4px;
        grid-template-columns: 325px 1fr 1fr 1fr 1fr 325px;
        grid-template-rows: 40px 408px 2fr 1fr 195px;

        &__tickers {
            grid-area: tickers;
            min-width: 325px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        &__info {
            grid-area: info;
            max-height: 40px;
        }

        &__chart {
            grid-area: chart;
        }

        &__history {
            grid-area: history;
            min-width: 325px;
        }

        &__chat {
            grid-area: chat;
            min-width: 325px;
        }

        &__market-activity {
            grid-area: activity;
        }

        &__ask-list {
            grid-area: ask;
        }

        &__forms {
            grid-area: forms;
        }

        &__bid-list {
            grid-area: bid;
        }

        &__own-lists-tabs-wrapper {
            grid-area: own;
        }
    }
    &__desktop.logged {
        grid-template-areas:
            'tickers info info info info history'
			'tickers chart chart chart chart history'
			'chat bid forms forms ask history'
			'chat bid forms forms ask history'
			'activity bid forms forms ask history'
			'own own own own own own';
        grid-template-rows: 40px 5fr 2fr 1fr 2fr 1fr;
    }

    &__mobile {
        display: flex;
        flex-grow: 1;
        flex-flow: column-reverse;

        &__chart {
            height: 100%;
        }

        &__tickers {
            height: 100%;
        }

        &__orders {

            height: 100%;
        }

        &__history {
            height: 100%;
        }

        &__chat {
            height: 100%;
        }

        &__forms {
            height: 100%;
        }
    }
}
@media screen and (min-width: 1382px) and (max-width: 1768px) {
    .trading {
        &__desktop {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            grid-template-areas:
                'info info info info info info info info info history history history tickers tickers tickers'
				'chart chart chart chart chart chart chart chart chart history history history tickers tickers tickers'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
                'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat';
            grid-template-rows: 40px 30fr 6fr 6fr 6fr 6fr 6fr;

            &__tickers {
                min-width: unset;
            }
            &__history {
                min-width: unset;
            }
            &__chat {
                min-width: unset;
            }
        }
        &__desktop.logged {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            grid-template-areas:
				'info info info info info info info info info history history history tickers tickers tickers'
				'chart chart chart chart chart chart chart chart chart history history history tickers tickers tickers'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
				'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
                'forms forms forms forms forms forms bid bid bid ask ask ask chat chat chat'
                'own own own own own own own own own own own own own own own';
            grid-template-rows: 40px 30fr 6fr 6fr 6fr 6fr 6fr 6fr;

            &__tickers {
                min-width: unset;
            }
            &__history {
                min-width: unset;
            }
            &__chat {
                min-width: unset;
            }
        }
    }
}
@media screen and (max-width: 1382px) {
    .trading {
        &__desktop {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            grid-template-areas:
				'info info info info info info info info info info info info tickers tickers tickers tickers'
                'chart chart chart chart chart chart chart chart chart chart chart chart tickers tickers tickers tickers'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask';
            grid-template-rows: 40px 4fr 2fr 2fr;

            &__tickers {
                min-width: unset;
            }
            &__history {
                min-width: unset;
            }
            &__chat {
                min-width: unset;
            }
        }
        &__desktop.logged {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            grid-template-areas:
				'info info info info info info info info info info info info tickers tickers tickers tickers'
                'chart chart chart chart chart chart chart chart chart chart chart chart tickers tickers tickers tickers'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
				'forms forms forms forms forms forms forms forms bid bid bid bid ask ask ask ask'
                'own own own own own own own own own own own own own own own own';
            grid-template-rows: 40px 4fr 2fr 2fr 1fr;

            &__tickers {
                min-width: unset;
            }
            &__history {
                min-width: unset;
            }
            &__chat {
                min-width: unset;
            }
        }
    }
}
</style>
