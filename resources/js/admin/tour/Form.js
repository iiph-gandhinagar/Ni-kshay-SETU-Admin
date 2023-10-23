import AppForm from '../app-components/Form/AppForm';

Vue.component('tour-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  this.getLocalizedFormDefaults() ,
                active:  false ,
                default:  false ,
                
            }
        }
    }

});