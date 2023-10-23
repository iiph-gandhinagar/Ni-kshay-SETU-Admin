import AppForm from '../app-components/Form/AppForm';

Vue.component('app-config-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                key:  '' ,
                value_json:  this.getLocalizedFormDefaults() , 
            }
        }
    }

});