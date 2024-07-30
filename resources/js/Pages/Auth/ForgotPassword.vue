<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthFormWrapper from '@/Components/Forms/Auth.vue';
import TextInputRequired from '@/Components/Fields/TextInputRequired.vue';
import { mdiEmailOutline } from '@mdi/js';
import { required, email, max255char } from '@/Rules/rules';
import { ref } from "vue";

defineProps({
    status: String,
});

const valid = ref(true);

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <AuthFormWrapper>
            <template #title>
                {{ $t('auth.forgot.title') }}
            </template>
            <template #sub_title>
                {{ $t('auth.forgot.subtitle')}}
            </template>
            <v-form @submit.prevent="submit" v-model="valid">
                <v-card-text>
                    <v-container class="pt-0 pb-0">
                        <v-row>
                            <v-col cols="12" md="12" class="pt-2 pb-0">
                                <TextInputRequired
                                    v-model="form.email"
                                    :append-inner-icon="mdiEmailOutline"
                                    :hint="$t('auth.login.enter_your_email')"
                                    :label="$t('auth.email')"
                                    :rules="[required, email, max255char]"
                                    :error-messages="form.errors.email"
                                    @input="form.errors.email = null"
                                    counter="255"
                                ></TextInputRequired>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                    <v-btn base-color="primary" block tile variant="flat" type="submit" :loading="form.processing" :disabled="!valid || form.processing">
                        {{ $t('common.send') }}
                    </v-btn>
                </v-card-actions>

                <div class="text-left pl-6 pr-6 pb-3">
                    <small style="font-size: 80%">
                            <span class="text-red">
                                <b>*</b>
                            </span>
                        <span class="text-grey-lighten-1 ml-1">
                                {{ $t('auth.indicates_required_fields') }}
                            </span>
                    </small>
                </div>
            </v-form>
            <template #additional_actions>
                <div class="text-center mt-6">
                    <div class="text-caption text-grey mb-1">
                        {{ $t('auth.login.noaccount') }}
                    </div>
                    <v-btn :to="route('register')" color="primary" variant="text" block>
                        {{ $t('auth.login.create') }}
                    </v-btn>
                </div>
            </template>
            <v-alert v-if="status" density="compact" variant="tonal" type="info" class="text-center ma-6 mt-2">
                {{ status }}
            </v-alert>
        </AuthFormWrapper>
    </AuthenticationCard>
</template>
