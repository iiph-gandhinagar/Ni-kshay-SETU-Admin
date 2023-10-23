import AppForm from '../app-components/Form/AppForm';

Vue.component('dynamic-algo-master-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                section:  '' ,
                active:  false ,
                
            },
            mediaCollections: ["node_icon"],
        }
    }

});