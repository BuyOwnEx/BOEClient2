<script setup>
import {
    mdiFinance,
    mdiHome,
    mdiAutorenew,
    mdiCached,
    mdiSwapHorizontal,
    mdiFileDocumentOutline,
    mdiCheckboxMarkedOutline,
    mdiCurrencyUsd,
    mdiAccountCash,
    mdiMessageOutline,
    mdiCogs,
    mdiInformationOutline
} from '@mdi/js'

const menu = [
    {
        icon: mdiFinance,
        key: 'menu.trading',
        link: '/',
    },
    {
        icon: mdiHome,
        key: 'menu.overview',
        link: '/overview',
    },
    {
        icon: mdiAutorenew,
        key: 'menu.transactions',
        link: '/transactions',
    },
    {
        icon: mdiCached,
        key: 'menu.fiat_transactions',
        link: '/fiat_transactions',
    },
    {
        icon: mdiSwapHorizontal,
        key: 'menu.transfers',
        link: '/transfers',
    },
    {
        icon: mdiFileDocumentOutline,
        key: 'menu.orders',
        link: '/orders',
    },
    {
        icon: mdiCheckboxMarkedOutline,
        key: 'menu.deals',
        link: '/deals',
    },
    {
        icon: mdiCurrencyUsd,
        key: 'menu.balance',
        link: '/balance',
    },
    {
        icon: mdiAccountCash,
        key: 'menu.refs',
        link: '/ref_payments',
    },
    {
        icon: mdiMessageOutline,
        key: 'menu.support',
        link: '/support',
    },
    {
        icon: mdiCogs,
        key: 'menu.profile',
        link: '/profile',
    },
    {
        icon: mdiInformationOutline,
        key: 'menu.notifications',
        link: '/notifications',
    },
];

</script>

<template>
    <v-list density="compact" nav>
        <v-list-item v-for='item in menu' :key='item.key' :prepend-icon="item.icon" :title="$t(item.key)" :link="item.link">
<!--            <template v-slot:prepend>
                <v-icon :icon="item.icon"></v-icon>
            </template>
            <v-list-item-title>{{ $t(item.key) }}</v-list-item-title>-->
        </v-list-item>
<!--        <v-list-item prepend-icon="mdi-folder" title="My Files" value="myfiles"></v-list-item>
        <v-list-item prepend-icon="mdi-account-multiple" title="Shared with me" value="shared"></v-list-item>
        <v-list-item prepend-icon="mdi-star" title="Starred" value="starred"></v-list-item>-->
    </v-list>
</template>
