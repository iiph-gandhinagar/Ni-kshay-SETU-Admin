import AppForm from '../app-components/Form/AppForm';

Vue.component('assessment-certificate-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  '' ,
                top:  '' ,
                left:  '' ,
                
            },
            mediaCollections: ["assessment_certificate"]
        }
    }

});