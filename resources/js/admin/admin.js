import './bootstrap';

import 'vue-multiselect/dist/vue-multiselect.min.css';
import flatPickr from 'vue-flatpickr-component';
import VueQuillEditor from 'vue-quill-editor';
import Notifications from 'vue-notification';
import Multiselect from 'vue-multiselect';
import VeeValidate from 'vee-validate';
import 'flatpickr/dist/flatpickr.css';
import VueCookie from 'vue-cookie';
import { Admin } from 'craftable';
import VModal from 'vue-js-modal'
import Vue from 'vue';
import draggable from "vuedraggable";
import ClassicEditor from './ckeditor';
import VueCkeditor from 'vue-ckeditor5';

import './app-components/bootstrap';
import './index';

import 'craftable/dist/ui';

Vue.component('multiselect', Multiselect);
Vue.component('draggable', draggable);
Vue.use(VeeValidate, {strict: true});
Vue.component('datetime', flatPickr);
Vue.use(VModal, { dialog: true, dynamic: true, injectModalsContainer: true });
Vue.use(VueQuillEditor);
Vue.use(Notifications);
Vue.use(VueCookie);
const options = {
  editors: {
    classic: ClassicEditor,
  },
  name: 'ckeditor'
}
Vue.use(VueCkeditor.plugin, options);

new Vue({
    mixins: [Admin],
});