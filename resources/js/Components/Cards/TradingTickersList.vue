<script setup>
import {computed, onMounted, ref} from "vue";
import { Link } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { mdiMagnify, mdiClose, mdiStar, mdiArrowUp, mdiArrowDown } from '@mdi/js';
import _ from "lodash";
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
});

const isSearch = ref(false);
let tickersSearchQuery = ref('');
let tickersSorting = ref('volumeDesc');
let selectedMarket = ref(props.market);
let prevSelectedMarket = ref(props.market);
let favoritePairs = ref([]);
const store = useStore();

onMounted(() => {
    if (typeof Storage !== 'undefined') {
        const savedFavoriteTickers = localStorage.getItem('tickersFavorites');
        if (savedFavoriteTickers) favoritePairs.value = JSON.parse(savedFavoriteTickers);
        const sortType = localStorage.getItem('tickersSorting');
        if (sortType) tickersSorting.value = sortType;
    }
});

const allMarkets = computed(() => {
    return _.uniqBy(store.state.tickers.tickersList, 'market').map(obj => obj['market']);
});

const allFavorites = computed(() => {
    if (favoritePairs.value.length === 0) {
        return [];
    }
    return _.filter(tickersList.value, item => {
        return _.find(favoritePairs.value, pair => {
            return pair === item.pairName;
        });
    });
});

const tickersList = computed(() => {
    return _.map(store.state.tickers.tickersList, item => {
        let change = BigNumber(item.latest).minus(BigNumber(item.previous_day));
        let changePercent = 0;
        if (!change.isZero()) {
            if (BigNumber(item.previous_day).isZero()) {
                changePercent = 100;
            } else {
                changePercent = change.multipliedBy(100).div(BigNumber(item.previous_day));
            }
        }
        return {
            pairName: item.currency.toUpperCase() + '/' + item.market.toUpperCase(),
            market: item.market,
            currency: item.currency,
            margin: item.margin,
            volume: item.volume,
            latest: item.latest.toFixed(item.scale),
            change: change.toFixed(item.scale),
            changePercent: changePercent.toFixed(2),
        };
    });
});

const tickersListFiltered = computed(() => {
    if (selectedMarket.value === null) {
        return allMarkets.value;
    }
    if (tickersSearchQuery.value !== '' && selectedMarket.value !== 'favorites') {
        return _.filter(tickersList.value, item => {
            return item.pairName.toLowerCase().indexOf(tickersSearchQuery.value.toLowerCase()) !== -1;
        });
    }
    if (selectedMarket.value === 'favorites') {
        if (tickersSearchQuery.value !== '') {
            return _.filter(allFavorites.value, item => {
                return item.pairName.toLowerCase().indexOf(tickersSearchQuery.value.toLowerCase()) !== -1;
            });
        }
        return allFavorites.value;
    }
    return _.filter(tickersList.value, item => {
        return item.market === selectedMarket.value;
    });
})

const tickersListFilteredAndSorted = computed(() => {
    switch (tickersSorting.value) {
        case 'pairAsc':
            return _.orderBy(tickersListFiltered.value, ['pairName'], ['asc']);
        case 'pairDesc':
            return _.orderBy(tickersListFiltered.value, ['pairName'], ['desc']);
        case 'changeAsc':
            return _.sortBy(tickersListFiltered.value, item => parseFloat(item.changePercent));
        case 'changeDesc':
            return _.sortBy(tickersListFiltered.value, item => parseFloat(item.changePercent) * -1);
        case 'volumeAsc':
            return _.sortBy(tickersListFiltered.value, item => parseFloat(item.volume));
        case 'volumeDesc':
        default:
            return _.sortBy(tickersListFiltered.value, item => parseFloat(item.volume) * -1);
    }
});

const switchSearchShowAndClearField = () => {
    isSearch.value = !isSearch.value;
    tickersSearchQuery.value = '';
};

const switchFavoriteSorting = () => {
    const isSelectedFavorite = selectedMarket.value === 'favorites';
    if (isSelectedFavorite) selectedMarket.value = prevSelectedMarket.value;
    else selectedMarket.value = 'favorites';
};

const setSortingToLocalStorage = (sorting) => {
    localStorage.setItem('tickersSorting', sorting);
};

const setSorting = (type) => {
    const sort = tickersSorting.value;
    switch (type) {
        case 'pair':
            if (sort === 'pairAsc') {
                tickersSorting.value = 'pairDesc';
            } else if (sort === 'pairDesc') {
                tickersSorting.value = '';
            } else {
                tickersSorting.value = 'pairAsc';
            }
            setSortingToLocalStorage(tickersSorting.value);
            break;
        case 'change':
            if (sort === 'changeAsc') {
                tickersSorting.value = 'changeDesc';
            } else if (sort === 'changeDesc') {
                tickersSorting.value = '';
            } else {
                tickersSorting.value = 'changeAsc';
            }
            setSortingToLocalStorage(tickersSorting.value);
            break;
        case 'volume':
        default:
            if (sort === 'volumeAsc') {
                tickersSorting.value = 'volumeDesc';
            } else if (sort === 'volumeDesc') {
                tickersSorting.value = '';
            } else {
                tickersSorting.value = 'volumeAsc';
            }
            setSortingToLocalStorage(tickersSorting.value);
    }
};

