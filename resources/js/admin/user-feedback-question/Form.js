import AppForm from '../app-components/Form/AppForm';

Vue.component('user-feedback-question-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                feedback_question:  this.getLocalizedFormDefaults() ,
                feedback_description:  this.getLocalizedFormDefaults() ,
                feedback_value:  '' ,
                feedback_time:  '' ,
                feedback_type:  '' ,
                feedback_days:  '' ,
                is_active:  false ,
                
            },
            mediaCollections: ["feedback_question_icon"]
        }
    }

});