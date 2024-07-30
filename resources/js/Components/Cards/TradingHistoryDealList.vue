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
    <v-card class="trading-history-deal-list">
        <div class="text-center">history-deals</div>
    </v-card>
</template>

