<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    label: {
        type: String,
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
    <v-checkbox
        v-model="proxyChecked"
        density="compact"
        :ripple="false"
        hide-details
    >
        <template #label>
            <span class="text-grey-lighten-4">
                {{ label }}
            </span>
        </template>
    </v-checkbox>
</template>
