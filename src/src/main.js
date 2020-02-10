import Vue from 'vue';
import EasySearch from './EasySearch.vue';

window.onload = function () {
    // Can we find the search input?
    window.searchInput = document.querySelector('header .search input');

    if (window.searchInput !== null) {
        // Create a new container for easy search
        const easySearchContainer = document.createElement('div');

        // Add the search container to the search input
        window.searchInput.after(easySearchContainer);

        new Vue({
            render: h => h(EasySearch),
        }).$mount(easySearchContainer);
    }
}