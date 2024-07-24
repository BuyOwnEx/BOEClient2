<script setup>
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import AuthenticationCard from '@/Components/AuthenticationCard.vue';
    import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
    import { mdiAccountOutline, mdiEmailOutline, mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js';
    import { required, email, min8char, min5char, latinAndNumbers } from '@/Rules/rules';
    import Checkbox from '@/Components/Checkbox.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { ref } from "vue";

    let show = ref(false);
    let show_confirm = ref(false);

    const toggleShow = () => {
        show.value = !show.value;
    };

    const toggleShowConfirm = () => {
        show_confirm.value = !show_confirm.value;
    };

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
        police: false,
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>

    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="layout-content auth ma-auto">
            <v-card>
                <v-card-title class="text-center">
                    <span class="text-overline mb-4" style="font-size: 1.25rem !important">
                        {{ $t('auth.register.title') }}
                    </span>
                </v-card-title>
                <v-card-subtitle class="text-center ">
                    <span>{{ $t('auth.register.subtitle') }}</span>
                </v-card-subtitle>
                <form @submit.prevent="submit">
                    <v-card-text>
                        <v-container class="pt-0 pb-5">
                            <v-row>
                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field v-model="form.name" variant="underlined"
                                        :append-inner-icon="mdiAccountOutline"
                                        :hint="$t('auth.register.username_rules')" :error-messages="form.errors.name"
                                        :rules="[required, min5char, latinAndNumbers]" counter="128" persistent-hint
                                        clearable required @input="form.errors.name = []">
                                        <template #label>
                                            {{ $t('auth.username') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>

                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field v-model="form.email" variant="underlined"
                                        :append-inner-icon="mdiEmailOutline" :hint="$t('auth.login.enter_your_email')"
                                        :error-messages="form.errors.email" :rules="[required, email]" counter="255"
                                        persistent-hint clearable required @input="form.errors.email = []">
                                        <template #label>
                                            {{ $t('auth.email') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>

                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field v-model="form.password" variant="underlined"
                                        :append-inner-icon="show ? mdiEyeOutline : mdiEyeOffOutline"
                                        :hint="$t('forms_validation.min_8char')" :error-messages="form.errors.password"
                                        :rules="[required, min8char]" counter="255" persistent-hint required
                                        :type="show ? 'text' : 'password'" @input="form.errors.password = []"
                                        @click:appendInner="toggleShow">
                                        <template #label>
                                            {{ $t('auth.password') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>

                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field v-model="form.password_confirmation" variant="underlined"
                                        :append-inner-icon="show_confirm ? mdiEyeOutline : mdiEyeOffOutline"
                                        :hint="$t('auth.forgot.repeat_pass')"
                                        :error-messages="form.errors.password_confirmation"
                                        :rules="[required, min8char]" counter="255" persistent-hint required
                                        :type="show_confirm ? 'text' : 'password'"
                                        @input="form.errors.password_confirmation = []"
                                        @click:appendInner="toggleShowConfirm">
                                        <template #label>
                                            {{ $t('auth.forgot.confirm_pass') }} <span
                                                class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>

                                <!-- <v-col v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" cols="12" md="12" -->
                                <v-col cols="12" md="12" class="pt-0 pb-0">

                                    <v-checkbox density="compact" :ripple="false" hide-details v-model="form.terms">
                                        <template #label>
                                            <small class="grey--text lighten-4">
                                                {{ $t('auth.register.checkbox_agree_terms') }}
                                                <a target="_blank" :href="route('register')">
                                                    <span>{{ $t('auth.register.terms_link') }}</span>
                                                </a>
                                            </small>
                                        </template>
                                    </v-checkbox>
                                    <v-checkbox density="compact" :ripple="false" hide-details v-model="form.terms">
                                        <template #label>
                                            <small class="grey--text lighten-4">
                                                {{ $t('auth.register.checkbox_agree_policy') }}
                                                <a target="_blank" :href="route('register')">
                                                    <span>{{ $t('auth.register.policy_link') }}</span>
                                                </a>
                                            </small>
                                        </template>
                                    </v-checkbox>
                                </v-col>


                            </v-row>
                        </v-container>
                    </v-card-text>





                    <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                        <v-btn base-color="primary" block tile variant="flat" :disabled="!valid || !form.terms"
                            type="submit">
                            {{ $t('auth.signin') }}
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
                </form>

            </v-card>
            <v-btn :to="route('login')" class="mt-10" color="primary" variant="text" block>
                {{ $t('auth.register.account') }}
            </v-btn>
        </div>
    </AuthenticationCard>
</template>
