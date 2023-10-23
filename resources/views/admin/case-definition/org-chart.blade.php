<div id="chart-container"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.1.1/js/jquery.orgchart.min.js" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.1.1/css/jquery.orgchart.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/org-chart.css') }}">

<script>
   (function($) {
  jQuery(function() {
   let masterId = location.search.split('master=')[1];
   // console.log('masterId---------',masterId);
   
   $.get('/get-case-definitions-dependent-nodes/'+masterId, function(data){
      var ds = data.data;
      var nodeTemplate = function(data) {
         if(data.description == null || data.description == 'NULL')
         {
            data.description = '';
         }
         return `
            <div class="title">${data.title}</div>
            <div class="content">${data.description}</div>
         `;
      };

      var oc = jQuery('#chart-container').orgchart({
         'data' : ds,
         'depth': 2,
         //   'nodeContent': 'title'
         'nodeTemplate': nodeTemplate
      });

      $(window).resize(function() {
         var width = $(window).width();
         if(width > 576) {
            oc.init({'verticalLevel': undefined});
         } else {
            oc.init({'verticalLevel': 2});
         }
      });
   });
  });
})(jQuery);
</script>