import AppForm from '../app-components/Form/AppForm';

Vue.component('cgc-intervention-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                chapter_title:  '' ,
                video_title:  '' ,
                description:  '' ,
                assessment_id: '' ,
                reference_title: '' ,
            },
            mediaCollections: ['chapter_video','reference_links','video_image']
        }
    }

});