import AppForm from '../app-components/Form/AppForm';

Vue.component('automatic-notification-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                description:  '' ,
                linking_url:  '' ,
                subscriber_id:  '' ,
                title:  '' ,
                type:  '' ,
                
            }
        }
    }

});