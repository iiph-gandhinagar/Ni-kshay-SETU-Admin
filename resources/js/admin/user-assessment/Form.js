import AppForm from '../app-components/Form/AppForm';

Vue.component('user-assessment-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                assessment_id:  '' ,
                user_id:  '' ,
                total_marks:  '' ,
                obtained_marks:  '' ,
                attempted:  '' ,
                right_answers:  '' ,
                wrong_answers:  '' ,
                skipped:  '' ,
                
            }
        }
    }

});