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
    <v-card class="trading-market-info">
        <div class="text-center">market-info</div>
    </v-card>
</template>
