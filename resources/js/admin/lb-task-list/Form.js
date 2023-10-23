import AppForm from '../app-components/Form/AppForm';

Vue.component('lb-task-list-form', {
    mixins: [AppForm],
    props:['lb_badge'],
    data: function() {
        return {
            form: {
                level:  '' ,
                badges:  '' ,
                mins_spent:  '' ,
                sub_module_usage_count:  '' ,
                App_opended_count:  '' ,
                chatbot_usage_count:  '' ,
                resource_material_accessed_count:  '' ,
                total_task:  '' ,
                badge : [],
            }
        }
    },

    methods:{
        getBadges(){
            // console.log(this.form.badges);
            // console.log(this.form.level);
            let levelId = this.form.level;
            this.form.badge = this.lb_badge.filter(el => {
                // console.log(this.form.level);
                    return levelId.id == el.level_id
                });
        }
    },
    beforeMount() {
        this.getBadges();
    }

});