const isPairFavorite = (pairName) => {
    const pairIndex = favoritePairs.value.indexOf(pairName);
    return pairIndex !== -1;
};

const addToFavorites = (pairName) => {
    const exist = _.find(favoritePairs.value, item => {
        return item.toLowerCase() === pairName.toLowerCase();
    });
    if (!exist) {
        favoritePairs.value.push(pairName);
        if (typeof Storage !== 'undefined') {
            localStorage.setItem('tickersFavorites', JSON.stringify(favoritePairs.value));
        }
    }
};

const removeFromFavorites = (pairName) => {
    const index = _.findIndex(favoritePairs.value, item => {
        return item.toLowerCase() === pairName.toLowerCase();
    });
    if (index !== -1) {
        favoritePairs.value.splice(index, 1);
        if (typeof Storage !== 'undefined') {
            localStorage.setItem('tickersFavorites', JSON.stringify(favoritePairs.value));
        }
    }
};

const getCurrencyOrPair = (item) => {
    return selectedMarket.value === 'favorites' || (isSearch.value && tickersSearchQuery.value !== '') ? item.pairName : item.currency;
};

const findCurrencyScale = (currency) => {
    const currencies = store.state.trading.all_currencies;
    const index = _.findIndex(currencies.data, item => {
        return item.currency.toUpperCase() === currency.toUpperCase();
    });
    if (index > -1) return currencies.data[index].scale;
};

const setSelectedMarket = (market) => {
    selectedMarket.value = market;
    prevSelectedMarket.value = market;
};

const formatWithScaleInAllCurrencies = (value, currency) => {
    const scale = findCurrencyScale(currency.toUpperCase());
    if (scale) {
        return BigNumber(value).toFixed(scale, 1);
    } else {
        return BigNumber(value).toFixed(2, 1);
    }
};

const getPercentColorClass = (percent) => {
    if (percent > 0) return 'text-success';
    else if (percent < 0) return 'text-error';
    else return '';
};

