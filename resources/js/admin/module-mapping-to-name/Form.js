import AppForm from '../app-components/Form/AppForm';

Vue.component('module-mapping-to-name-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                module_name:  '' ,
                mapping_name:  '' ,
                
            }
        }
    }

});