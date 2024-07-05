import './bootstrap';
import '../scss/config/creative/app.scss';
import '../scss/krayin/app.scss';
import '@vueform/slider/themes/default.css';
import '../scss/mermaid.min.css';
import VueFroala from 'vue-froala-wysiwyg';

import {
    createApp,
    h
} from 'vue';
import {
    createInertiaApp
} from '@inertiajs/vue3';
import {
    resolvePageComponent
} from 'laravel-vite-plugin/inertia-helpers';
import {
    ZiggyVue
} from '../../vendor/tightenco/ziggy/dist/vue.m';
import BootstrapVueNext from 'bootstrap-vue-next';
import vClickOutside from "click-outside-vue3";
import VueApexCharts from "vue3-apexcharts";
import VueFeather from 'vue-feather';
import VueTheMask from 'vue-the-mask';
import moment from 'moment';
import eventBus from "@/common/eventBus";

import AOS from 'aos';
import 'aos/dist/aos.css';

import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

import store from "./state/store";
import i18n from './i18n';
import http from './http';


import {
    Form,
    Field,
    ErrorMessage,
    defineRule,
    configure
} from 'vee-validate';
import * as rules from '@vee-validate/rules';

Object.keys(rules).forEach(ruleName => {
    defineRule(ruleName, rules[ruleName]);
});

defineRule('decimal', (value) => {
    const pattern = /^\d+(\.\d+)?$/;
    if (pattern.test(value)) {
        return true;
    }
    return false;
});
defineRule('date_format', (value, [format]) => {
    if (!value) {
        return true; // Skip validation if value is empty. Use 'required' rule to ensure value is present.
    }
    // Check if the date is valid according to the provided format
    const isValid = moment(value, format, true).isValid();
    // Return true if valid, or a custom error message if not
    return isValid || `The date must be in the format: ${format}`;
});
defineRule('after', (value, [targetDate], ctx) => {
    // Parse both the input value and the target date using moment
    const inputDate = moment(value, "YYYY-MM-DD HH:mm:ss", false);
    const comparisonDate = moment(targetDate, "YYYY-MM-DD HH:mm:ss", false);

    // Check if the input date is valid
    if (!inputDate.isValid()) {
        return `The date must be in the format: YYYY-MM-DD HH:mm:ss`;
    }

    // Validate that the input date is after the target date
    if (!inputDate.isAfter(comparisonDate)) {
        return `The ${ctx.field} must be after ${targetDate}.`;
    }

    return true;
});
configure({
    // Example configuration
    generateMessage: (ctx) => {
        const messages = {
            required: `The field "${ctx.label ?? ctx.field}" is required.`,
            email: `The field "${ctx.label ?? ctx.field}" must be a valid email.`,
            min: `The field "${ctx.label ?? ctx.field}" must have at least ${ctx.rule.params.length} characters.`,
            decimal: `The "${ctx.label ?? ctx.field}" field must be numeric and may contain decimal points`,
            unique_email: `The value has already been taken`,
            unique_contact_number: `The value has already been taken`,
            date_format: `The field "${ctx.label ?? ctx.field}" must be in the format: ${ctx.rule.params[0]}.`,
        };

        return messages[ctx.rule.name] ? messages[ctx.rule.name] : `The field ${ctx.field} is invalid.`;
    },
    validateOnInput: true, // Validates fields on input
});

AOS.init({
    easing: 'ease-out-back',
    duration: 1000
});
const vueToastifyOptions = {
    autoClose: 3000,
}

const preferredLocale = localStorage.getItem('preferredLocale');
const preferredCountry = localStorage.getItem('preferredCountry');
const preferredFlag = localStorage.getItem('preferredFlag');

if (preferredLocale && preferredCountry && preferredFlag) {
  // Set language preferences from browser storage
  i18n.global.locale = preferredLocale;
}

createInertiaApp({
    title: title => title ? `${title} | Memvera` : 'Memvera',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')),
    setup({
        el,
        App,
        props,
        plugin
    }) {
        return createApp({
            render: () => h(App, props),
            data() {
                return {
                  pageLoaded: false,
                  modalIds: {},
                  isMenuOpen: localStorage.getItem('crm-sidebar') === 'true',
                };
            },
            mounted() {
                this.$nextTick(() => {
                    const lang = document.documentElement.lang;
                    if (this.$refs.validator && this.$refs.validator.localize) {
                      this.$refs.validator.localize(lang);
                    }
                  });
              },
              methods: {
                onSubmit(e, formScope = '') {
                  this.toggleButtonDisable(true);
            
                  if (typeof tinyMCE !== 'undefined') {
                    tinyMCE.triggerSave();
                  }
                  if (self.$refs.validator && self.$refs.validator.localize) {
                    self.$refs.validator.validateAll(formScope || null).then(result => {
                        if (result) {
                            e.target.submit();
                        } else {
                            this.activateAutoScroll();
                            this.toggleButtonDisable(false);
                            eventBus.emit('onFormError');
                        }
                    });
                  }
                },
                activateAutoScroll(event) {
                  const normalElement = document.querySelector('.control-error:first-of-type');
                  const scrollConfig = {
                    behavior: 'smooth',
                    block: 'end',
                    inline: 'nearest',
                  };
                  if (normalElement) {
                    normalElement.scrollIntoView(scrollConfig);
                    return;
                  }
                },
                toggleButtonDisable(value) {
                  const buttons = document.getElementsByTagName('button');
                  for (let i = 0; i < buttons.length; i++) {
                    buttons[i].disabled = value;
                  }
                },
                openModal(id) {
                    this.modalIds[id] = true;
                    this.disableAutoComplete();
                },
                closeModal(id) {
                    this.modalIds[id] = false;
                },
                toggleMenu() {
                  this.isMenuOpen = !this.isMenuOpen;
                  localStorage.setItem('crm-sidebar', this.isMenuOpen);
                },
                disableAutoComplete: function () {
                    queueMicrotask(() => {
                        const dateInputs = document.querySelectorAll('.date-container input');
                        dateInputs.forEach(input => {
                            input.setAttribute('autocomplete', 'off');
                        });
                        const datetimeInputs = document.querySelectorAll('.datetime-container input');
                        dateInputs.forEach(input => {
                            input.setAttribute('autocomplete', 'off');
                        });
                    });
                }
              },
        })
            .use(plugin)
            .use(store)
            .use(http)
            .use(i18n)
            .use(ZiggyVue)
            .use(BootstrapVueNext)
            .use(VueApexCharts)
            .use(VueTheMask)
            .use(vClickOutside)
            .use(Vue3Toastify, vueToastifyOptions)
            .use(VueFroala)
            .component(VueFeather.type, VueFeather)
            .component('VForm', Form)
            .component('VField', Field)
            .component('ErrorMessage', ErrorMessage)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
