import AppListing from "../app-components/Listing/AppListing";

Vue.component("resource-material-listing", {
    mixins: [AppListing],
    props: ["all_cadres", "all_states", "session_search"],
    data: function() {
        return {
            form: {
                all_cadres: this.all_cadres,
                all_states: 1
            },
            orderBy: {
                column: "created_at",
                direction: "desc"
            },
            search: ""
        };
    },
    methods: {
        getCadreNamesByIds: function onSuccess(item) {
            if (isNaN(item.cadre)) {
                //array
                const splitted = item.cadre.split(",");
                // console.log('splitted',splitted);
                return this.all_cadres
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.title);
            } else {
                return this.all_cadres
                    .filter(v => v.id == item.cadre)
                    .map(item => item.title);
            }
        },
        getStateNamesByIds: function onSuccess(item) {
            if (isNaN(item.state)) {
                //array
                const splitted = item.state.split(",");
                // console.log('splitted',splitted);
                return this.all_states
                    .filter(v => splitted.includes(v.id.toString()))
                    .map(item => item.title);
            } else {
                return this.all_states
                    .filter(v => v.id == item.state)
                    .map(item => item.title);
            }
        },
        getSerchFilter(ajax) {
            if (ajax && ajax == 1) {
                this.session_search = this.search;
            } else {
                this.search = this.session_search;
            }
        },
        clearSession() {
            console.log("inside clear session");
            axios.get("/admin/resource-materials").then(result => {
                console.log("session closed");
            });
        },
        idStore(id) {
            localStorage.setItem("id1", id);
        },
        getId() {
            return localStorage.getItem("id1");
        }
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    },
    beforeMount() {
        this.getSerchFilter(0);
        if (window.location.href != localStorage.getItem("url")) {
            localStorage.setItem("scrollpos", "");
            localStorage.setItem("id1", "");
        }
    }
});
