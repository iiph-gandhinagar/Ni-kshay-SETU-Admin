import AppForm from '../app-components/Form/AppForm';

Vue.component('user-feedback-history-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                subscriber_id:  '' ,
                feedback_id:  '' ,
                ratings:  '' ,
                review:  '' ,
                skip:  false ,
                
            }
        }
    }

});