import AppForm from '../app-components/Form/AppForm';

Vue.component('key-feature-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                description:  this.getLocalizedFormDefaults() ,
                order_index:  '' ,
                title:  this.getLocalizedFormDefaults() ,
                
            },
            mediaCollections: ["icon","icon_bg"]
        }
    }

});