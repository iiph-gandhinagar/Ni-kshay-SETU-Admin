import AppListing from "../app-components/Listing/AppListing";

Vue.component("subscriber-listing", {
    mixins: [AppListing],
    props:['district','all_blocks','health_facility', 'session_search'],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        // console.log("date --->" + params.get("from_date")? params.get("from_date"):'');
        // console.log("date --->" + params.get("to_date")? params.get("to_date"):'');
        console.log("district ----->" + params.get("district_id[]")? params.get("district_id[]"):"");
        return {
            
            form: {
                select_cadre: "",
                select_state: params.get("state_id")? params.get("state_id"):"",
                select_district: params.get("district_id[]") && params.get("district_id[]") != 0 ? params.get("district_id[]"):[],
                select_block: params.get("block_id")? params.get("block_id"):"",
                select_health_facility: "",
                district:[],
                block_options: [],
                block:[],
                health_facility:[],
                from_date: params.get("from_date")? params.get("from_date"):"",
                to_date: params.get("to_date")? params.get("to_date"):"",
                select_user_app_version:'',
                // date : params.get("from_date")? params.get("from_date"):'',
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
            search: '',
        };
        
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    },
    methods: {
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        },
        getStateDistrict() {
            // const url = new URL(window.location);
            // url.searchParams.set('state_id', this.form.select_state);
            // url.searchParams.set('district_id[]', []);
            // window.history.pushState({}, '', url);
            // console.log("district id ------>",this.form.select_state);
            
            this.form.district = this.district.filter(v=>v.state_id == this.form.select_state).map(title=>title,id=>id);
            console.log(this.form.district);
            console.log(this.form.select_state);
        },
        getDistrictBlock() {
            // const url = new URL(window.location);
            // for (let index = 0; index < this.form.select_district.length; index++) {
            //     const element = this.form.select_district[index];
            //     if(index ==0){

            //         url.searchParams.set('district_id[]', element);
            //     }else{
            //         url.searchParams.append('district_id[]', element);
            //     }
            //     window.history.pushState({}, '', url);
            // }
           
            console.log("district ---->",this.form.select_district,this.form.select_district.length);
            if(typeof this.form.select_district != "string"){
                let districtIds = this.form.select_district;
            
                if (districtIds.length > 0) {
                    this.form.block = this.all_blocks.filter(el => {
                            return districtIds.find(element => {
                                return element === el.district_id;
                            });
                        });
                    // this.form.block = this.all_blocks.filter(v=>v.state_id == this.form.select_state && v.district_id == splitted.includes(v.id.toString())).map(title=>title,id=>id);
                    // this.form.block_options = this.form.filtered_district;
                }
            }
            else{
                if(this.form.select_district.length > 0){
                    
                    this.form.block = this.all_blocks.filter(v=>v.state_id == this.form.select_state && v.district_id == this.form.select_district).map(title=>title,id=>id);
                }else{
                    this.form.block = "";
                }
            }
        },
        getBlockHealthFacility(){
            // const url = new URL(window.location);
            // url.searchParams.set('block_id', this.form.select_block);
            // window.history.pushState({}, '', url);
            this.form.health_facility = this.health_facility.filter(
                v=>v.state_id == this.form.select_state && v.district_id == this.form.select_district && v.block_id == this.form.select_block)
                .map(title=>title,id=>id);
        },
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
        leaderBoardDetails: function (event){
            console.log(event);
            $('#payload_data').empty();
            $('#payload_data').html(`<div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr><th> Level </th><td>${ event?.lb_subscriber_rankings?.lb_level?.level } </td></tr>
                                    <tr><th> Badge </th><td> ${ event?.lb_subscriber_rankings?.lb_badge?.badge } </td></tr> 
                                    <tr><th> Minutes Spent </th><td> ${ (event?.lb_subscriber_rankings?.mins_spent_count) /60 } </td></tr> 
                                    <tr><th> Sub Module Usage Count </th><td> ${ event?.lb_subscriber_rankings?.sub_module_usage_count } </td></tr> 
                                    <tr><th> App Opened Cout </th><td> ${ event?.lb_subscriber_rankings?.App_opended_count} </td></tr> 
                                    <tr><th> Chatbot Usage </th><td> ${ event?.lb_subscriber_rankings?.chatbot_usage_count } </td></tr> 
                                    <tr><th> Resource Material Usage </th><td> ${ event?.lb_subscriber_rankings?.resource_material_accessed_count } </td></tr> 
                                    <tr><th> Total Task </th><td> ${ event?.lb_subscriber_rankings?.total_task_count } </td></tr>
                                    <tr><th> App Performance Percentage </th><td>${ event?.lb_subscriber_rankings?.total_task_count * 100 / 64}%</td></tr>
                                    </tbody>
                            </table>
                        </div>
                                `);
            $('#payload_details').modal('show');  
        },
        
        sendOtp: function sendOtp(url) {
            console.log("url-->"+url)
            var _this7 = this;
  
            this.$modal.show('dialog', {
                title: 'Warning!',
                text: 'Do you really want to Send Forgot Otp?',
                buttons: [{ title: 'No, cancel.' }, {
                    title: '<span class="btn-dialog btn-danger">Yes, Send Forogot Otp.<span>',
                    handler: function handler() {
                        _this7.$modal.hide('dialog');
                        axios.get(url).then(function (response) {
                            _this7.loadData();
                            _this7.$notify({ type: 'success', title: 'Success!', text: response.data.message ? response.data.message : 'Otp Send To subscriber' });
                        }, function (error) {
                            _this7.$notify({ type: 'error', title: 'Error!', text: error.response.data.message ? error.response.data.message : 'An error has occured.' });
                        });
                    }
                }]
            });
        },
    },
    beforeMount() {
        this.getSerchFilter(0);
        this.getStateDistrict();
        this.getDistrictBlock();
        this.getBlockHealthFacility();
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});
