import AppForm from '../app-components/Form/AppForm';

Vue.component('symptom-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                category:  '' ,
                symptoms_title:  this.getLocalizedFormDefaults() ,
                
            },
            mediaCollections: ['symptoms_image']
        }
    }

});