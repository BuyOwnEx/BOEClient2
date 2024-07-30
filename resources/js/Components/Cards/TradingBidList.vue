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
    <v-card class="trading-bid-list">
        <div class="text-center">bid-list</div>
    </v-card>
</template>

