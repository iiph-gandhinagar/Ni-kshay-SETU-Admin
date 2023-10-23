import AppForm from '../app-components/Form/AppForm';

Vue.component('lb-level-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                level:  this.getLocalizedFormDefaults() ,
                content:  this.getLocalizedFormDefaults() ,
                
            }
        }
    }

});