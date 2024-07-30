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
    <v-card class="trading-own-list">
        <div class="text-center">own-list</div>
    </v-card>
</template>

