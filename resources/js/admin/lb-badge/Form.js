import AppForm from '../app-components/Form/AppForm';

Vue.component('lb-badge-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                level_id:  '' ,
                badge:  this.getLocalizedFormDefaults() ,
                
            }
        }
    }

});