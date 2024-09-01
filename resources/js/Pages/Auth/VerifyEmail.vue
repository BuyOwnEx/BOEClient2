<script setup>
import { computed } from 'vue';
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthFormWrapper from '@/Components/Forms/Auth.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const logout = () => {
    router.post(route('logout'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

    <AuthenticationCard>
        <AuthFormWrapper>
            <template #title>
                {{ $t('auth.verify.title') }}
            </template>
            <template #sub_title>
                {{ $t('auth.verify.subtitle') }}
            </template>
            <v-form @submit.prevent="submit">
                <v-card-actions class="pt-2 pl-6 pr-6 pb-4">
                    <v-btn base-color="primary" block tile variant="flat" type="submit" :loading="form.processing" :disabled="form.processing">
                        {{ $t('common.resend') }}
                    </v-btn>
                </v-card-actions>
            </v-form>
            <v-alert v-if="verificationLinkSent" density="compact" variant="tonal" type="info" class="text-center ma-6 mt-2">
                {{ $t('auth.verify.resend_activation_link_text') }}
            </v-alert>
            <v-alert v-if="$page.props.errors.message" density="compact" variant="tonal" type="error" class="text-center ma-6 mt-2">
                {{ $page.props.errors.message }}
            </v-alert>
            <template #additional_actions>
                <div class="text-center mt-6">
                    <v-btn @click="logout" color="primary" variant="text" block>
                        {{ $t('menu.logout') }}
                    </v-btn>
                </div>
            </template>
        </AuthFormWrapper>
    </AuthenticationCard>
</template>
