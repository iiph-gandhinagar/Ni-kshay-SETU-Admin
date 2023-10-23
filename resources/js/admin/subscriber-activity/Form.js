import AppForm from '../app-components/Form/AppForm';

Vue.component('subscriber-activity-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                user_id:  '' ,
                action:  '' ,
                ip_address:  '' ,
                plateform: '',
                
            }
        }
    }

});