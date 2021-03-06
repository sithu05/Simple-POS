
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('category', require('./components/Category.vue'));
Vue.component('car', require('./components/Car.vue'));
Vue.component('product', require('./components/Product.vue'));
Vue.component('supplier', require('./components/Supplier.vue'));
Vue.component('customer', require('./components/Customer.vue'));
Vue.component('Purchase', require('./components/Purchase.vue'));
Vue.component('Sale', require('./components/Sale.vue'));
Vue.component('Stock', require('./components/Stock.vue'));

Vue.config.debug = true;

const app = new Vue({
    el: '#app'
});
