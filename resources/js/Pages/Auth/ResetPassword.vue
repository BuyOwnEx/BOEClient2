<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthFormWrapper from '@/Components/Forms/Auth.vue';
import PasswordInput from '@/Components/Fields/PasswordInput.vue';
import { mdiEmailOutline } from '@mdi/js';
import { required, min8char, max255char, passMatch } from '@/Rules/rules';

import {ref} from "vue";

const valid = ref(true);

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <AuthFormWrapper>
            <template #title>
                {{ $t('auth.check.title') }}
            </template>
            <template #sub_title>
                {{ $t('auth.forgot.safe_password_alert') }}
            </template>
            <v-form @submit.prevent="submit" v-model="valid">
                <v-card-text>
                    <v-container class="pt-0 pb-0">
                        <v-row>
                            <v-col cols="12" md="12" class="pt-2 pb-0">
                                <v-text-field
                                    v-model="form.email"
                                    :append-inner-icon="mdiEmailOutline"
                                    :hint="$t('auth.login.enter_your_email')"
                                    :rules="[required]"
                                    :error-messages="form.errors.email"
                                    @input="form.errors.email = null"
                                    counter="255"
                                    autocomplete="username"
                                    readonly
                                    variant="underlined"
                                    persistent-hint
                                    required
                                >
                                    <template #label>
                                        {{ $t('auth.email') }} <span class="text-red ml-1"><b>*</b></span>
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="12" class="pt-2 pb-0">
                                <PasswordInput
                                    v-model="form.password"
                                    :hint="$t('forms_validation.min_8char')"
                                    :label="$t('auth.password')"
                                    :rules="[required, min8char, max255char]"
                                    :error-messages="form.errors.password"
                                    @input="form.errors.password = null"
                                    counter="255"
                                    autocomplete="new-password"
                                ></PasswordInput>
                            </v-col>
                            <v-col cols="12" md="12" class="pt-2 pb-0">
                                <PasswordInput
                                    v-model="form.password_confirmation"
                                    :hint="$t('auth.forgot.repeat_pass')"
                                    :label="$t('auth.forgot.confirm_pass')"
                                    :rules="[required, min8char, max255char, passMatch(form.password_confirmation, form.password)]"
                                    :error-messages="form.errors.password_confirmation"
                                    @input="form.errors.password_confirmation = null"
                                    counter="255"
                                    autocomplete="new-password"
                                ></PasswordInput>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                    <v-btn base-color="primary" block tile variant="flat" type="submit" :disabled="!valid || form.processing">
                        {{ $t('common.set') }}
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
                        {{ $t('auth.register.account') }}
                    </div>
                    <v-btn :to="route('login')" color="primary" variant="text" block>
                        {{ $t('auth.signin') }}
                    </v-btn>
                </div>
            </template>
        </AuthFormWrapper>
    </AuthenticationCard>
</template>
