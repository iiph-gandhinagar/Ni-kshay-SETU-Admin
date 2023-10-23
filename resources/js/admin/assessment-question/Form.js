import AppForm from '../app-components/Form/AppForm';

Vue.component('assessment-question-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                assessment_id:  '' ,
                // question:  '' ,
                // option1:  '' ,
                // option2:  '' ,
                // option3:  '' ,
                // option4:  '' ,
                category: '',
                correct_answer:  '' ,
                order_index:  '' ,
                question:  this.getLocalizedFormDefaults() ,
                option1:  this.getLocalizedFormDefaults() ,
                option2:  this.getLocalizedFormDefaults() ,
                option3:  this.getLocalizedFormDefaults() ,
                option4:  this.getLocalizedFormDefaults() ,
                
            }
        }
    }

});