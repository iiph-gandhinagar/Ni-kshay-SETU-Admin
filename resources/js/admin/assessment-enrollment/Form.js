import AppForm from '../app-components/Form/AppForm';

Vue.component('assessment-enrollment-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                assessment_id:  '' ,
                user_id:  '' ,
                response:  '' ,
                send_inital_invitation:  false ,
                
            }
        }
    }

});