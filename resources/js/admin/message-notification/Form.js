import AppForm from '../app-components/Form/AppForm';

Vue.component('message-notification-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
               material:'',
               message:'',
                
            },
            mediaCollections: ['material'],
        }
    }

});