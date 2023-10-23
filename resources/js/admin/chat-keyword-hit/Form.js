import AppForm from '../app-components/Form/AppForm';

Vue.component('chat-keyword-hit-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                keyword_id:  '' ,
                subscriber_id:  '' ,
                
            }
        }
    }

});