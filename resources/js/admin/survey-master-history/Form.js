import AppForm from '../app-components/Form/AppForm';

Vue.component('survey-master-history-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                answer:  '' ,
                survey_id:  '' ,
                survey_question_id:  '' ,
                user_id:  '' ,
                
            }
        }
    }

});