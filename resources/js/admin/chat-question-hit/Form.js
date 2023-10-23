import AppForm from '../app-components/Form/AppForm';

Vue.component('chat-question-hit-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                question_id:  '' ,
                subscriber_id:  '' ,
                session_token:  '' ,
                
            }
        }
    }

});