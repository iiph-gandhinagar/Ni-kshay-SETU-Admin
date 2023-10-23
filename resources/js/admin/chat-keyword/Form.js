import AppForm from '../app-components/Form/AppForm';

Vue.component('chat-keyword-form', {
    mixins: [AppForm],
    props: ["submodules"],
    data: function() {
        return {
            form: {
                title:  this.getLocalizedFormDefaults() ,
                hit:  '' ,
                modules:  '' ,
                sub_modules:  '' ,
                resource_material:  '' ,
                custom_ordering:  '' ,
                sub_module_options: [],
                filtered_sub_module: []
            }
        }
    },
    methods: {
        getSubModules: function() {
            this.form.sub_modules = [];
            this.getSubModuleList();
        },

        getSubModuleList: function() {
            this.form.sub_module_options = [];
            this.form.filtered_sub_module = [];
            let moduleIds = this.form.modules; //.map(v => v.id);
            if (moduleIds.length > 0) {
                this.form.filtered_sub_module = this.submodules.filter(el => {
                    return moduleIds.find(element => {
                        return element.id === el.module_id;
                    });
                });

                this.form.sub_module_options = this.form.filtered_sub_module;
            }
        }
    },
    beforeMount() {
        //window on load
        this.getSubModuleList();
    }

});