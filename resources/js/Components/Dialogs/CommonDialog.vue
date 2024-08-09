<script setup>
import {computed, ref} from "vue";

const props = defineProps({
    confirmText: {
        type: String,
        required: false,
    },
    confirmColor: {
        type: String,
        required: false,
        default: 'primary',
    },
    headerColor: {
        type: String,
        required: false,
        default: 'primary',
    },
});
const dialog = ref(false);

const emit = defineEmits(['confirm']);

const confirm = () => {
    emit('confirm');
    close();
};

const close = () => {
    dialog.value = false;
};

</script>
<template>
    <v-dialog v-model="dialog" width="500">
        <template #activator="{ props: activatorProps }">
			<span v-bind="activatorProps">
				<slot></slot>
			</span>
        </template>

        <v-card class="common-dialog">
            <slot name="card">
                <v-card-title class="common-dialog__title" :class="'bg-'+props.headerColor">
                    <slot name="title">
                        {{ $t('common.confirmation') }}
                    </slot>
                </v-card-title>

                <v-card-text class="common-dialog__content">
                    <slot name="content"></slot>
                </v-card-text>

                <v-divider />

                <v-card-actions class="common-dialog__actions">
                    <slot name="actions">
                        <v-spacer />

                        <slot name="close">
                            <v-btn color="secondary" tile variant="text" @click="close">
                                {{ $t('common.cancel') }}
                            </v-btn>
                        </slot>

                        <v-spacer />

                        <slot name="confirm">
                            <v-btn :color="confirmColor" tile variant="text" @click="confirm">
                                {{ confirmText || $t('common.confirm') }}
                            </v-btn>
                        </slot>

                        <v-spacer />
                    </slot>
                </v-card-actions>
            </slot>
        </v-card>
    </v-dialog>
</template>

<style scoped lang="sass">
.common-dialog
    &__title
        font-weight: 600 !important
        padding: 8px 24px 8px !important

    &__content
        padding-top: 8px !important
        padding-bottom: 8px !important

    &__actions
        .v-btn
            text-transform: uppercase !important
            letter-spacing: 1px !important
</style>
