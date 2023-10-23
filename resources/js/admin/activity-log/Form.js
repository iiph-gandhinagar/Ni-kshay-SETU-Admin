import AppForm from '../app-components/Form/AppForm';

Vue.component('activity-log-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                log_name:  '' ,
                description:  '' ,
                subject_type:  '' ,
                subject_id:  '' ,
                causer_type:  '' ,
                causer_id:  '' ,
                properties:  this.getLocalizedFormDefaults() ,
                
            }
        }
    }

});