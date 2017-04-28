/**
 * @file retina.js
 */

/**
 * Sets a cookie for devices with HDPI displays.
 */
document.addEventListener("DOMContentLoaded", function(event) {
    //check if browser is retina and set cookie
    if((window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio) > 1) {
        document.cookie='HTTP_IS_RETINA=1;path=/';
    }
});