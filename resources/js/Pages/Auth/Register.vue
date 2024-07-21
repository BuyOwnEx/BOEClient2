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
import {ref} from "vue";

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

        <form @submit.prevent="submit">
            <v-card-text>
                <v-container class="pt-0 pb-0">
                    <v-row>
                        <v-col cols="12" md="12" class="pt-0 pb-0">
                            <v-text-field
                                v-model="form.name"
                                variant="underlined"
                                :append-inner-icon="mdiAccountOutline"
                                :hint="$t('auth.register.username_rules')"
                                :error-messages="form.errors.name"
                                :rules="[required, min5char, latinAndNumbers]"
                                counter="128"
                                persistent-hint
                                clearable
                                required
                                @input="form.errors.name = []"
                            >
                                <template #label>
                                    {{ $t('auth.username') }} <span class="text-red ml-1"><b>*</b></span>
                                </template>
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" md="12" class="pt-0 pb-0">
                            <v-text-field
                                v-model="form.email"
                                variant="underlined"
                                :append-inner-icon="mdiEmailOutline"
                                :hint="$t('auth.login.enter_your_email')"
                                :error-messages="form.errors.email"
                                :rules="[required,email]"
                                counter="255"
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
                                :hint="$t('forms_validation.min_8char')"
                                :error-messages="form.errors.password"
                                :rules="[required, min8char]"
                                counter="255"
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

                        <v-col cols="12" md="12" class="pt-0 pb-0">
                            <v-text-field
                                v-model="form.password_confirmation"
                                variant="underlined"
                                :append-inner-icon="show_confirm ? mdiEyeOutline : mdiEyeOffOutline"
                                :hint="$t('auth.forgot.repeat_pass')"
                                :error-messages="form.errors.password_confirmation"
                                :rules="[required, min8char, confirm]"
                                counter="255"
                                persistent-hint
                                required
                                :type="show_confirm ? 'text' : 'password'"
                                @input="form.errors.password_confirmation = []"
                                @click:appendInner="toggleShowConfirm"
                            >
                                <template #label>
                                    {{ $t('auth.forgot.confirm_pass') }} <span class="text-red ml-1"><b>*</b></span>
                                </template>
                            </v-text-field>
                        </v-col>

                        <v-col v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" cols="12" md="12" class="pt-0 pb-0">
                            <v-checkbox inline density="compact" v-model:checked="form.terms" :error-messages="form.errors.terms" />
                            <div class="text-left pl-6 pb-4 pr-6">
                                <small class="text-grey-lighten-4">
                                    {{ $t('auth.register.agree') }}
                                    <a target="_blank" :href="route('terms.show')">
                                        <span>{{ $t('auth.register.terms_link') }}</span>
                                    </a>
                                    {{ $t('auth.and') }}
                                    <a target="_blank" :href="route('policy.show')">
                                        <span>{{ $t('auth.register.policy_link') }}</span>
                                    </a>
                                </small>
                            </div>
                        </v-col>

                        <v-col cols="12" md="12" class="pt-0 pb-0">
                            <div class="text-left">
                                <small>
                                    <span class="text-red mr-1">
                                        <b>*</b>
                                    </span>
                                    <span class="text-grey-lighten-1">
                                        {{ $t('indicates_required_fields') }}
                                    </span>
                                </small>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>



            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
