/**
 * *****   IMPORTANT   *****
 *
 * DO NOT CHANGE OR DELETE THIS FILE!
 *
 * This file imports multiple JS libraries and frameworks.
 * Each project and/or environment may need different settings. So, this file should be copied and used as a template.
 * Changes in this actual file should be discussed between Web Development teams!
 *
 */


/**   lodash   **/
import _ from 'lodash';
window._ = _;


/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

// fix for jQuery Bootstrap functions (issue caused by Vite)
// solution source: https://blog.madbob.org/fixing-vite-bootstrap-jquery/
function defineJQueryPlugin(plugin) {
    const name = plugin.NAME;
    const JQUERY_NO_CONFLICT = $.fn[name];
    $.fn[name] = plugin.jQueryInterface;
    $.fn[name].Constructor = plugin;
    $.fn[name].noConflict = () => {
        $.fn[name] = JQUERY_NO_CONFLICT;
        return plugin.jQueryInterface;
    }
}

/**   jQuery   **/
import $ from 'jquery';
window.$ = window.jQuery = $;


/**   Bootstrap   **/
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

Object.keys(bootstrap).forEach(key => {
    defineJQueryPlugin(bootstrap[key]);
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))


/**   Alpine.js   **/

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
