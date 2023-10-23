import AppForm from '../app-components/Form/AppForm';

Vue.component('cadre-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  '' ,
                cadre_type: '',
            }
        }
    }

});