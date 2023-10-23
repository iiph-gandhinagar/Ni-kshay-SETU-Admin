import AppForm from '../app-components/Form/AppForm';

Vue.component('t-module-master-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                
            }
        }
    }

});