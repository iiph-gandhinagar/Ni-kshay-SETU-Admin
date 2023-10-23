import AppForm from '../app-components/Form/AppForm';

Vue.component('lb-sub-module-usage-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                subscriber_id:  '' ,
                sub_module:  '' ,
                total_time:  '' ,
                mins_spent:  '' ,
                completed_flag:  false ,
                
            }
        }
    }

});