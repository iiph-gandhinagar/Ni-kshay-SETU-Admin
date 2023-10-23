import AppListing from '../app-components/Listing/AppListing';

Vue.component('activity-log-listing', {
    mixins: [AppListing],
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
        activityLogs: function (event){
            $('#payload_data').empty();
            $('#payload_data').html(`<div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr><th> Id </th><td>${ event.id } </td></tr>
                                    <tr><th> Description </th><td> ${ event.description } </td></tr>
                                    <tr><th> Type </th><td> ${ event.subject_type } </td></tr> 
                                    <tr><th> Causer Name </th><td> ${ event.admin_user ? event.admin_user.first_name:''} </td></tr>
                                    <tr><th> Properties </th><td>${ event.properties}</td></tr>
                                    </tbody>
                            </table>
                        </div>
                                `);
            $('#payload_details').modal('show');  
        }
    }
});