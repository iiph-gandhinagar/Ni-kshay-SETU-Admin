import AppListing from "../app-components/Listing/AppListing";

Vue.component("user-assessment-listing", {
    mixins: [AppListing],
    props: [
        "all_cadres",
        "district",
        "all_blocks",
        "health_facility",
        "subscribers"
        // 'select_assessment','select_subscriber','select_cadre','item'
    ],

    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        return {
            form: {
                all_cadres: this.all_cadres,
                select_assessment: "",
                select_subscriber: "",
                select_cadre: "",
                select_country: "",
                select_state: params.get("state_id")
                    ? params.get("state_id")
                    : "",
                select_district: params.get("district_id")
                    ? params.get("district_id")
                    : "",
                select_block: params.get("block_id_id")
                    ? params.get("block_id_id")
                    : "",
                select_health_facility: "",
                district: [],
                block: [],
                subscriber_data: [],
                health_facility: [],
                date: params.get("date") ? params.get("date") : "",
                from_date: params.get("from_date")
                    ? params.get("from_date")
                    : "",
                to_date: params.get("to_date") ? params.get("to_date") : ""
            },
            orderBy: {
                column: "created_at",
                direction: "desc"
            }
        };
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
        getStateDistrict() {
            this.form.district = this.district
                .filter(v => v.state_id == this.form.select_state)
                .map(
                    title => title,
                    id => id
                );
            // axios
            //     .get("/get-distirct/" + this.form.select_state)
            //     .then(result => {
            //         this.form.district = result.data;
            //         // (this.form.select_district = result.data.bpi)
            //     });
            // .then(response => (this.select_district = response.data.bpi));
        },
        getDistrictBlock() {
            this.form.block = this.all_blocks
                .filter(
                    v =>
                        v.state_id == this.form.select_state &&
                        v.district_id == this.form.select_district
                )
                .map(
                    title => title,
                    id => id
                );
            // axios
            //     .get("/get-block/" + this.form.select_state + "/" + this.form.select_district)
            //     .then(result => {
            //         this.form.block = result.data;
            //     });
        },
        getBlockHealthFacility() {
            this.form.health_facility = this.health_facility
                .filter(
                    v =>
                        v.state_id == this.form.select_state &&
                        v.district_id == this.form.select_district &&
                        v.block_id == this.form.select_block
                )
                .map(
                    title => title,
                    id => id
                );
            // axios
            //     .get("/get-health-facility/" + this.form.select_state + "/" + this.form.select_district + "/" + this.form.select_block)
            //     .then(result => {
            //         this.form.health_facility = result.data;
            //     });
        }
    },
    beforeMount() {
        this.getStateDistrict();
        this.getDistrictBlock();
        this.getBlockHealthFacility();
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    }
});
