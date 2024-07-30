<script setup>
    import { Link } from '@inertiajs/vue3';
    import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
    import ChangeLocale from "@/Components/ChangeLocale.vue";
    import ChangeTheme from "@/Components/ChangeTheme.vue";
    import { useDisplay, useTheme } from "vuetify";
    import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
    import { ClassicalNoise } from "@/Plugins/hero-canvas";

    const { smAndDown } = useDisplay();
    const theme = useTheme()
    const name = import.meta.env.VITE_PRODUCT_NAME;
    const version = import.meta.env.VITE_PRODUCT_VERSION;
    const slogan = import.meta.env.VITE_CONFIG_SLOGAN;

    const wavesRef = ref(null);
    const waves = new ClassicalNoise()

    onMounted(() => {
        nextTick(() => {
            waves.start(wavesRef.value);
        });
    })
    onBeforeUnmount(() => {
        if (waves.isStarted) waves.stop();
    })
</script>
<template>
    <v-app>
        <div class="auth-layout flex-md-row ">
            <div class="auth-layout__header">
                <ChangeLocale class="auth-layout__lang"></ChangeLocale>
                <ChangeTheme class="auth-layout__theme mr-md-0 ml-md-0" :class="[$vuetify.rtl ? 'mr-1' : 'ml-1']"></ChangeTheme>
            </div>

            <v-sheet class="auth-layout__side mx-auto d-none d-md-flex flex-md-column justify-space-between">
                <canvas class="waves-canvas" ref="wavesRef"></canvas>
                <div class="auth-layout__side-top mt-3 mt-md-1 pa-2">
                    <AuthenticationCardLogo />
                    <div class="auth-layout__slogan title my-2 text-white">
                        {{ slogan }}
                    </div>
                </div>
                <div class="auth-layout__links text-overline pa-1 mb-1">
                    <span>
                        <Link class="text-white" :href="route('landing')">{{ $t('menu.trading') }}</Link>
                        <Link class="text-white" :href="route('fees')">{{ $t('fees.title') }}</Link>
                        <Link class="text-white" :href="route('status')">{{ $t('status.title') }}</Link>
                        <Link class="text-white" :href="route('api')">{{ $t('menu.api') }}</Link>
                    </span>
                </div>
            </v-sheet>

            <div class="pa-2 pa-md-4 pt-9 pt-md-12 flex-grow-1 align-center justify-center d-flex flex-column z-1">
                <div class="layout-content ma-auto w-full">
                    <slot />
                </div>

                <div class="text-overline mt-4">
                    <div v-if="smAndDown" class="auth-layout__links mb-1">
                        <Link :href="route('landing')">{{ $t('menu.trading') }}</Link>
                        <Link :href="route('fees')">{{ $t('fees.title') }}</Link>
                        <Link :href="route('status')">{{ $t('status.title') }}</Link>
                        <Link :href="route('api')">{{ $t('menu.api') }}</Link>
                    </div>
                    <div class="text-center">{{ name }} - {{ version }}</div>
                </div>
            </div>
        </div>
    </v-app>
</template>
<style scoped lang="scss">
    .w-full {
        width: 100% !important;
    }

    .auth-layout {
        display: flex;
        flex-flow: column;
        flex-grow: 1;

        &__header {
            position: absolute;
            top: 32px;
            right: 32px;
            z-index: 1;
        }

        &__side {
            width: 420px;
            background: rgba(0, 0, 0, 0) linear-gradient(5deg, rgb(20, 47, 109) 0%, rgb(9, 28, 72) 40%) repeat scroll 0% 0%;
        }

        &__slogan {
            z-index: 1;
            text-align: center;
        }

        &__links {
            z-index: 1;
            text-align: center;

            a {
                text-decoration: none;
                color: unset;
                margin-right: 8px;
            }
        }
    }

    .v-locale--is-rtl .auth-layout {
        &__header {
            text-align: left;
            left: 32px;
            right: unset;
        }
    }

    .v-theme--dark .auth-layout {
        background-color: #222;

        &__side {
            background: rgba(0, 0, 0, 0) linear-gradient(5deg, rgba(3, 164, 194, 0.55) 0%, rgba(255, 255, 255, 0) 60%) repeat scroll 0 0;
        }
    }

    @media screen and (max-width: 959px) {
        .auth-layout {
            &__header {
                top: 16px;
                right: 8px;
            }
        }

        .v-locale--is-rtl .auth-layout {
            &__header {
                left: 8px;
            }
        }
    }

    .waves-canvas {
        width: 100%;
        max-height: 100%;
        position: absolute;
        top: 0;
        left: -2px;
        height: 100vh;
        z-index: 0;
    }
</style>
