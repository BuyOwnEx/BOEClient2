<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import AuthenticationCard from '@/Components/AuthenticationCard.vue';
    import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
    import { mdiEmailOutline } from '@mdi/js';

    defineProps({
        status: String,
    });

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
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="layout-content auth ma-auto">
            <v-card>
                <v-card-title class="text-center">
                    <span class="text-overline mb-4" style="font-size: 1.25rem !important">
                        {{ $t('auth.forgot.title') }}
                    </span>
                </v-card-title>
                <v-card-subtitle class="text-center ">
                    <span>{{ $t('auth.forgot.subtitle') }}</span>
                </v-card-subtitle>


                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <v-card-text>
                        <v-container class="pt-0 pb-5">
                            <v-row>

                                <v-col cols="12" md="12" class="pt-0 pb-0">
                                    <v-text-field v-model="form.email" variant="underlined"
                                        :append-inner-icon="mdiEmailOutline" :hint="$t('auth.login.enter_your_email')"
                                        :error-messages="form.errors.email" :rules="[required, email]" persistent-hint
                                        clearable required @input="form.errors.email = []">
                                        <template #label>
                                            {{ $t('auth.email') }} <span class="text-red ml-1"><b>*</b></span>
                                        </template>
                                    </v-text-field>
                                </v-col>


                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                        <v-btn base-color="primary" block tile variant="flat" type="submit">
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
        </div>
    </AuthenticationCard>
</template>
