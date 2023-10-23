import AppForm from '../app-components/Form/AppForm';

Vue.component('flash-news-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                author:  '' ,
                description:  '' ,
                href:  '' ,
                order_index:  '' ,
                publish_date:  '' ,
                source:  '' ,
                title:  this.getLocalizedFormDefaults() ,
                
            },
            mediaCollections: ["flash_news_icon"]
        }
    }

});