import AppForm from '../app-components/Form/AppForm';

Vue.component('t-sub-module-master-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                module_id:  '' ,
                existing_module_ref:  '' ,
                
            }
        }
    }

});