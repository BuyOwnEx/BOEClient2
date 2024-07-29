<script setup>

defineProps({
    modelValue: String,
    label: {
        type: String,
        default: null,
    }
});
</script>

<template>
    <v-text-field
        v-bind="$attrs"
        :value="modelValue"
        variant="underlined"
        persistent-hint
        clearable
        required
        @input="v => $emit('input', v)"
    >
        <template #label>
            {{ label }} <span class="text-red ml-1"><b>*</b></span>
        </template>
    </v-text-field>
</template>
