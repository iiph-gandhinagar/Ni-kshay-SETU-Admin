import AppForm from '../app-components/Form/AppForm';

Vue.component('static-what-we-do-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                location:  this.getLocalizedFormDefaults() ,
                order_index:  '' ,
                title:  this.getLocalizedFormDefaults() ,
                
            },
            mediaCollections: ["cover_image"]
        }
    }

});