import AppListing from "../app-components/Listing/AppListing";

Vue.component("admin-user-listing", {
    mixins: [AppListing],
    methods: {
        resendActivation(url) {
            axios
                .get(url)
                .then(response => {
                    if (response.data.message) {
                        this.$notify({
                            type: "success",
                            title: "Success",
                            text: response.data.message
                        });
                    } else if (response.data.redirect) {
                        window.location.replace(response.data.redirect);
                    }
                })
                .catch(errors => {
                    if (errors.response.data.message) {
                        this.$notify({
                            type: "error",
                            title: "Error!",
                            text: errors.response.data.message
                        });
                    }
                });
        },
        impersonalLogin(url) {
            axios
                .get(url)
                .then(response => {
                    if (response.data.message) {
                        this.$notify({
                            type: "success",
                            title: "Success",
                            text: response.data.message
                        });
                    } else if (response.data.data.path) {
                        window.location.replace(response.data.data.path);
                    }
                })
                .catch(errors => {
                    if (errors.response.data.message) {
                        this.$notify({
                            type: "error",
                            title: "Error!",
                            text: errors.response.data.message
                        });
                    }
                });
        },
        getStateNamesByIds: function onSuccess(item) {
            if (isNaN(item.state)) {
                //array
                const splitted = item.state.split(",");
                if (splitted != "" && splitted.length > 0) {
                    return this.all_states
                        .filter(v => splitted.includes(v.id.toString()))
                        .map(item => item.title);
                }
            } else {
                return this.all_states
                    .filter(v => v.id == item.state)
                    .map(item => item.title);
            }
        }
    },
    props: {
        activation: {
            type: Boolean,
            required: true
        },
        all_states: []
    }
});