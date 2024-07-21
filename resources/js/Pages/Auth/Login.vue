<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { mdiEmailOutline, mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js';
import { required, email, min8char } from '@/Rules/rules'
import {ref} from "vue";

const valid = ref(true);
let show = ref(false);

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const toggleShow = () => {
    show.value = !show.value;
};

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="layout-content auth ma-auto">
            <v-card>
                <v-card-title class="text-center">
				<span class="text-overline mb-4" style="font-size: 1.25rem !important">
					{{ $t('auth.login.title') }}
				</span>
                </v-card-title>

                <v-card-subtitle class="text-center">
                    <span>{{ $t('auth.login.fill_all_to_login') }}</span>
                </v-card-subtitle>

                <v-form @submit.prevent="submit" v-model="valid">
                    <v-card-text>
                        <v-container class="pt-0 pb-0">
                            <v-row>
                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field
                                        v-model="form.email"
                                        variant="underlined"
                                        :append-inner-icon="mdiEmailOutline"
                                        :hint="$t('auth.login.enter_your_email')"
                                        :error-messages="form.errors.email"
                                        :rules="[required,email]"
                                        persistent-hint
                                        clearable
                                        required
                                        @input="form.errors.email = []"
                                    >
                                        <template #label>
                                            {{ $t('auth.email') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>

                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field
                                        v-model="form.password"
                                        variant="underlined"
                                        :append-inner-icon="show ? mdiEyeOutline : mdiEyeOffOutline"
                                        :hint="$t('auth.password')"
                                        :error-messages="form.errors.password"
                                        :rules="[required, min8char]"
                                        persistent-hint
                                        required
                                        :type="show ? 'text' : 'password'"
                                        @input="form.errors.password = []"
                                        @click:appendInner="toggleShow"
                                    >
                                        <template #label>
                                            {{ $t('auth.password') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" md="12" class="pt-1 pb-0 pl-2 pr-2">
                                    <v-checkbox density="compact" :ripple="false" hide-details v-model="form.remember">
                                        <template #label>
                                            <span class="text-grey-lighten-4">
                                                {{ $t('auth.login.remember') }}
                                            </span>
                                        </template>
                                    </v-checkbox>
                                </v-col>
                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <div id="captcha"></div>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                        <v-btn base-color="primary" block tile variant="flat" type="submit">
                            {{ $t('auth.signin') }}
                        </v-btn>
                    </v-card-actions>

                    <div v-if="canResetPassword" class="text-center pb-1 pl-2 pr-2">
                        <v-btn color="primary" :to="route('password.request')" small variant="plain">
                            {{ $t('auth.login.forgot') }}
                        </v-btn>
                    </div>

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
            </v-card>

            <v-alert v-if="status" density="compact" variant="text" class="mt-4">
                {{ status }}
            </v-alert>

<!--            <v-alert v-if="status.verify_block" density="compact" variant="text" type="success" class="mt-4">
                {{ $t('auth.login.verified_text') }}
            </v-alert>

            <v-alert v-if="status.verify_error" density="compact" variant="text" type="error" class="mt-4">
                {{ status.verify_error }}
            </v-alert>-->

            <div class="text-center mt-6">
                <div class="text-caption text-grey">
                    {{ $t('auth.login.noaccount') }}
                </div>
                <v-btn :to="route('register')" color="primary" variant="text" block>
                    {{ $t('auth.login.create') }}
                </v-btn>
            </div>
        </div>
    </AuthenticationCard>
</template>
<style scoped lang="scss">

</style>
