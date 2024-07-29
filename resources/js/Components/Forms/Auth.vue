<script setup>

defineProps({
    status: String,
});

</script>

<template>
    <div class="layout-content auth ma-auto">
        <v-card>
            <v-card-title class="text-center">
				<span class="text-overline mb-4" style="font-size: 1.25rem !important">
                    <slot name="title" />
				</span>
            </v-card-title>

            <v-card-subtitle class="text-center text-break ma-4 mb-2 mt-0">
                <slot name="sub_title" />
            </v-card-subtitle>

            <slot />
        </v-card>

        <v-alert v-if="status" density="compact" variant="text" class="mt-4">
            {{ status }}
        </v-alert>

        <slot name="additional_actions" />
    </div>
</template>

<style scoped lang="scss">
    .layout-content {
        max-width: 480px;
        z-index: 1;
    }
</style>
