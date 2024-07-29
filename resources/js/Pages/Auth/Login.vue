<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useLocale, useTheme } from 'vuetify'
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthFormWrapper from '@/Components/Forms/Auth.vue';
import TextInputRequired from '@/Components/Fields/TextInputRequired.vue';
import PasswordInput from '@/Components/Fields/PasswordInput.vue';
import Checkbox from '@/Components/Fields/Checkbox.vue';
import { mdiEmailOutline } from '@mdi/js';
import { required, email, min8char, max255char } from '@/Rules/rules'
import {onMounted, onUnmounted, ref} from "vue";

const props = defineProps({
    canResetPassword: Boolean,
    captcha_enabled: Boolean,
    captcha_type: String
});

const { current } = useLocale();
const theme = useTheme();
const valid = ref(true);
let captcha_obj = ref(null);

const form = useForm({
    email: '',
    password: '',
    remember: false,
    lot_number: null,
    captcha_output: null,
    pass_token: null,
    gen_time: null
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const verify = () => {
    if(props.captcha_enabled)
    {
        if(props.captcha_type.toUpperCase() === 'GEETEST')
            captcha_obj.showCaptcha();
        else submit();
    }
    else submit();
};

const getGeetestLang = (lang) => {
    switch (lang)
    {
        case 'ar':
            return 'ara';
        case 'de':
            return 'deu';
        case 'en':
            return 'eng';
        case 'es':
            return 'spa';
        case 'fr':
            return 'fra';
        case 'ja':
            return 'jpn';
        case 'ko':
            return 'kor';
        case 'ru':
            return 'rus';
        case 'pt':
            return 'por';
        case 'zh':
            return 'zho';
        default:
            return 'eng';
    }
};

const captchaHandler = (obj) => {
    obj.appendTo("#captcha")
    .onReady(function () {
        captcha_obj=obj
    }).onSuccess(function() {
        captcha_obj=obj;
        let result = obj.getValidate();

        form.lot_number = result.lot_number;
        form.captcha_output = result.captcha_output;
        form.pass_token = result.pass_token;
        form.gen_time = result.gen_time;
        submit();
    }).onError(function () {
        obj.reset();
    })
};

const initCaptcha = (e) => {
    if (props.captcha_type.toUpperCase() === 'GEETEST') {
        axios.get('/captcha?captcha_type=geetest&t='+(new Date()).getTime()).then( res => {
            initGeetest4({
                captchaId: res.data.captcha_id,
                product: res.data.product,
                language: getGeetestLang(current.value),
                protocol: window.location.protocol+'//'
            }, captchaHandler);
        })
    }
    else if(props.captcha_type.toUpperCase() === 'CLOUDFLARE')
    {
        axios.get('/captcha?captcha_type=cloudflare&t='+(new Date()).getTime()).then( res => {
            turnstile.render('#captcha', {
                sitekey: res.data.captcha_id,
                language: current.value,
                theme: theme.global.current.value.dark ? 'dark' : 'light',
                callback: function(token) {
                    captcha_obj = token;
                    form.captcha_output = token;
                },
            });
        })
    }
};

onMounted(() => {
    if(props.captcha_enabled ) initCaptcha()
});

onUnmounted(() => {

});

</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <AuthFormWrapper>
            <template #title>
                {{ $t('auth.login.title') }}
            </template>
            <template #sub_title>
                {{ $t('auth.login.fill_all_to_login') }}
            </template>
            <v-form @submit.prevent="verify" v-model="valid">
                <v-card-text>
                    <v-container class="pt-0 pb-0">
                        <v-row>
                            <v-col cols="12" md="12" class="pt-0 pb-0">
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
                                    :hint="$t('auth.password')"
                                    :label="$t('auth.password')"
                                    :rules="[required, min8char, max255char]"
                                    :error-messages="form.errors.password"
                                    @input="form.errors.password = null"
                                    counter="255"
                                ></PasswordInput>
                            </v-col>
                            <v-col cols="12" md="12" class="pt-2 pb-0 pl-1 pr-1">
                                <Checkbox v-model="form.remember" :label="$t('auth.login.remember')"></Checkbox>
                            </v-col>
                            <v-col cols="12" md="12" class="pt-0 pb-0 pl-2 pr-2">
                                <div id="captcha"></div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions class="pt-2 pl-6 pr-6 pb-2">
                    <v-btn base-color="primary" block tile variant="flat" type="submit" :loading="form.processing" :disabled="!valid || form.processing">
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
            <template #additional_actions>
                <div class="text-center mt-6">
                    <div class="text-caption text-grey mb-1">
                        {{ $t('auth.login.noaccount') }}
                    </div>
                    <v-btn :to="route('register')" color="primary" variant="text" block>
                        {{ $t('auth.login.create') }}
                    </v-btn>
                </div>
            </template>
        </AuthFormWrapper>
    </AuthenticationCard>
</template>
