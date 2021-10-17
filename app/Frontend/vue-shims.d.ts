// This is required so that we can import .vue files in .ts files.
// https://github.com/microsoft/typescript-vue-starter#single-file-components

declare module "*.vue" {
    import Vue from "vue";
    export default Vue;
}

// declare module '@fullcalendar/vue';
// declare module 'jquery';
// declare module 'doc-cookies';
// declare module 'moment-duration-format';