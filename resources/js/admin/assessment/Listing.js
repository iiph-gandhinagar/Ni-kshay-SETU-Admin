import AppListing from "../app-components/Listing/AppListing";

Vue.component("assessment-listing", {
    mixins: [AppListing],
    props: ['all_cadres','all_states', 'all_districts',"session_search"],
    data: function() {
        return {
            form: {
                all_cadres: this.all_cadres,
                select_cadre: []
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },  
            search: '',
        }
    }, 
    methods: {
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
        format_date(date) {
            let value = new Date();
            // console.log("todays date ------>",value);
            // console.log("moment(String(value)).format('Y-M-D H:m:s') ------>",moment(String(value)).format("Y-M-D H:m:s"));
            // console.log("item.from_date ----->",date);
            return moment(String(value)).format("Y-M-D H:m:s");
        },
        clearSession() {
            // console.log("inside clear session");
            axios.get("/admin/assessments").then(result => {
                console.log("session closed");
            });
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
        copyItem: function copyItem(url) {
            console.log("url-->" + url);
            var _this7 = this;

            this.$modal.show("dialog", {
                title: "Warning!",
                text: "Do you really want to copy this Assessment?",
                buttons: [
                    { title: "No, cancel." },
                    {
                        title:
                            '<span class="btn-dialog btn-danger">Yes, Copy.<span>',
                        handler: function handler() {
                            _this7.$modal.hide("dialog");
                            axios.get(url).then(
                                function(response) {
                                    _this7.loadData();
                                    _this7.$notify({
                                        type: "success",
                                        title: "Success!",
                                        text: response.data.message
                                            ? response.data.message
                                            : "Assessment successfully Copied."
                                    });
                                },
                                function(error) {
                                    _this7.$notify({
                                        type: "error",
                                        title: "Error!",
                                        text: error.response.data.message
                                            ? error.response.data.message
                                            : "An error has occured."
                                    });
                                }
                            );
                        }
                    }
                ]
            });
        },
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        }
        
    },
    beforeMount() {
        this.getSerchFilter(0);
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});
