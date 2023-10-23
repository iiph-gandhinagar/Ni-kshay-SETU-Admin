import { unset } from "lodash";
import AppForm from "../app-components/Form/AppForm";

Vue.component("assessment-form", {
    mixins: [AppForm],
    props: ["cadre", "state", "district"],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        return {
            form: {
                // assessment_title:  '' ,
                time_to_complete: "",
                cadre_id: "",
                state_id: "",
                country_id: "",
                assessment_title: this.getLocalizedFormDefaults(),
                assessment_type:  '' ,
                from_date:  '' ,
                to_date:  '' ,
                initial_invitation:  false ,
                all_cadres: this.cadre,
                filtered_cadres: [],
                all_states: this.state,
                activated: false,
                district_id: "",
                cadre_type: "",
                options: [],
                district_options: [],
                filtered_district: [],
                ids:params.get("ids") ? params.get("ids") : '',
                message: '',
                certificate_type: '',
            }
        };
    },
    methods: {
        selectAll: function onSuccess() {
            if (this.form.cadre_id.length == this.form.filtered_cadres.length) {
                this.form.cadre_id = [];
            } else {
                this.form.cadre_id = this.form.filtered_cadres; //.map(v => v.id);
            }
        },

        getCadresOnChangeOfType: function() {
            this.form.cadre_id = [];
            this.getCadres();
        },

        getCadres: function() {
            this.form.options = [];
            this.form.filtered_cadres = [];
            let cadre_type = this.form.cadre_type;

            if (this.form.cadre_type == "All") {
                this.form.filtered_cadres = this.cadre;
            } else if (
                this.form.cadre_type != "All" &&
                this.form.cadre_type != ""
            ) {
                this.form.filtered_cadres = this.cadre.filter(function(e) {
                    return e.cadre_type == cadre_type;
                });
            }
            this.form.options = this.form.filtered_cadres;
        },
        
        selectAllStates: function onSuccess() {
            if (this.form.state_id.length == this.form.all_states.length) {
                this.form.state_id = [];
            } else {
                this.form.state_id = this.form.all_states;
            }
            this.getDistrictOnChangeOfState();
        },

        getDistrictOnChangeOfState: function() {
            this.form.district_id = [];
            this.getDistrictList();
        },

        selectAllDistrict: function onSuccess() {
            if (
                this.form.district_id.length ==
                this.form.filtered_district.length
            ) {
                this.form.district_id = [];
            } else {
                this.form.district_id = this.form.filtered_district; //.map(v => v.id);
            }
        },

        getDistrictList: function() {
            this.form.district_options = [];
            this.form.filtered_district = [];
            let stateIds = this.form.state_id; //.map(v => v.id);
            if (stateIds.length > 0) {
                this.form.filtered_district = this.district.filter(el => {
                    return stateIds.find(element => {
                        return element.id === el.state_id;
                    });
                });

                this.form.district_options = this.form.filtered_district;
            }
        },
    },
    beforeMount() {
        //window on load
        this.getCadres();
        this.getDistrictList();
    }
});
