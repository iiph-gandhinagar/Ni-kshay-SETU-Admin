import AppForm from '../app-components/Form/AppForm';

Vue.component('static-enquiry-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                subject:  '' ,
                email:  '' ,
                message:  '' ,
                
            }
        }
    }

});