import AppForm from "../app-components/Form/AppForm";

Vue.component("user-notification-form", {
    mixins: [AppForm],
    props: ["subscriber","cadre", "state", "district","assessment","resource_material","case_definition","dignosis_algo","cgc_algo","differential_care_algo","guidance_on_adr","latent_tb_infection","treatment_algo","dynamic_algo"],
    data: function() {

        return {
            form: {
                title: "",
                description: "",
                type: "",
                country_id:"",
                user_id: [],
                all_subscriber: this.subscriber,
                all_states: this.state,
                all_cadres: this.cadre,
                list: this.subscriber,
                state_id: [],
                district_id: [],
                cadre_type: "",
                cadre_id: [],
                options: [],
                filtered_cadres: [],
                district_options: [],
                filtered_district: [],
                message: '',
                is_deeplinking:  false ,
                automatic_notification_type:  '' ,
                type_title:  '' ,
                list_title : [],
            },
            active:false,
        };
    },
    methods: {
        selectAll: function onSuccess() {
            // console.log(this.form.all_subscriber);
            if (this.form.user_id.length == this.subscriber.length)
                this.form.user_id = [];
            else this.form.user_id = this.subscriber;
        },

        selectAllCadres: function onSuccess() {
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
        mouseOver: function(){
            this.active = true;      
        },
        nameWithPhoneNumber({name,phone_no}){
             return `${name} — [${phone_no}]`
        },
        getTitleListing: function(){
            this.form.list_title = [];
            this.form.type_title = "";
            if(this.form.automatic_notification_type == "Assessment"){
                console.log("assessment ----->",this.assessment);
                this.form.list_title = this.assessment.map(title=>title,id=>id);
                console.log(this.form.list_title);
            }
            else if(this.form.automatic_notification_type == "Resource Material"){
                this.form.list_title = this.resource_material.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Case Definitions"){
                this.form.list_title = this.case_definition.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Diagnosis Algorithms"){
                this.form.list_title = this.dignosis_algo.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "NTEP Interventions Algorithms"){
                this.form.list_title = this.cgc_algo.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Differential Care Algorithms"){
                this.form.list_title = this.differential_care_algo.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "PMTPT"){
                this.form.list_title = this.latent_tb_infection.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Treatment Algorithms"){
                this.form.list_title = this.treatment_algo.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Guidance On Adverse Drug Reactions"){
                this.form.list_title = this.guidance_on_adr.map(title=>title,id=>id);
            }
            else if(this.form.automatic_notification_type == "Dynamic Algorithm"){
                this.form.list_title = this.dynamic_algo.map(title=>title,id=>id);
            }
        }
    },
    beforeMount() {
        //window on load
        this.getCadres();
        this.getDistrictList();
    }
});
