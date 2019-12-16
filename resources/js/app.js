/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/* require('./bootstrap');

window.Vue = require('vue');

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);


const app = new Vue({
    el: '#app',
}); */


require('./bootstrap');

require('./global.js');
require('./form_elements.js');

// Document Management
require('./doc_management/create/add_fields.js');
require('./doc_management/create/files.js');
require('./doc_management/create/upload.js');

require('./doc_management/fill/fill_fields.js');

require('./test/test.js');
