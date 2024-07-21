import { BigNumber } from 'bignumber.js';
BigNumber.config({ EXPONENTIAL_AT: [-15, 20] });
import i18n from './../Translations/i18n.js'

export const required = v => !!v || i18n.global.t('forms_validation.required');
export const email = v => (v && /.+@.+\..+/.test(v)) || i18n.global.t('forms_validation.email_invalid');
export const passMatch = (v,p) => (v && v === p) || i18n.global.t('auth.forgot.passwords_not_match');
export const min5char = v => !v || v.length >= 5 || i18n.global.t('forms_validation.min_5char');
export const min6char = v => !v || v.length >= 6 || i18n.global.t('forms_validation.min_6char');
export const min8char = v => !v || v.length >= 8 || i18n.global.t('forms_validation.min_8char');
export const max6char = v => !v || v.length <= 6 || i18n.global.t('forms_validation.max_6char');
export const max64char = v => !v || v.length <= 64 || i18n.global.t('forms_validation.max_64char');
export const latinAndNumbers = v => !v || (v && /^[a-zA-Z0-9-_]+$/g.test(v)) || i18n.global.t('forms_validation.unsupported_char_latinAndNumbers');
export const numbers = v => !v || /^[0-9]+$/.test(v) || i18n.global.t('forms_validation.unsupported_char_numbers');
export const positive = v => !v || BigNumber(v).gt(0) || i18n.global.t('forms_validation.positive');
export const maxFileSize5MB = v => !v || v.size < 5000000 || i18n.global.t('forms_validation.max_filesize_5MB');
export const nameRules = [
    (v) => !!v || i18n.global.t('forms_validation.required'),
    (v) => (v && v.length >= 5) || i18n.global.t('forms_validation.min_5char'),
    (v) => (v && /^[a-zA-Z0-9-_]+$/g.test(v)) || i18n.global.t('forms_validation.unsupported_char_latinAndNumbers'),
];
