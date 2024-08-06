<script setup>
import {useDisplay} from "vuetify";
import {computed, ref} from "vue";
import {useStore} from "vuex";
import TradingFormBuy from "@/Components/Cards/TradingFormBuy.vue";
import TradingFormSell from "@/Components/Cards/TradingFormSell.vue";
const { mobile } = useDisplay();
const store = useStore();

const props = defineProps({
    currency: {
        type: String,
        required: true,
    },
    market: {
        type: String,
        required: true,
    }
});

const best_ask = computed(() => {
    return store.state.trading.best_ask;
});

const best_bid = computed(() => {
    return store.state.trading.best_bid;
});

const selectedTab = ref(0);

</script>
<template>
    <v-card class="trading-forms">
        <v-row v-if="!mobile" class="pa-1" no-gutters>
            <v-col class="trading-forms__form trading-forms__form--buy" cols="12" md="6">
                <v-card-title class="trading-forms__header component-title pa-0">
					<span>
						{{ $t('trading.order.direction.buy') }}
						{{ currency }}
					</span>
                    <div class="trading-forms__best">
                        <span> {{ $t('trading.forms.best_ask') }}: {{ best_ask }} </span>
                    </div>
                </v-card-title>
                <v-card-text class="trading-forms__content pa-0">
                    <TradingFormBuy :currency="currency" :market="market" />
                </v-card-text>
            </v-col>

            <v-col class="trading-forms__form trading-forms__form--sell" cols="12" md="6">
                <v-card-title class="trading-forms__header component-title pa-0">
					<span>
						{{ $t('trading.order.direction.sell') }}
						{{ currency }}
					</span>
                    <div class="trading-forms__best">
                        <span>{{ $t('trading.forms.best_bid') }}: {{ best_bid }}</span>
                    </div>
                </v-card-title>
                <v-card-text class="trading-forms__content pa-0">
                    <TradingFormSell :currency="currency" :market="market" />
                </v-card-text>
            </v-col>
        </v-row>

        <!--	mobile version	-->
        <div v-else>
            <v-tabs v-model="selectedTab" class="small-tabs" grow>
                <v-tab :key="1">
                    {{ $t('trading.order.direction.buy') }}
                </v-tab>
                <v-tab :key="2">
                    {{ $t('trading.order.direction.sell') }}
                </v-tab>
            </v-tabs>

            <v-tabs-window v-model="selectedTab" class="py-1 px-1" touchless>
                <v-tabs-window-item value="1">
                    <TradingFormBuy :currency="currency" :market="market" />
                </v-tabs-window-item>
                <v-tabs-window-item value="2">
                    <TradingFormSell :currency="currency" :market="market" />
                </v-tabs-window-item>
            </v-tabs-window>
        </div>
    </v-card>
</template>
<style scoped lang="sass">
.trading-forms
    display: flex
    flex-grow: 1
    flex-flow: column

    &__form
        display: flex
        flex-flow: column
        flex-grow: 1
        &--buy
            padding-right: 8px !important
        &--sell
            padding-left: 8px !important

    &__header
        display: flex
        justify-content: space-between
        margin-left: 4px

    &__best
        font-weight: normal
        font-size: 12px
        text-transform: none
        opacity: 0.6
    &__content
        display: flex
        flex-flow: column
        flex-grow: 1

.v-application--is-rtl
    .trading-forms
        &__form
            &--buy
                padding-right: 0
                padding-left: 8px !important
            &--sell
                padding-left: 0
                padding-right: 8px !important

@media screen and (min-width: 960px)
    .trading-forms
        min-height: 528px

@media screen and (max-width: 959px)
    .trading-forms
        &__sell
            padding-top: 2rem
</style>
