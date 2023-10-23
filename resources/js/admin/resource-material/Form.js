import AppForm from '../app-components/Form/AppForm';

Vue.component('resource-material-form', {
    mixins: [AppForm],
    props: ["cadre", "state"],
    data: function() {
           let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
        return {
            form: {
                title:  this.getLocalizedFormDefaults(),
                type_of_materials: "",
                country_id: [],
                state: "",
                cadre: "",
                all_cadres: this.cadre,
                all_states: this.state,
                parent_id: params.get("master"),
                icon_type: "",
                index:  '' ,
                craeted_by:  "" ,
                
            },
            mediaCollections: ["material", "video_thumb"]
        };
    },
    methods: {
        selectAll: function onSuccess() {
            if (this.form.cadre.length == this.cadre.length)
                this.form.cadre = [];
            else this.form.cadre = this.cadre.map(v => v.id);
        },
        
        selectAllStates: function onSuccess() {
            if (this.form.state.length == this.form.all_states.length) {
                this.form.state = [];
            } else {
                this.form.state = this.form.all_states.map(v => v.id);
            }
        },
    }
});