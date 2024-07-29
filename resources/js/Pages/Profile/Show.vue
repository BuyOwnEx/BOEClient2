<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import {ref} from "vue";
import SumSub from "@/Pages/Profile/Partials/Verification/SumSub.vue";
import Kontur from "@/Pages/Profile/Partials/Verification/Kontur.vue";
import Local from "@/Pages/Profile/Partials/Verification/Local.vue";
import UserAccountTab from "@/Pages/Profile/Partials/UserAccountTab.vue";
import UserApiTab from "@/Pages/Profile/Partials/UserApiTab.vue";
import UserSecurityTab from "@/Pages/Profile/Partials/UserSecurityTab.vue";
import UserSettingsTab from "@/Pages/Profile/Partials/UserSettingsTab.vue";
//import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
//import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
//import SectionBorder from '@/Components/SectionBorder.vue';
//import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
//import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
//import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
const selectedTab = ref(0);
const kyc_driver = import.meta.env.VITE_KYC_DRIVER;
</script>

<template>
    <MainLayout title="Profile">
        <div class="profile-page flex-grow-1">
            <v-tabs v-model="selectedTab" show-arrows bg-color="surface" color="primary" class="mb-2">
                <v-tab value="1">{{ $t('user.title.account') }}</v-tab>
                <v-tab value="2">{{ $t('user.title.verification') }}</v-tab>
                <v-tab value="3">{{ $t('user.title.api') }}</v-tab>
                <v-tab value="4">{{ $t('user.title.security') }}</v-tab>
                <v-tab value="5">{{ $t('user.title.settings') }}</v-tab>
            </v-tabs>
            <v-tabs-window v-model="selectedTab" class="profile-page__tabs-items">
                <v-tabs-window-item value="1">
                    <UserAccountTab :user="$page.props.auth.user" :sessions="sessions" :requires-confirmation="confirmsTwoFactorAuthentication" />
                </v-tabs-window-item>

                <v-tabs-window-item value="2">
                    <SumSub v-if="kyc_driver === 'sumsub'" />
                    <Kontur v-else-if="kyc_driver === 'kontur'" />
                    <Local v-else />
                </v-tabs-window-item>

                <v-tabs-window-item value="3">
                    <UserApiTab />
                </v-tabs-window-item>
                <v-tabs-window-item value="4">
                    <UserSecurityTab :requires-confirmation="confirmsTwoFactorAuthentication" />
                </v-tabs-window-item>
                <v-tabs-window-item value="5">
                    <UserSettingsTab />
                </v-tabs-window-item>
            </v-tabs-window>
        </div>
<!--        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>-->
    </MainLayout>
</template>
