import AppForm from "../app-components/Form/AppForm";

Vue.component("admin-user-form", {
    mixins: [AppForm],
    props: ["states", "district", "cadre"],
    data: function() {
        return {
            form: {
                first_name: "",
                last_name: "",
                email: "",
                password: "",
                activated: false,
                forbidden: false,
                language: "",
                role_type: "",
                country: "",
                // cadre_type: "",
                state: [],
                district: "",
                cadre: "",
                all_states: this.states,
                district_options: [],
                options: [],
                filtered_cadres: [],
                filtered_district: [],
                district_options: []
            }
        };
    },
    methods: {
        selectAll: function onSuccess() {
            if (this.form.cadre.length == this.form.filtered_cadres.length) {
                this.form.cadre = [];
            } else {
                this.form.cadre = this.form.filtered_cadres; //.map(v => v.id);
            }
        },
        getCadresOnChangeOfType: function() {
            this.form.cadre_id = [];
            this.form.cadre = '';
            this.getCadres();
        },
        getCadres: function() {
            this.form.options = [];
            this.form.filtered_cadres = [];
            
            console.log('role_type', this.form.role_type);
            if( this.form.role_type == 'country_type'){
                this.form.filtered_cadres = this.cadre;
            }
            else if(this.form.role_type == 'state_type'){
                this.form.filtered_cadres = this.cadre.filter(function(e) {
                    return e.cadre_type == 'State_Level' || e.cadre_type == 'District_Level' || e.cadre_type == 'Block_Level' || e.cadre_type == 'Health-facility_Level';
                });
            }else if(this.form.role_type == 'district_type'){
                this.form.filtered_cadres = this.cadre.filter(function(e) {
                    return e.cadre_type == 'District_Level' || e.cadre_type == 'Block_Level' || e.cadre_type == 'Health-facility_Level';
                });
            }


            // if (this.form.cadre_type == "All") {
            //     this.form.filtered_cadres = this.cadre;
            // } else if (
            //     this.form.cadre_type != "All" &&
            //     this.form.cadre_type != ""
            // ) {
            //     this.form.filtered_cadres = this.cadre.filter(function(e) {
            //         return e.cadre_type == cadre_type;
            //     });
            // }
            this.form.options = this.form.filtered_cadres;
        },
        getDistrictOnChangeOfState: function() {
            this.form.district = "";
            this.getDistrictList();
        },
        selectAllStates: function onSuccess() {
            console.log('inside selectAllStates',this.form.state.length , this.form.all_states.length);
            if (this.form.state.length == this.form.all_states.length) {
                this.form.state = [];
            } else {
                this.form.state = this.form.all_states;
            }
            this.getDistrictOnChangeOfState();
        },

        getDistrictForOneState: function() {
            this.form.district_options = [];
            this.form.filtered_district = [];
            this.form.district = "";
            let stateIds = this.form.state; //.map(v => v.id);
            if (stateIds != "" && stateIds != 0) {
                this.form.filtered_district = this.district.filter(el => {
                    return stateIds.id === el.state_id;
                });

                this.form.district_options = this.form.filtered_district;
            }
        },

        getDistrictList: function() {
            this.form.district_options = [];
            this.form.filtered_district = [];
            let stateIds = this.form.state; //.map(v => v.id);
            if (stateIds.length > 0 && stateIds != null) {
                console.log("inside state Ids if");
                this.form.filtered_district = this.district.filter(el => {
                    return stateIds.find(element => {
                        return element.id === el.state_id;
                    });
                });

                this.form.district_options = this.form.filtered_district;
            } else if (stateIds != "" && stateIds != 0 && stateIds != null) {
                console.log("inside state Ids else if");
                this.form.filtered_district = this.district.filter(el => {
                    return stateIds.id === el.state_id;
                });

                this.form.district = this.form.filtered_district;
            } else {
                console.log("inside else part");
                this.form.district = this.form.district;
            }
        },
        selectAllDistrict: function() {
            if (
                this.form.district.length == this.form.filtered_district.length
            ) {
                console.log("insdie if of all district ----.");
                this.form.district = [];
            } else {
                console.log("insdie else of all district ----.");
                this.form.district = this.form.filtered_district; //.map(v => v.id);
            }
        },
        clearInputs: function() {
            this.form.country = "";
            this.form.state = [];
            this.form.district = "";
            this.form.cadre = "";
            this.form.cadre_type = "";
            this.form.all_states = this.states;
        }
    },
    beforeMount() {
        //window on load
        this.form.all_states = this.states;
        this.getDistrictList();
        this.getCadres();
    }
});
