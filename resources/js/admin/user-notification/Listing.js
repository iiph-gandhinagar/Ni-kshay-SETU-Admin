import AppListing from "../app-components/Listing/AppListing";

Vue.component("user-notification-listing", {
    mixins: [AppListing],
    props: ["all_subscriber", "all_states", "all_cadres","assessment","resource_material","case_definition","dignosis_algo","treatment_algo","guidance_on_adr","latent_tb_infection","differential_care_algo","cgc_algo","dynamic_algo_master"],
    data: function data() {
        return {
            orderBy: {
                column: 'created_at',
                direction: 'desc',
                n:[],
            },    
        }
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss");
        },
        stringCount(string){
            return (string.length) > 300 ? string.substring(0, 300) + "....." : string;
        }
    },
    methods: {
        getSubscriberNamesByIds: function onSuccess(item) {
            if (isNaN(item.user_id)) {
                //array
                const splitted = item.user_id.split(",");
                // console.log("splitted", splitted);
                if(splitted.length <= 5){
                    return this.all_subscriber
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.name);
                }
                
            } else {
                return this.all_subscriber
                    .filter(v => v.id == item.user_id)
                    .map(item => item.name);
            }
        },
        getUserNameByIds: function onSuccess(item) {
            if (isNaN(item.user_id)) {
                //array
                const splitted = item.user_id.split(",");
                // console.log("splitted", splitted);
                    return this.all_subscriber
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.name);
                
            } else {
                return this.all_subscriber
                    .filter(v => v.id == item.user_id)
                    .map(item => item.name);
            }
        },

        getSubscriberCounts:function onSuccess(item){
            const splitted = item.split(",");
            // console.log(splitted.length);
            return (splitted.length);
        },

        getCadreNamesByIds: function onSuccess(item) {
            if (isNaN(item.cadre_id)) {
                //array
                const splitted = item.cadre_id.split(",");
                // console.log('splitted',splitted);
                return this.all_cadres
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.title);
            } else {
                return this.all_cadres
                    .filter(v => v.id == item.cadre_id)
                    .map(item => item.title);
            }
        },

        getStateNamesByIds: function onSuccess(item) {
            if (isNaN(item.state_id)) {
                //array
                const splitted = item.state_id.split(",");
                // console.log('splitted',splitted);
                return this.all_states
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.title);
            } else {
                return this.all_states
                    .filter(v => v.id == item.state_id)
                    .map(item => item.title);
            }
        },
        clearSession() {
            console.log("inside clear session");
            axios
                .get("/admin/user-notifications")
                .then(result => {
                    console.log("session closed");
                });
        },
        userNotification: function (event){
            $('#payload_data').empty();
            $('#payload_data').html(`<div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr><th> Id </th><td>${ event.id } </td></tr>
                                    <tr><th> Title </th><td> ${ event.title } </td></tr> 
                                    <tr><th> Description </th><td> ${ event.description } </td></tr> 
                                    <tr><th> Type </th><td> ${ event.type } </td></tr> 
                                    <tr><th> Cadre Type </th><td> ${ (event.cadre_type != "" && event.cadre_type != null) ? event.cadre_type: "--"} </td></tr> 
                                    <tr><th> Country </th><td> ${ event.country_id == 1 ? "India": "--" } </td></tr> 
                                    <tr><th> States </th><td id="state_names"></td></tr>
                                    <tr><th> Cadre </th><td id="cadre_names"></td></tr>
                                    <tr><th> User </th><td id="user_names"></td></tr>
                                    <tr><th> Module </th><td>${ (event.automatic_notification_type != "" && event.automatic_notification_type != null) ? event.automatic_notification_type: "--"}</td></tr>
                                    <tr><th> Module Name </th><td id="module_name"></td></tr>
                                    <tr><th> Successful Notifications </th><td>${ event.successful_count }</td></tr>
                                    <tr><th> Failed Notifications </th><td>${ event.failed_count }</td></tr>
                                    </tbody>
                            </table>
                        </div>
                                `);
            if(this.getStateNamesByIds(event).length > 0){
                for (let i = 0; i < this.getStateNamesByIds(event).length; i++) {
                    // console.log(this.getStateNamesByIds(event)[i]);
                    $('#state_names').append(`
                        <span class="badge badge-success m-1 p-1" style="font-size:0.8rem;color:white">${this.getStateNamesByIds(event)[i]}</span>
                    `);
                }
            }else{
                $('#state_names').append(`--`);
            }

            if(this.getCadreNamesByIds(event).length > 0){
                for (let i = 0; i < this.getCadreNamesByIds(event).length; i++) {
                    // console.log(this.getCadreNamesByIds(event)[i]);
                    $('#cadre_names').append(`
                        <span class="badge badge-success m-1 p-1" style="font-size:0.8rem;color:white">${this.getCadreNamesByIds(event)[i]}</span>
                    `);
                }
            }else{
                $('#cadre_names').append(`--`);
            }
            // console.log("event type is---->",event.type);
            if(event.type == "public"){
                // console.log("inside public notification");

                $('#user_names').append(`All Users`);
            }else if(this.getUserNameByIds(event).length == 0){
                // console.log("inside length is 0");
                $('#user_names').append(`--`);
            }else if(this.getUserNameByIds(event).length > 50){
                $('#user_names').append(this.getUserNameByIds(event).length );
            }else if(this.getUserNameByIds(event).length <= 50){
                for (let i = 0; i < this.getUserNameByIds(event).length; i++) {
                    // console.log(this.getUserNameByIds(event)[i]);
                    $('#user_names').append(`
                        <span class="badge badge-success m-1 p-1" style="font-size:0.8rem;color:white">${this.getUserNameByIds(event)[i]}</span>
                    `);
                } 
            }else{
                // console.log("inside else");
                $('#user_names').append(`--`);
            }

            if(event.automatic_notification_type == "Resource Material" && event.type_title != ""){
                let rs =  this.resource_material
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Assessment" && event.type_title != ""){
                let rs =  this.assessment
                    .filter(v => v.id == event.type_title)
                    .map(item => item.assessment_title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Case Definitions" && event.type_title != ""){
                let rs =  this.case_definition
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Diagnosis Algorithms" && event.type_title != ""){
                let rs =  this.dignosis_algo
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Treatment Algorithms" && event.type_title != ""){
                let rs =  this.treatment_algo
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Guidance On Adverse Drug Reactions" && event.type_title != ""){
                let rs =  this.guidance_on_adr
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "PMTPT"){
                let rs =  this.latent_tb_infection
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Differential Care Algorithms" && event.type_title != ""){
                let rs =  this.differential_care_algo
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }
            else if(event.automatic_notification_type == "NTEP Interventions Algorithms" && event.type_title != ""){
                let rs =  this.cgc_algo
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else if(event.automatic_notification_type == "Dynamic Algorithm" && event.type_title != ""){
                let rs =  this.dynamic_algo_master
                    .filter(v => v.id == event.type_title)
                    .map(item => item.title);
                $('#module_name').append(rs);
            }else{
                $('#module_name').append('--');
            }
            
            
            $('#payload_details').modal('show');  
        }
    }
});
