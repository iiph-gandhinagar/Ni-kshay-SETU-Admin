import AppForm from '../app-components/Form/AppForm';

Vue.component('static-resource-material-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                order_index:  '' ,
                title:  this.getLocalizedFormDefaults() ,
                type_of_materials:  '' ,
                
            },
            mediaCollections: ["material"]
        }
    }

});