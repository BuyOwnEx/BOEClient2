<script setup>
const props = defineProps({
    market: {
        type: String,
        required: true,
    }
});
</script>
<template>
    <v-card class="trading-last-price">
        <div class="text-center">last-price</div>
    </v-card>
</template>
