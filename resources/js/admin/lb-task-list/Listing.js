import AppListing from '../app-components/Listing/AppListing';

Vue.component('lb-task-list-listing', {
    mixins: [AppListing],
    props: ['level','badge'],
    data: function data() {
        return {
            form: {
                select_level: '',
                select_badge: '',
                level:[],
                badge:[],
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
        }
    },
    methods: {
        getLevelBadge() {
            this.form.badge = this.badge.filter(v=>v.level_id == this.form.select_level).map(badge=>badge,id=>id);
        },
    },
    beforeMount() {
        this.getLevelBadge();
    },
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
});