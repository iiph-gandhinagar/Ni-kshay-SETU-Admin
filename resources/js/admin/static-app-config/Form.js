import AppForm from '../app-components/Form/AppForm';

Vue.component('static-app-config-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                key:  '' ,
                type:  '' ,
                value_json:  this.getLocalizedFormDefaults() ,
                
            }
        }
    }

});