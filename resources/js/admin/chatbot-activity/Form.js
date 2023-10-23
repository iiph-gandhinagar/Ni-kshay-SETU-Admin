import AppForm from '../app-components/Form/AppForm';

Vue.component('chatbot-activity-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                user_id:  '' ,
                action:  '' ,
                payload:  '' ,
                plateform:  '' ,
                ip_address:  '' ,
                tag_id:  '' ,
                question_id:  '' ,
                like:  '' ,
                dislike:  '' ,
                
            }
        }
    }

});