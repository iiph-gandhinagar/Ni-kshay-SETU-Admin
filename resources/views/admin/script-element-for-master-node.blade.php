<script>
    function getMasterNodes(){
        let algoType = $('#redirect_algo_type').val();
        // console.log('load with value',$('#redirect_algo_type').val());
        $('#redirect_node_id').empty();
        $('#redirect_node_id').append(`<option class="form-control" value="">Select Redirect Node</option>`);
        if(algoType && algoType != '')
        {
            $.get(`/get-master-node-by-type/${algoType}`, function(data){
                // console.log(data);
                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    $('#redirect_node_id').append(`<option class="form-control" value="${a.id}">${a.title}</option>`);
                }
            });
        }
    }

    window.onload = function(){
        getMasterNodes();
    }
</script>