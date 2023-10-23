let matrix_data, prepared_matrix_data = {};
let column_mapping_data;
function get_contributing_index(base_index, item, column_mapping, mat_color_obj, curr_col_pos) {
    let cont_obj = {}
    _.each(column_mapping[base_index], function(idx, id) {
        if (item[idx] !== undefined) {
            cont_obj[idx] = {
                'score': round_val(item[idx]),
                'color': d3.color(mat_color_obj[idx](round_val(item[idx]))).hex(),
                'col_pos': curr_col_pos + id + 1
            }
        }
    })
    return cont_obj
}
let mat_color_obj = {}
let mat_color_range = ["#8ac4db", "#8e95c5", "#9cd6dd","#bbe2d9"]
function prepare_matrix_data(filter_key, matrix_data, column_mapping_data, district_view) {
    let min_val = {}
      , max_val = {}
      , mat_data = []
    let mat_data_obj;
    if (filter_key == '') {
        mat_data_obj = matrix_data
    } else {
        mat_data_obj = matrix_data[filter_key]
    }
    _.each(Object.keys(mat_data_obj[0]), function(k) {
        min_val[k] = getMinValue(mat_data_obj, k)
        max_val[k] = getMaxValue(mat_data_obj, k)
        mat_color_obj[k] = d3.scaleLinear().domain([min_val[k], (min_val[k] + max_val[k]) / 2, max_val[k]]).range(mat_color_range)
    })
    let ordered_ind_list = ["points_notify", "points_notify_hiv", "points_udst", "points_sucess_rate", "points_npy", "points_drtb_patients", "points_expenditure", "points_chemo", "points_plhiv"]
    _.each(mat_data_obj, function(item) {
        let mat_obj = {}
        if (district_view === "1") {
            mat_obj['district_name'] = {
                "value": item['district_name'],
                "id": item['district_id']
            }
        } else {
            mat_obj['state'] = {
                "value": item['state'],
                "id": item['state_id']
            }
        }
        mat_obj['score'] = {
            "value": Math.round(item['score']),
            "color": d3.color(mat_color_obj['score'](Math.round(item['score']))).hex(),
            "col_pos": 1
        }
        let col_pos = 2
        _.each(ordered_ind_list, function(points, idx) {
            if (idx !== 0) {
                let prev_point = ordered_ind_list[idx - 1]
                col_pos = col_pos + (column_mapping_data[prev_point].length)
            }
            let pnts_cld = round_val(item[points])
            mat_obj[points] = {
                "value": pnts_cld,
                "col_pos": col_pos,
                "contributing": get_contributing_index(points, item, column_mapping_data, mat_color_obj, col_pos),
                "color": d3.color(mat_color_obj[points](pnts_cld)).hex()
            }
        })
        mat_data.push(mat_obj)
    });
    return mat_data;
}
function get_matrix_filter(matrix_data) {
    let year = matrix_data.year
    let matrix_filter = Object.keys(matrix_data)
    let index = matrix_filter.indexOf("year");
    if (index > -1) {
        matrix_filter.splice(index, 1);
    }
    return {
        year: year,
        matrix_filter: matrix_filter
    }
}
function draw_matrix_modal() {
    let get_mat_data_url = "get_state_matrix_data?quarter=" + options.quarter[0] + "&year=" + options.year[0]
    if (options["district_view"][0] == "1") {
        get_mat_data_url = "get_district_matrix_data?quarter=" + options.quarter[0] + "&year=" + options.year[0] + "&state_id=" + options.state_id[0]
    }
    ajaxchain_fetch(["get_column_mapping", get_mat_data_url]).ajaxchain_instance.on('done', function(e) {
        column_mapping_data = JSON.parse(e.response[1]);
        matrix_data = JSON.parse(e.response[2]);
        let filter_values = get_matrix_filter(matrix_data)
        _.each(filter_values.matrix_filter, function(data_key) {
            if (options['district_view'][0] === "1") {
                prepared_matrix_data[data_key] = prepare_matrix_data(data_key, matrix_data, column_mapping_data, "1")
            } else {
                prepared_matrix_data[data_key] = prepare_matrix_data(data_key, matrix_data, column_mapping_data, "0")
            }
        })
        $(".expand_matrix_modal").on('template', function() {
            $('tbody').scroll(function() {
                $('thead').css("left", -$("tbody").scrollLeft());
                $('thead th:nth-child(1)').css("left", $("tbody").scrollLeft());
                $('tbody td:nth-child(1)').css("left", $("tbody").scrollLeft());
            });
            $('.matrix_filter').removeClass("matrix_filter_highlighted");
            $('.matrix_filter[data-filter="' + options['quarter'][0] + '"]').addClass("matrix_filter_highlighted");
        }).template({
            year: filter_values.year,
            mat_data: prepared_matrix_data[options['quarter'][0]],
            matrix_filter: filter_values.matrix_filter,
            mat_view_name: mat_view_name
        });
    })
}
