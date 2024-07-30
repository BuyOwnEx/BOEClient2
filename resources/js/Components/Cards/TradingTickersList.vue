<script setup>
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
</script>
<template>
    <v-card class="trading-tickers-list">
        <div class="text-center">tickers</div>
    </v-card>
</template>
