import AppListing from '../app-components/Listing/AppListing';

Vue.component('survey-master-listing', {
    mixins: [AppListing],
    props: ["all_cadres", "all_states", "all_districts","survey_question"],
    data: function data() {
        return {
            form: {
                all_cadres: this.all_cadres,
                select_cadre: []
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
        }
    },
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    methods:{
        clearSession() {
            console.log("inside clear session");
            axios.get("/admin/assessments").then(result => {
                console.log("session closed");
            });
        },
        deleteItem: function deleteItem(url, survey_master_id) {
            let count = this.survey_question.filter(
                (v) => v.survey_master_id == survey_master_id
            ).length;
            if (count > 0) {
                var _this7 = this;

                this.$modal.show("dialog", {
                    title: "Warning!",
                    text: `Under this Survey ${count} Survey Questions are Store. Do you really want to delete this item?`,
                    buttons: [
                        { title: "No, cancel." },
                        {
                            title: '<span class="btn-dialog btn-danger">Yes, delete.<span>',
                            handler: function handler() {
                                _this7.$modal.hide("dialog");
                                axios.delete(url).then(
                                    function (response) {
                                        _this7.loadData();
                                        _this7.$notify({
                                            type: "success",
                                            title: "Success!",
                                            text: response.data.message
                                                ? response.data.message
                                                : "Item successfully deleted.",
                                        });
                                    },
                                    function (error) {
                                        _this7.$notify({
                                            type: "error",
                                            title: "Error!",
                                            text: error.response.data.message
                                                ? error.response.data.message
                                                : "An error has occured.",
                                        });
                                    }
                                );
                            },
                        },
                    ],
                });
            }else{
                var _this7 = this;

            this.$modal.show("dialog", {
                title: "Warning!",
                text: "Do you really want to delete this item?",
                buttons: [
                    { title: "No, cancel." },
                    {
                        title: '<span class="btn-dialog btn-danger">Yes, delete.<span>',
                        handler: function handler() {
                            _this7.$modal.hide("dialog");
                            axios.delete(url).then(
                                function (response) {
                                    _this7.loadData();
                                    _this7.$notify({
                                        type: "success",
                                        title: "Success!",
                                        text: response.data.message
                                            ? response.data.message
                                            : "Item successfully deleted.",
                                    });
                                },
                                function (error) {
                                    _this7.$notify({
                                        type: "error",
                                        title: "Error!",
                                        text: error.response.data.message
                                            ? error.response.data.message
                                            : "An error has occured.",
                                    });
                                }
                            );
                        },
                    },
                ],
            });
            }
        },
    }
});