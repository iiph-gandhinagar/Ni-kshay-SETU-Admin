import AppForm from '../app-components/Form/AppForm';

Vue.component('enquiry-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                email:  '' ,
                phone:  '' ,
                subject:  '' ,
                message:  '' ,
                
            }
        }
    }

});