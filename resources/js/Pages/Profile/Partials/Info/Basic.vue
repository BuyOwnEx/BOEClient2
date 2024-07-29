<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { createAvatar } from '@dicebear/core';
import * as initials from '@dicebear/initials';
import * as identicon from '@dicebear/identicon';


const props = defineProps({
    user: Object,
    sessions: Array,
    requiresConfirmation: Boolean,
});
const page = usePage();
const enabling = ref(false);

const avatar_type = import.meta.env.VITE_USER_ICON_TYPE || 'identicon';

const ref_link = () => {
    return `${window.location.origin}?ref=${props.user.id}`;
};

const avatar = computed(() => {
    if(avatar_type === 'initials')
    {
        return createAvatar(initials, {
            seed: props.user.name,
        });
    }
    else
    {
        return createAvatar(identicon, {
            seed: props.user.name,
        });
    }
})


const is2FAEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled && !props.requiresConfirmation,
);

const verifyStatus = true;

</script>
<template>
    <v-card class="user-account-tab-basic-info d-flex flex-column mb-1">
        <v-card-title>
            {{ $t('user.title.basic_info') }}
        </v-card-title>

        <v-card-text>
            <div class="d-flex flex-column flex-sm-row">
                <div>
                    <v-avatar
                        class="user-account-tab-basic-info__avatar rounded"
                        size="90"
                        v-html="avatar"
                    />
                </div>

                <div class="flex-grow-1 pt-2 pt-sm-0 px-sm-2">
                    <v-text-field
                        class="mb-2"
                        v-model="props.user.name"
                        :label="$t('user.info.display_name')"
                        hide-details
                        readonly
                    />
                    <v-text-field
                        class="mb-2"
                        v-model="props.user.email"
                        :label="$t('user.info.email')"
                        hide-details
                        readonly
                    />
                    <v-text-field
                        class="mb-2"
                        v-model="props.user.rate_limit"
                        :label="$t('user.info.rate')"
                        hide-details
                        readonly
                    />

                    <div class="mt-1 mb-2">
                        <div>{{ $t('user.info.ref_link') }}</div>
<!--TODO                        <CommonCopyLabel :text="ref_link" />-->
                    </div>

                    <div class="d-flex flex-column">
                        <v-checkbox
                            class="mt-0"
                            :input-value="props.user.email_verified_at !== null"
                            :label="$t('user.info.email_verified')"
                            readonly
                            hide-details
                            dense
                        />
                        <v-checkbox
                            class="mt-0"
                            v-model="is2FAEnabled"
                            :label="$t('user.info.protected')"
                            readonly
                            hide-details
                            dense
                        />
                        <v-checkbox
                            class="mt-0"
                            v-model="verifyStatus"
                            :label="$t('user.info.kyc_status')"
                            readonly
                            hide-details
                            dense
                        />
                    </div>
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>
