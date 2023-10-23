import AppListing from "../app-components/Listing/AppListing";

Vue.component("chatbot-activity-listing", {
    mixins: [AppListing],
    props: ["district", "all_blocks", "health_facility", "subscribers"],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        console.log(params);
        console.log("params------------->" +params.get("response")? params.get("response"):"");
        return {
            form: {
                select_subscriber: "",
                subscriber_data: [],
                select_action:'',
                select_response:params.get("response")? params.get("response"):"",
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
          
        };
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    },
    methods: {
        asyncFind(query) {
            this.isLoading = true;
            if (
                this.form.select_subscriber == "" ||
                this.form.select_subscriber == null ||
                this.form.select_subscriber == 0
            ) {
                if (query.length >= 3) {
                    this.form.subscriber_data = this.subscribers.filter(
                        option =>
                            option.name
                                .toLowerCase()
                                .startsWith(query.toLowerCase())
                    );
                    this.isLoading = false;
                }
            } else {
                this.form.subscriber_data = this.subscribers
                    .filter(v => v.id == this.form.select_subscriber)
                    .map(
                        name => name,
                        id => id
                    );
                this.isLoading = false;
            }
        },
        getResponse(e){
            // console.log("valueeee->"+this.form.select_response,e.target.value);
            location.href='chatbot-activities?response='+e.target.value;
        }
    }
});
