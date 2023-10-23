import AppForm from '../app-components/Form/AppForm';

Vue.component('role-has-permission-form', {
    mixins: [AppForm],
    props: ["permissions"],
    data: function() {
        return {
            form: {
                permission_id:  '' ,
                role_id:  '' ,
                all_permissions: this.permissions,
                
            }
        }
    }

});