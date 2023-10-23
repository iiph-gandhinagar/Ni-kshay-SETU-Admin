import AppForm from '../app-components/Form/AppForm';

Vue.component('user-app-version-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                user_id:  '' ,
                user_name:  '' ,
                app_version:  '' ,
                
            }
        }
    }

});