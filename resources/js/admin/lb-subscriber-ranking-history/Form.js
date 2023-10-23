import AppForm from '../app-components/Form/AppForm';

Vue.component('lb-subscriber-ranking-history-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                lb_subscriber_rankings_id:  '' ,
                subscriber_id:  '' ,
                level_id:  '' ,
                badge_id:  '' ,
                mins_spent_count:  '' ,
                sub_module_usage_count:  '' ,
                App_opended_count:  '' ,
                chatbot_usage_count:  '' ,
                resource_material_accessed_count:  '' ,
                
            }
        }
    }

});