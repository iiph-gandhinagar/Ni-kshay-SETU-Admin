import AppForm from '../app-components/Form/AppForm';

Vue.component('survey-master-question-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                option1:  this.getLocalizedFormDefaults() ,
                option2:  this.getLocalizedFormDefaults() ,
                option3:  this.getLocalizedFormDefaults() ,
                option4:  this.getLocalizedFormDefaults() ,
                order_index:  '' ,
                question:  this.getLocalizedFormDefaults() ,
                survey_master_id:  '' ,
                type:  '' ,
                
            }
        }
    }

});