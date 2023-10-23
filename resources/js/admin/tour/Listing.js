import AppListing from '../app-components/Listing/AppListing';

Vue.component('tour-listing', {
    mixins: [AppListing],
    props: ['tour_slides'],
    data: function data() {
        return {
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
        deleteItem: function deleteItem(url, tour_id) {
            let count = this.tour_slides.filter(
                (v) => v.tour_id == tour_id
            ).length;
            if (count > 0) {
                var _this7 = this;

                this.$modal.show("dialog", {
                    title: "Warning!",
                    text: `Under this Tour ${count} tour slides are Store. Do you really want to delete this item?`,
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