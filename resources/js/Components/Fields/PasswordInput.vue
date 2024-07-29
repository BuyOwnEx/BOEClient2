<script setup>
import { ref } from 'vue';
import { mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js';

defineProps({
    modelValue: String,
    label: {
        type: String,
        default: null,
    }
});

let show = ref(false);

const toggleShow = () => {
    show.value = !show.value;
};
</script>

<template>
    <v-text-field
        v-bind="$attrs"
        variant="underlined"
        :value="modelValue"
        :append-inner-icon="show ? mdiEyeOutline : mdiEyeOffOutline"
        persistent-hint
        :clearable="false"
        required
        :type="show ? 'text' : 'password'"
        @input="v => $emit('input', v)"
        @click:appendInner="toggleShow"
    >
        <template #label>
            {{ label }} <span class="text-red ml-1"><b>*</b></span>
        </template>
    </v-text-field>
</template>
