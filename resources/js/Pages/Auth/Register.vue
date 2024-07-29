<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthFormWrapper from '@/Components/Forms/Auth.vue';
import TextInputRequired from '@/Components/Fields/TextInputRequired.vue';
import PasswordInput from '@/Components/Fields/PasswordInput.vue';
import { mdiAccountOutline, mdiEmailOutline } from '@mdi/js';
import { required, email, min8char, min5char, max128char, max255char, latinAndNumbers, passMatch } from '@/Rules/rules';
import {computed, ref} from "vue";

const force_agree = import.meta.env.VITE_CONFIG_FORCE_AGREE === 'true';
let term_agree = ref(false);
let policy_agree = ref(false);

const valid = ref(true);

const isTermsAccepted = computed(() => {
    return force_agree ? true : term_agree.value && policy_agree.value;
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: isTermsAccepted,
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
        <AuthFormWrapper>
            <template #title>
                {{ $t('auth.register.title') }}
            </template>
            <template #sub_title>
                {{ $t('auth.register.subtitle') }}
            </template>
            <v-form @submit.prevent="submit" v-model="valid">
                <v-card-text>
                    <v-container class="pt-0 pb-0">
                        <v-row>
                            <v-col cols="12" md="12" class="pt-0 pb-0">
                                <TextInputRequired
                                    v-model="form.name"
                                    :append-inner-icon="mdiAccountOutline"
                                    :hint="$t('auth.register.username_rules')"
                                    :label="$t('auth.username')"
                                    :rules="[required, latinAndNumbers, min5char, max128char]"
                                    :error-messages="form.errors.name"
                                    @input="form.errors.name = null"
                                    counter="128"
                                ></TextInputRequired>
                            </v-col>
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
                            <v-col cols="12" md="12" class="pt-2 pb-0">
                                <PasswordInput
                                    v-model="form.password"
                                    :hint="$t('forms_validation.min_8char')"
                                    :label="$t('auth.password')"
                                    :rules="[required, min8char, max255char]"
                                    :error-messages="form.errors.password"
                                    @input="form.errors.password = null"
                                    counter="255"
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
                                ></PasswordInput>
                            </v-col>
                            <v-col v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" cols="12" md="12" class="pt-4 pb-4 pl-1 pr-1">
                                <div class="text-left pl-2 pr-2" v-if="force_agree">
                                    <span class="text-grey">
                                        {{ $t('auth.register.agree') }}
                                        <Link :href="route('terms.show')">{{ $t('auth.register.terms_link') }}</Link>
                                        {{ $t('auth.and') }}
                                        <Link :href="route('policy.show')">{{ $t('auth.register.policy_link') }}</Link>
                                    </span>
                                </div>
                                <div class="text-left pl-0 pr-0" v-else>
                                    <v-checkbox :ripple="false" hide-details v-model="term_agree">
                                        <template #label>
                                            <small class="text-grey">
                                                {{ $t('auth.register.checkbox_agree_terms') }}
                                                <Link :href="route('terms.show')">{{ $t('auth.register.terms_link') }}</Link>
                                            </small>
                                        </template>
                                    </v-checkbox>
                                    <v-checkbox :ripple="false" hide-details v-model="policy_agree">
                                        <template #label>
                                            <small class="text-grey">
                                                {{ $t('auth.register.checkbox_agree_policy') }}
                                                <Link :href="route('policy.show')">{{ $t('auth.register.policy_link') }}</Link>
                                            </small>
                                        </template>
                                    </v-checkbox>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                    <v-btn base-color="primary" block tile variant="flat" type="submit" :disabled="!valid || (!force_agree && (!term_agree || !policy_agree)) || form.processing">
                        {{ $t('auth.register.register_action') }}
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
