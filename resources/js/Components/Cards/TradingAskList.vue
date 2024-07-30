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
    <v-card class="trading-ask-list">
        <div class="text-center">ask-list</div>
    </v-card>
</template>

