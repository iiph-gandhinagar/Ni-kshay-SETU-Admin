import AppForm from "../app-components/Form/AppForm";

Vue.component("t-training-tag-form", {
    mixins: [AppForm],
    props: ["submodules"],
    data: function() {
        return {
            form: {
                tag: "",
                pattern: [],
                is_fix_response: false,
                like_count: "",
                dislike_count: "",
                // response:  [] ,
                response: this.getLocalizedFormDefaults(),
                questions: "",
                modules: "",
                sub_modules: "",
                resource_material: "",
                sub_module_options: [],
                filtered_sub_module: [],
                draggable_questions:[]
            },
            options: [],
            response_options: []
        };
    },
    view: function() {
        return {
            form: {
                tag: "",
                pattern: [],
                is_fix_response: false,
                like_count: "",
                dislike_count: "",
                // response:  [] ,
                response: this.getLocalizedFormDefaults(),
                questions: "",
                modules: "",
                sub_modules: "",
                resource_material: "",
                sub_module_options: [],
                filtered_sub_module: [],
                draggable_questions:[]
            },
            options: [],
            response_options: []
        };
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
        },

        addTag: function(newTag) {
            console.log(this.form.pattern);
            let tag_repeated = -1;
            if(this.form.pattern.length > 0 && this.form.pattern != ''){
                tag_repeated = this.form.pattern.findIndex(item => $.trim(newTag).toLowerCase() === item.toLowerCase());
            }
           
            console.log(tag_repeated);
            if (newTag.includes("|")) {
                alert('Please Add pattern without "|" ');
            } else if(tag_repeated != -1){
                alert(newTag + ' Pattern is Repated!!');
            }else {
                this.options.push($.trim(newTag));
                this.form.pattern.push($.trim(newTag));
            }
        },

        addResponseTag: function(newResTag) {
           
            if (newResTag.includes("|")) {
                alert('Please Add response without "|" ');
            } else {
                this.response_options.push(newResTag);
                this.form.response.push(newResTag);
            }
        },
        checkMove: function(e) {
            console.log("inside check move");
            console.log("Future index: " + e.draggedContext.futureIndex);
        }
    },
    beforeMount() {
        //window on load
        this.getSubModuleList();
    }
});
