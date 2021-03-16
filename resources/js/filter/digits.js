import Vue from 'vue'

export const JustDigits = () => {
    Vue.directive('digitsonly', (el, binding, vnode) => {
        el.addEventListener('keydown', (e) => {
            if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode === 65 && e.ctrlKey === true) ||
                // Allow: Command+A
                (e.keyCode === 65 && e.metaKey === true) ||
                // Allow: Ctrl+V
                (e.keyCode === 86 && e.ctrlKey === true) ||
                // Allow: Command+V
                (e.keyCode === 86 && e.metaKey === true) ||
                // Allow: Ctrl+C
                (e.keyCode === 67 && e.ctrlKey === true) ||
                // Allow: Command+C
                (e.keyCode === 67 && e.metaKey === true) ||
                // Allow: Ctrl+X
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: Command+X
                (e.keyCode === 88 && e.metaKey === true) ||
                // Allow: Ctrl+Y
                (e.keyCode === 89 && e.ctrlKey === true) ||
                // Allow: Command+Y
                (e.keyCode === 89 && e.metaKey === true) ||
                // Allow: Ctrl+Z
                (e.keyCode === 90 && e.ctrlKey === true) ||
                // Allow: Command+Z
                (e.keyCode === 90 && e.metaKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault()
            }
        })
    });
};