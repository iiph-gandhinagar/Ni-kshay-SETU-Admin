import AppListing from "../app-components/Listing/AppListing";

Vue.component("subscriber-activity-listing", {
    mixins: [AppListing],
    props: ["subscribers"],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        return {
            form: {
                select_subscriber: "",
                select_cadre: "",
                select_country: "",
                select_state: params.get("state_id")
                    ? params.get("state_id")
                    : "",
                select_plateform: "",
                select_action: "",
                subscriber_data: [],
                from_date: params.get("from_date")
                    ? params.get("from_date")
                    : ""
            },
            orderBy: {
                column: "created_at",
                direction: "desc"
            }
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
        }
    }
});
