import AppForm from '../app-components/Form/AppForm';

Vue.component('flash-similar-app-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  '' ,
                sub_title:  '' ,
                href:  '' ,
                href_web:  '' ,
                href_ios:  '' ,
                order_index:  '' ,
                active:  false ,
                
            },
            mediaCollections: ["flash_app_icon"]
        }
    }

});