<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Basic from "@/Pages/Profile/Partials/Info/Basic.vue";

const props = defineProps({
    user: Object,
    sessions: Array,
    requiresConfirmation: Boolean,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
});

const verificationLinkSent = ref(null);

const updateProfileInformation = () => {
    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => { console.log('UPDATED!')},
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

</script>

<template>
    <Basic :user="props.user" :sessions="props.sessions" :requires-confirmation="props.requiresConfirmation" />
    <div class="user-account-tab d-flex flex-column mb-1">
<!--TODO        <UserAccountBlockedAlert v-if="blockStatus" :status="blockStatus" />-->

<!--TODO        <UserAccountPanels :user="props.user" />-->
    </div>
    <v-card>
        <FormSection @submitted="updateProfileInformation">
            <template #title>
                Profile Information
            </template>

            <template #description>
                Update your account's profile information and email address.
            </template>

            <template #form>
                <!-- Name -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="name"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />

                    <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                        <p class="text-sm mt-2">
                            Your email address is unverified.

                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                @click.prevent="sendEmailVerification"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <ActionMessage :on="form.recentlySuccessful" class="me-3">
                    Saved.
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </template>
        </FormSection>
    </v-card>
</template>
