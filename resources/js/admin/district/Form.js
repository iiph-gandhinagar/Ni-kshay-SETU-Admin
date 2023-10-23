import AppForm from '../app-components/Form/AppForm';

Vue.component('district-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                state_id:  '' ,
                title:  '' ,
                country_id: [],
            }
        }
    }

});