</script>
<template>
    <v-card class="trading-tickers-list pa-2">
        <div class="trading-tickers-list__header ml-1 mr-1">
            <div v-if="!isSearch" class="trading-tickers-list__header-markets">
                <v-btn
                    v-if="allMarkets.length !== null"
                    v-for="market in allMarkets"
                    class="mr-1"
                    :key="market"
                    :active="market === selectedMarket"
                    :input-value="selectedMarket === market"
                    variant="text"
                    size="small"
                    @click="setSelectedMarket(market)"
                >
                    <span>{{ market }}</span>
                </v-btn>
            </div>

            <div v-if="isSearch" class="trading-tickers-list__header-search">
                <v-text-field
                    v-model="tickersSearchQuery"
                    :label="$t('trading.search')"
                    hide-details
                    variant="underlined"
                    single-line
                    density="compact"
                    tile
                    autofocus
                >
                </v-text-field>
            </div>

            <div class="trading-tickers-list__header-actions">
				<span class="trading-tickers-list__search-action">
					<v-btn @click="switchSearchShowAndClearField" size="small" variant="text">
						<v-icon v-if="!isSearch">{{ mdiMagnify }}</v-icon>
                        <v-icon v-else>{{ mdiClose }}</v-icon>
					</v-btn>
				</span>
            </div>
        </div>
        <div class="trading-tickers-list__table-header pb-1 mb-1 pt-2">

            <div class="trading-tickers-list__header-item--favorite clickable" @click="switchFavoriteSorting">
                    <div class="trading-tickers-list__header-favorite-wrapper d-flex align-center">
                        <v-icon v-if="selectedMarket !== 'favorites'" color="#b0b29c">
                            {{ mdiStar }}
                        </v-icon>
                        <v-icon v-if="selectedMarket === 'favorites'" color="#FFE040">
                            {{ mdiStar }}
                        </v-icon>
                    </div>
            </div>

            <div class="trading-tickers-list__header-item--pair clickable" @click="setSorting('pair')">
                <div class="d-flex align-center">
                        <span>
                            {{ $t('table_header.market') }}
                        </span>
                    <v-icon v-if="tickersSorting === 'pairAsc'" size="x-small" class="ml-1">
                        {{ mdiArrowUp }}
                    </v-icon>
                    <v-icon v-if="tickersSorting === 'pairDesc'" size="x-small" class="ml-1">
                        {{ mdiArrowDown }}
                    </v-icon>
                </div>
            </div>

            <div class="trading-tickers-list__header-item--rate">
                {{ $t('table_header.rate') }}
            </div>

            <div class="trading-tickers-list__header-item--volume clickable" @click="setSorting('volume')">
                <div class="d-flex align-center">
                        <span>
                            {{ $t('trading.sort.volume') }}
                        </span>
                    <v-icon v-if="tickersSorting === 'volumeAsc'" size="x-small" class="ml-1">
                        {{ mdiArrowUp }}
                    </v-icon>
                    <v-icon v-if="tickersSorting === 'volumeDesc'" size="x-small" class="ml-1">
                        {{ mdiArrowDown }}
                    </v-icon>
                </div>
            </div>

            <div class="trading-tickers-list__header-item--change clickable text-end" @click="setSorting('change')">
                <div class="d-flex align-center justify-end">
                        <span>
                            {{ $t('trading.sort.change') }}
                        </span>
                    <v-icon v-if="tickersSorting === 'changeAsc'" size="x-small" class="ml-1">
                        {{ mdiArrowUp }}
                    </v-icon>
                    <v-icon v-if="tickersSorting === 'changeDesc'" size="x-small" class="ml-1">
                        {{ mdiArrowDown }}
                    </v-icon>
                </div>
            </div>
        </div>
        <div class="trading-tickers-list__table-body mt-1">
            <div class="trading-tickers-list__table-body-item" v-if="tickersListFilteredAndSorted.length > 0" v-for="item in tickersListFilteredAndSorted" :key="item.pairName">
                <div class="trading-tickers-list__body-item--favorite text-start">
                    <div class="trading-tickers-list__body-favorite-wrapper">
                        <v-icon
                            v-if="isPairFavorite(item.pairName)"
                            color="#FFE040"
                            small
                            @click="removeFromFavorites(item.pairName)"
                        >
                            {{ mdiStar }}
                        </v-icon>
                        <v-icon
                            v-if="!isPairFavorite(item.pairName)"
                            color="#b0b29c"
                            small
                            @click="addToFavorites(item.pairName)"
                        >
                            {{ mdiStar }}
                        </v-icon>
                    </div>
                </div>

                <div class="trading-tickers-list__body-item--pair text-start">
                    <Link class="trading-tickers-list__pair-link" :href="route('trading',{'currency': item.currency, 'market': item.market})">
                        <strong class="trading-tickers-list__pair-currency" :class="{ 'small-cell-text': selectedMarket === 'favorites' || (isSearch && tickersSearchQuery !== '') }">
                            {{ getCurrencyOrPair(item) }}
                        </strong>
                    </Link>
                </div>

                <div class="trading-tickers-list__body-item--cost text-start">
                    {{ BigNumber(item.latest).toString() }}
                </div>

                <div class="trading-tickers-list__body-item--volume text-start" :class="{ 'small-cell-text': selectedMarket === 'favorites' || (isSearch && tickersSearchQuery !== '')}">
                    {{ formatWithScaleInAllCurrencies(item.volume, item.market) }}
                </div>

                <div class="trading-tickers-list__body-item--change text-end" :class="[getPercentColorClass(item.changePercent)]">
                    <span>{{ item.changePercent }}</span> <span>%</span>
                </div>
            </div>

            <div v-else-if="tickersListFilteredAndSorted.length === 0" class="text-center mt-4 overline">
                {{ $t('trading.not_found') }}
            </div>
        </div>
    </v-card>
</template>
<style scoped lang="sass">
.trading-tickers-list
    display: flex
    flex-flow: column
    flex-grow: 1
    font-size: 0.7rem
    &__header
        display: flex
        justify-content: space-between
        align-items: center
        height: 32px

    &__header-search
        width: 100%
        padding-right: 8px

    &__header-actions
        display: flex
        align-items: center

    &__table-header
        font-weight: bold
        display: flex
        border-bottom: 1px solid gray

    &__header-item--favorite
        padding: 4px
        width: 26px

    &__header-item--pair
        text-transform: uppercase
        padding: 4px
        width: 80px

    &__header-item--rate
        text-transform: uppercase
        padding: 4px
        width: 80px

    &__header-item--volume
        text-transform: uppercase
        padding: 4px
        width: 100px

    &__header-item--change
        text-transform: uppercase
        padding: 4px
        width: 50px

    &__table-body
        display: flex
        flex-flow: column
        overflow-y: auto

    &__table-body-item
        display: flex
        justify-content: flex-start
        align-items: flex-start

    &__body-item--favorite
        padding: 4px
        flex: 1 1 auto
        width: 26px
        display: flex
        flex-direction: row

    &__body-item--pair
        padding: 4px
        flex: 1 1 auto
        width: 80px
        display: flex
        flex-direction: row

    &__body-item--cost
        padding: 4px
        flex: 1 1 auto
        width: 80px
        display: flex
        flex-direction: row

    &__body-item--volume
        padding: 4px
        flex: 1 1 auto
        width: 100px
        display: flex
        flex-direction: row

    &__body-item--change
        padding: 4px
        flex: 1 1 auto
        width: 50px
        display: flex
        flex-direction: row

    &__body-favorite-wrapper
        display: flex
        align-items: center
        justify-content: center

    &__pair-link
        text-decoration: none
        color: unset

    .small-cell-text
        font-size: 9px !important

.v-application--is-rtl
    .trading-tickers-list
        &__header-search
            padding-right: 0
            padding-left: 8px
        &__pair-link
            padding-left: 0
            padding-right: 4px

</style>
