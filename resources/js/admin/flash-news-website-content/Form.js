import AppForm from '../app-components/Form/AppForm';

Vue.component('flash-news-website-content-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  '' ,
                source:  '' ,
                href:  '' ,
                author:  '' ,
                publish_date:  '' ,
                
            }
        }
    }

});