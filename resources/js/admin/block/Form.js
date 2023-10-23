import AppForm from '../app-components/Form/AppForm';

Vue.component('block-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                state_id:  '' ,
                district_id:  '' ,
                title:  '' ,
                country_id: [],
            }
        }
    }

});