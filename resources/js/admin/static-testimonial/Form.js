import AppForm from '../app-components/Form/AppForm';

Vue.component('static-testimonial-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                description:  this.getLocalizedFormDefaults() ,
                name:  this.getLocalizedFormDefaults() ,
                order_index:  '' ,
                
            },
            mediaCollections: ["icon"]
        }
    }

});