require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

require('livewire-vue');

import Vue from 'vue';
import 'livewire-vue';

window.Vue = Vue

window.Vue.directive('focus', {
    inserted: function (el) {
        el.focus()
    }
})

// Vue.component('wtvmodal', require('./components/wtvmodal.vue').default);
