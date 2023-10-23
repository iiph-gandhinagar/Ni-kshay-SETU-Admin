// Sentry.init({
//     dsn: 'https://c7b36781a0554e2cbeafd715a5cd651e@sentry.io/1759357'
// });
url = g1.url.parse(location.href);
let indicator_values,
    map_data,
    table_data,
    range_slider = "",
    g_state_id;
let national_avg = [];
let time_stamp = {};
let options = {
    state_id: ["IN"],
    district_view: url.searchKey.state_id ? ["1"] : ["0"],
    tab: ["overview"],
    m_names: ["false"],
    m_score: ["true"],
    m_annotations: ["false"],
    ind: ["points_notify"],
    quarter: ["Q3"],
    year: ["2019"],
    active_cal: ["quarter"],
    chart_type: ["score_rank"],
    score_type: ["abs_score"],
    view2: ["state"],
    view: ["state"],
    min_value: [],
    max_value: [],
    table_sort_state: ["rank", "asc"],
    district_id: [""],
    mat_filter_key: "Q3",
    mat_sort_key: "state",
    mat_sort_odr: "asc",
    tab_sort_odr: "asc"
};

function format_inr(x) {
    var negative = x < 0,
        str = String(negative ? -x : x),
        arr = [],
        i = str.indexOf("."),
        j;
    if (i === -1) {
        i = str.length;
    } else {
        for (j = str.length - 1; j > i; j--) {
            arr.push(str[j]);
        }
        arr.push(".");
    }
    i--;
    for (j = 0; i >= 0; i--, j++) {
        if (j > 2 && j % 2 === 1) {
            arr.push(",");
        }
        arr.push(str[i]);
    }
    if (negative) {
        arr.push("-");
    }
    return arr.reverse().join("");
}
let indicator_list;
// options = $.extend({}, options, url.searchList)

function slug_(text) {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w\\-]+/g, "")
        .replace(/\\-\\-+/g, "-")
        .replace(/^-+/, "")
        .replace(/-+$/, "");
}

function toCamelCase(str) {
    var final_str = str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
    return final_str;
}

function get_max_min_value(data) {
    map_data = data;
    options["min_value"] = [d3.min(d3.values(data))];
    options["max_value"] = [d3.max(d3.values(data))];
    create_ion_rangeslider();
}

function draw_india_map() {
    $("#chart").show();
    $('#state-national-count').removeAttr("style");
    $("#show-hide-map").empty();
    $("#map_slider_wrapper").show();
    $(".heat-map-name").html("INDIA");
    let _tmp_options = _.cloneDeep(options);
    _tmp_options["state_id"] = [""];
    fetch("get_map_data?" + $.param(_tmp_options, true))
        .then(function(res) {
            return res.json();
        })
        .then(function(map_data) {
            let data = {};
            map_data.forEach(function(element) {
                if (element.state_id === "IN") return;
                data[element["state_id"]] = round_val(
                    element[options["ind"][0]]
                );
            });
            if (!_.values(data).some(x => x !== 0)) {
                show_hide_no_data(true);
            } else {
                show_hide_no_data(false);
                get_max_min_value(data);
                draw_map("india", "", data, map_data);
            }
        });
}

function draw_district_map() {
    let _tmp_options = _.cloneDeep(options);
    _tmp_options["district_id"] = [""];
    fetch("get_district_map_data?" + $.param(_tmp_options, true))
        .then(function(res) {
            return res.json();
        })
        .then(function(map_data) {
            let data = {};
            let state_name = "";
            let state_id = "";
            // console.log("map_data-------->",map_data);
            if (map_data.length > 0) {
                $("#map_slider_wrapper").show();
                $("#chart").show();
                $("#show-hide-map").empty();
                $('#state-national-count').removeAttr("style");
                $('#chart').attr("style",'display:flex !important;height:510px');
               
                map_data.forEach(function(element) {
                    if (element.state_id === "IN") return;
                    state_name = element.state;
                    state_id = element.state_id;
                    data[element["district_id"]] = round_val(
                        element[options["ind"][0]]
                    );
                });
                g_state_id = state_id;
                $(".heat-map-name").html(state_name);
                let _tmp = _.cloneDeep(options);
                _tmp["state_id"] = [""];
                fetch("get_map_data?" + $.param(_tmp, true))
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(state_values) {
                        let _tmp_state = _.filter(state_values, function(d) {
                            return options["state_id"][0] === d.state_id;
                        });
                        let nat_avg = calculate_na(state_values);
                        $(".district_state_view").template({
                            state_id: state_id,
                            state_name: state_name,
                            number_of_districts: _.keys(data).length,
                            score: _tmp_state[0]
                                ? _tmp_state[0][options["ind"][0]]
                                : null,
                            rank: _tmp_state[0] ? _tmp_state[0]["rank"] : null,
                            quarter: _tmp_state[0]
                                ? _tmp_state[0]["quarter"]
                                : null,
                            year: _tmp_state[0] ? _tmp_state[0]["year"] : null,
                            nat_avg: nat_avg ? nat_avg.average : null
                        });
                    });
                    // console.log("data inside common js---->",data);
                // if (!_.values(data).some(x => x !== 0)) {
                //     console.log("insdie if to show hide no data----->");
                //     show_hide_no_data(true);
                // } else {
                    if(state_id != 32 && state_id != 19){
                        show_hide_no_data(false);
                        get_max_min_value(data);
                        // if(state_id==1) //only state id 1
                        draw_map("state", state_id, data, map_data);
                    }else{
                        $('#show-hide-map').empty();
                        $("#map_slider_wrapper").hide();
                        $("#chart").hide();
                        $('#show-hide-map').prepend(
                            '<img id="theImg" src="./Group 86.svg" style="display:flex;justify-content:center;align-items:center;margin: auto;opacity:0.3"/>'
                        );
                        $('#state-national-count').attr("style","display: none !important");
                    }
                   
                // }
            } else {
                console.log("show hide map-->");
                // $('#show-hide-map').empty();
                // $("#map_slider_wrapper").hide();
                // $("#chart").hide();
                // $('#show-hide-map').prepend(
                //     '<img id="theImg" src="./Group 86.svg" style="display:flex;justify-content:center;align-items:center;margin: auto;opacity:0.3"/>'
                // );
                // $('#state-national-count').attr("style","display: none !important");
            }
        });
}

function create_ion_rangeslider() {
    if (range_slider) range_slider.data("ionRangeSlider").destroy();
    range_slider = $("#map_change");
    range_slider.ionRangeSlider({
        type: "double",
        min: d3.min(d3.values(map_data)),
        max: d3.max(d3.values(map_data)),
        onFinish: function(data) {
            options.min_value = [data.from];
            options.max_value = [data.to];
            url.update({
                min_value: options.min_value,
                max_value: options.max_value
            });
            history.pushState({}, "", url.toString());
            let level = "india";
            if (options["district_view"][0] === "1") level = "state";
            colorSubunits(
                level,
                d3.selectAll(".drilldown_map"),
                map_data,
                indicator_list
            );
        }
    });
    range_slider?.data("ionRangeSlider")?.update({
        from: _.toNumber(d3.min(d3.values(map_data))),
        to: _.toNumber(d3.max(d3.values(map_data)))
    });
}

function show_hide_no_data(check) {
    if (check) {
        $(".map-area").css("display", "none");
        $("#view2-table-container").css("display", "none");
        $(".view1-table").css("display", "none");
        $(".rank-table").css("display", "none");
        $(".data-empty-text").removeClass("d-none");
        $(".score").attr("style", "display: none !important");
        $(".change").attr("style", "display: none !important");
    } else {
        $(".map-area").css("display", "block");
        $("#view2-table-container").css("display", "block");
        $(".view1-table").css("display", "block");
        $(".data-empty-text").addClass("d-none");
        $(".score").attr("style", "display: block");
        $(".change").attr("style", "display: block");
    }
}

function drawTable() {
    if (options["quarter"][0] == "Q1") {
        $(".change").addClass("not-allowed");
    } else {
        $(".change").removeClass("not-allowed");
    }
    let url_name = "";
    if (options["district_view"][0] === "0")
        url_name = "get_table_data?" + $.param(options, true);
    else if (options["district_view"][0] === "1")
        url_name = "get_district_table_data?" + $.param(options, true);
    fetch(url_name)
        .then(function(res) {
            return res.json();
        })
        .then(function(table_resp) {
            table_data = table_resp.filter(function(obj) {
                return obj.state_id != "IN";
            });
            let rect_value = 32;
            $(".view-2-table")
                .on("template", function() {
                    $(
                        ".score-type a[id='view-" +
                            options["score_type"][0] +
                            "']"
                    ).click();
                    $.when(sort_table("rank-table1", 1, "text"))
                        .then(function() {
                            if (
                                options["state_id"] != "IN" &&
                                options["district_view"][0] == "0"
                            ) {
                                highlight_state(options["state_id"][0]);
                            } else if (
                                options["district_view"][0] == "1" &&
                                options["district_id"][0] != ""
                            ) {
                                if (options["district_id"][0] == "") {
                                    highlight_state("x");
                                } else {
                                    highlight_state(options["district_id"][0]);
                                }
                            }
                        })
                        .then(function() {
                            loader_hide();
                        });
                })
                .template({
                    table_body: table_data,
                    configs: options,
                    find_prev_quarter: find_prev_quarter,
                    indicator_values: indicator_values,
                    rect_value: rect_value
                });
        });
}

function show_state_info() {
    $(".district-filter").removeClass("d-none");
    $(".state-filter").addClass("d-none");
    $(".tab-state-filter").addClass("d-none");
    $(".district-view-state-profile").removeClass("d-none");
    $(".h-500").css("height", "calc(100vh - 319px)");
}

function hide_state_info() {
    $(".district-filter").addClass("d-none");
    $(".state-filter").removeClass("d-none");
    $(".tab-state-filter").removeClass("d-none");
    $(".district-view-state-profile").addClass("d-none");
    $(".h-500").css("height", "calc(100vh - 192px)");
}

function render_district_dropdown() {
    $(".district-filter").html("");
    fetch("get_districts?state_id=" + options["state_id"][0])
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            $(".render_district_template")
                .on("template", function() {
                    $(".district-filter .selectpicker").selectpicker();
                })
                .template({
                    districts: data
                });
        });
}

function redraw() {
    loader_show();
    url = g1.url.parse(location.href);
    options = $.extend({}, options, url.searchList);
    if (!options["ind"][0]) {
        options["ind"] = ["total_points"];
    }
    $(".select-regions")
        .val(options["state_id"][0])
        .selectpicker("refresh");
    $("#indicator_dropdown_wrapper .selectpicker")
        .val(options["ind"][0])
        .selectpicker("refresh");
    $(".selected-indicator-header").html(
        $("#indicator_dropdown_wrapper .selectpicker option:selected")
            .text()
            .split("(")[0]
    );
    if (options["district_view"][0] === "0") {
        hide_state_info();
        draw_india_map();
        $("#annotations-wrapper").removeClass("d-none");
        $("#annotations-wrapper").addClass("d-flex");
        $("#state_search").attr("placeholder", "Search for a state in India");
        $("#header-statename-text").text("State Name");
    } else if (options["district_view"][0] === "1") {
        show_state_info();
        render_district_dropdown();
        draw_district_map();
        $("#annotations-wrapper").removeClass("d-flex");
        $("#annotations-wrapper").addClass("d-none");
        $("#header-statename-text").text("District Name");
        fetch("get_state_name?state_id='" + options["state_id"][0] + "'")
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                $("#state_search").attr(
                    "placeholder",
                    "Search for a district in " + data[0].state
                );
            });
    }
    show_checkbox("names", options["m_names"][0]);
    show_checkbox("score", options["m_score"][0]);
    show_checkbox("annotations", options["m_annotations"][0]);
    drawTable();
    $(".nav-pills a[id='pills-" + options["tab"][0] + "-tab']").click();
    $(".score-type a[id='view-" + options["score_type"][0] + "']").click();
    $(".select-regions")
        .val(options["state_id"][0])
        .selectpicker("refresh");
}

function show_hide_name_score() {
    let level = "india";
    if (options["district_view"][0] === "1") level = "state";
    let topojson_file = "/india.topo.json";
    let obj_key = "india";
    if (level === "state") {
        topojson_file =
            "assets/data/districts/" +
            state_filename_mapping[g_state_id].toUpperCase() +
            ".json";
        obj_key = "DISTRICTS_2018";
    }
    d3.json(topojson_file, function(data) {
        drawSubUnitLabels(level, data, obj_key, map_data);
    });
}

function draw_view1_table() {
    if (options["district_view"][0] == 1) {
        $(".search_text").attr("placeholder", "Search District / Placeholder");
    } else {
        $(".search_text").attr("placeholder", "Search State / Placeholder");
    }
    let state_url =
        "get_table_view_data?quarter=" +
        options.quarter[0] +
        "&year=" +
        options.year[0];
    let district_url =
        "get_district_table_view_data?quarter=" +
        options.quarter[0] +
        "&year=" +
        options.year[0] +
        "&state_id=" +
        options.state_id[0];
    let get_mat_data_url =
        options["district_view"][0] == "1" ? district_url : state_url;
    let view1_table_data = [],
        min_val,
        max_val,
        state_id_mapping = {},
        column_mapping_data,
        matrix_data;
    ajaxchain_fetch([
        "get_column_mapping",
        get_mat_data_url
    ]).ajaxchain_instance.on("done", function(e) {
        column_mapping_data = JSON.parse(e.response[1]);
        matrix_data = e.response[2];
        if (_.size(matrix_data) > 0) {
            $(".index_table").removeClass("d-none");
            $(".no_data").addClass("d-none");
            let view = options["district_view"][0] == "1" ? "1" : "0";
            view1_table_data = prepare_matrix_data(
                "",
                matrix_data,
                column_mapping_data,
                view
            );
            _.each(view1_table_data, function(item) {
                state_id_mapping[
                    options["district_view"][0] == "1"
                        ? item["district_name"]
                        : item["state"]
                ] =
                    options["district_view"][0] == "1"
                        ? item["district_id"]
                        : item["state_id"];
            });
            $(".view1-table")
                .on("template", function() {
                    $("tbody").scroll(function() {
                        $("thead").css("left", -$("tbody").scrollLeft());
                        $("thead th:nth-child(1)").css(
                            "left",
                            $("tbody").scrollLeft()
                        );
                        $("tbody td:nth-child(1)").css(
                            "left",
                            $("tbody").scrollLeft()
                        );
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                })
                .template({
                    map_data: view1_table_data,
                    min_val: min_val,
                    max_val: max_val,
                    state_id_mapping: state_id_mapping,
                    mat_view_name: mat_view_name,
                    tooltip_data: tooltip_data
                });
        } else {
            $(".no_data").removeClass("d-none");
            $(".index_table").addClass("d-none");
        }
    });
}
$(".data_empty").template({
    options: options
});

function calculate_na(state_values) {
    national_avg = Array.from(
        state_values.reduce(
            (acc, obj) =>
                Object.keys(obj).reduce(
                    (acc, key) =>
                        typeof obj[key] == "number"
                            ? acc.set(
                                  key,
                                  (acc.get(key) || []).concat(obj[key])
                              )
                            : acc,
                    acc
                ),
            new Map()
        ),
        ([indicator, values]) => ({
            indicator,
            average: values.reduce((a, b) => a + b) / values.length
        })
    );
    return national_avg[0];
}
ajaxchain_fetch([
    "get_states",
    "get_indicator_values?show_column=1&_sort=-view_column_name",
    "get_latest_timestamp",
    "get_latest_timestamp_dist"
]).ajaxchain_instance.on("done", function(e) {
    let states = e.response[1];
    indicator_values = e.response[2];
    time_stamp["0"] = e.response[3];
    time_stamp["1"] = e.response[4];
    options["quarter"][0] = time_stamp[options["district_view"][0]][0].quarter;
    options["year"][0] = time_stamp[options["district_view"][0]][0].year;
    options = $.extend({}, options, url.searchList);
    indicator_list = indicator_values;
    indicator_values = _.sortBy(indicator_values, function(item) {
        return order_ind.indexOf(item.mapping_name);
    });
    $(".render_state_template")
        .on("template", function() {
            $(".state-filter .selectpicker").selectpicker();
            $(".tab-state-filter .selectpicker").selectpicker();
            $(".render_indicator_template")
                .on("template", function() {
                    $(
                        "#indicator_dropdown_wrapper .selectpicker"
                    ).selectpicker();
                    redraw();
                })
                .template({
                    res: indicator_values
                });
        })
        .template({
            states: states
        });
    $(".render_state_template1")
        .on("template", function() {})
        .template({
            states: states
        });
});

function click_state_view(state_id, isredraw) {
    options["min_value"] = [];
    options["max_value"] = [];
    options["district_view"] = ["0"];
    options["district_id"] = [""];
    options["state_id"] = [state_id];
    url.update(
        {
            district_view: "0"
            // 'district_id': ''
        },
        "del"
    );
    url.update({
        state_id: state_id
    });
    history.pushState({}, "", url.toString());
    $(".tab-state-filter .selectpicker").val(state_id);
    $(".tab-state-filter .selectpicker").selectpicker("refresh");
    $(".state-filter .selectpicker").val(state_id);
    $(".state-filter .selectpicker").selectpicker("refresh");
    if (isredraw) draw_india_map();
    else {
        show_hide_name_score();
        colorSubunits(
            "india",
            d3.selectAll(".drilldown_map"),
            map_data,
            indicator_list
        );
    }
    if (state_id === "IN") highlight_state("x");
    else highlight_state(state_id);
}

function highlight_state(state_id) {
    $(".table-rows").removeClass("not-active active-row");
    $(".drill-down-arrow").attr("src", "assets/img/forward-arrow.svg");
    if (state_id != "x" && state_id != "") {
        $(".table-rows:not(#state_id-" + state_id + ")").addClass("not-active");
        $("#state_id-" + state_id).addClass("active-row");
        document.getElementById("state_id-" + state_id).scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
        $(".expand-chart.active")
            .attr("src", "assets/img/expand-close.svg")
            .removeClass("px-0");
        $(".expand-chart")
            .removeClass("active")
            .attr("data-target", "")
            .addClass("px-2");
        $(".active-row .expand-chart")
            .addClass("active")
            .attr("data-target", "#expand_graph");
        $(".expand-chart.active")
            .attr("src", "assets/img/expand-active.svg")
            .removeClass("px-2")
            .addClass("px-0");
        $("#state_id-" + state_id + " .drill-down-arrow").attr(
            "src",
            "assets/img/back-face2.png"
        );
    }
}

function click_district_view(district_id, isredraw) {
    options["min_value"] = [];
    options["max_value"] = [];
    options["district_view"] = ["1"];
    options["district_id"] = [district_id];
    // url.update({
    //     'district_id': district_id
    // })
    history.pushState({}, "", url.toString());
    $(".district-filter .selectpicker").val(district_id);
    $(".district-filter .selectpicker").selectpicker("refresh");
    if (isredraw) draw_district_map();
    else {
        show_hide_name_score();
        colorSubunits(
            "state",
            d3.selectAll(".drilldown_map"),
            map_data,
            indicator_list
        );
    }
    highlight_state(district_id);
}
let sort_img = {
    asc: {
        dir: "desc",
        img: "assets/img/sort-bottom.svg"
    },
    desc: {
        dir: "asc",
        img: "assets/img/sort-top.svg"
    }
};
$(document)
    .tooltip({
        selector: '[data-toggle="tooltip"]',
        container: "body",
        placement: "top"
    })
    .on("click", ".icon-search", function() {
        $(".table_header_row").toggleClass("border-dot");
        $(".search_bar_row").toggleClass("d-none");
        $("#state_search").val("");
        $("#rank-table1 tr").filter(function() {
            $(this).toggle(
                $(this)
                    .text()
                    .toLowerCase()
                    .indexOf($("#state_search").text()) > -1
            );
        });
    })
    .on("click", "#pills-rankings-tab", function() {
        $(".tab_download").addClass("d-none");
        $(".tab_search").attr("style", "display: none !important");
        $(".note").addClass("d-none");
        $(".slider-image").addClass("d-none");
        $(".ind-header").addClass("mt-0");
        $(".state-text").css("display", "none");
        $(".state-filter").attr("style", "display: block !important");
        $(".tab-state-filter").attr("style", "display: none !important");
        $(".district-filter").attr("style", "display: block !important");
        $(".overview-tabs")
            .addClass("pr-0")
            .removeClass("pr-4");
        $("#indicator_dropdown_wrapper").removeClass("d-none");
        $("#indicator_dropdown_wrapper").addClass("d-flex");
        options["tab"][0] = "rankings";
        url.update({
            tab: "rankings"
        });
        history.pushState({}, "", url.toString());
        load_calendar("top_cal", options["quarter"][0], options["year"][0]);
        $(".state-filter select").prop("disabled", false);
        if (options["district_view"][0] == "0") {
            $(".district-filter").attr("style", "display: none !important");
            $(".state-filter .selectpicker").val(options["state_id"][0]);
            $(".state-filter .selectpicker").selectpicker("refresh");
        } else {
            $(".state-filter").attr("style", "display: none !important");
            $(".district-filter").removeClass("d-none");
            if (options["district_id"] == "") {
                $(".district-filter .selectpicker").val();
            } else {
                $(".district-filter .selectpicker").val(
                    options["district_id"][0]
                );
            }
            $(".district-filter .selectpicker").selectpicker("refresh");
        }
    })
    .on("click", "#logout", function() {
        window.location.replace(
            sso_domain + "/v1/sso/logout?returnUrl=" + return_url
        );
    })
    .on("click", "#resetPwd", function() {
        window.location.replace(
            sso_domain + "/v1/sso/resetPassword?returnUrl=" + return_url
        );
    })
    .on("click", "#pills-overview-tab", function() {
        $(".tab_download").removeClass("d-none");
        $(".tab_search").attr("style", "display: flex !important");
        $(".note").removeClass("d-none");
        $(".slider-image").removeClass("d-none");
        $(".overview-tabs")
            .removeClass("pr-0")
            .addClass("pr-4");
        $(".ind-header").removeClass("mt-0");
        $(".tab-state-filter").attr("style", "display: block !important");
        $(".state-filter").attr("style", "display: none !important");
        $(".district-filter").attr("style", "display: none !important");
        $("#indicator_dropdown_wrapper").addClass("d-none");
        $("#indicator_dropdown_wrapper").removeClass("d-flex");
        options["tab"][0] = "overview";
        url.update({
            tab: "overview"
        });
        history.pushState({}, "", url.toString());
        load_calendar("top_cal", options["quarter"][0], options["year"][0]);
        $(".state-filter, .district-filter").addClass("d-none");
        if (options["district_view"][0] == "0") {
            draw_view1_table();
        } else {
            draw_view1_table();
        }
    })
    .on("click", "#help-icon", function() {
        if (options["tab"][0] == "overview") {
            var intro = introJs();
            intro.setOptions({
                doneLabel: "Next View",
                steps: [
                    {
                        element: document.querySelectorAll(".overview-tabs")[0],
                        intro:
                            "This constitute the first view which helps you to have an understanding for all the state for all the major index indicators."
                    },
                    {
                        element: document.querySelectorAll(
                            ".cal-button-area"
                        )[0],
                        intro:
                            "View data for other quarters by clicking on the calendar.",
                        position: "bottom"
                    },
                    {
                        element: document.querySelectorAll(".tab_search")[0],
                        intro:
                            "Filter the table based on State and Indicator with ease.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#view1_table th:nth-child(3)"
                        )[0],
                        intro:
                            "Click on a indicator to explore the states data in the map view.",
                        position: "bottom"
                    },
                    {
                        element: document.querySelectorAll(
                            "#view1_table tr:nth-child(2) td:nth-child(1)"
                        )[0],
                        intro:
                            "Click on a state to explore the district data of that state.",
                        position: "bottom"
                    },
                    {
                        element: document.querySelectorAll(".tab_download")[0],
                        intro:
                            "Download the given below table in Excel format for your own use.",
                        position: "left"
                    },
                    {
                        element: document.querySelectorAll("#map-view-link")[0],
                        intro: "Explore data in the Map view.",
                        position: "bottom"
                    }
                ]
            });
            intro.start().oncomplete(function() {
                window.location.href = "?tab=rankings&score_type=abs_score";
            });
        } else {
            var intro2 = introJs();
            intro2.setOptions({
                doneLabel: "Next View",
                steps: [
                    {
                        element: document.querySelectorAll(
                            "#indicator_dropdown_wrapper"
                        )[0],
                        intro: "Select a major indicator from the dropdown."
                    },
                    {
                        element: document.querySelectorAll(
                            ".cal-button-area"
                        )[0],
                        intro:
                            "View data for other quarters by clicking on the calendar.",
                        position: "bottom"
                    },
                    {
                        element: document.querySelectorAll("#pills-score")[0],
                        intro:
                            "This constitute a sortable table to explore data for states/district",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#header-statename"
                        )[0],
                        intro: "Sort the table based on different columns.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll("#view2-search")[0],
                        intro: "Filter States/District using quick search.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#view-per_change"
                        )[0],
                        intro:
                            "Toggle the percentage change figures for each district/state.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#pills-tabContent tbody tr:nth-child(2)"
                        )[1],
                        intro: "Select a state to explore its districts.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(".map-wrapper")[0],
                        intro:
                            "You can also select a state on the map to explore its districts.",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#map_slider_wrapper"
                        )[0],
                        intro:
                            "Adjust the slide to only view specific sections of the map based on the values",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(".map-filters")[0],
                        intro:
                            "Enable/Disable the options to view Name, Score and Annotations",
                        position: "right"
                    },
                    {
                        element: document.querySelectorAll(
                            "#matrix-modal-wrapper"
                        )[0],
                        intro: "Click here  to explore data on a martix table",
                        position: "left"
                    }
                ]
            });
            intro2.start().oncomplete(function() {
                // window.location.href = "profile?state_id=37&district_id=&district_view=0&active_tab=heatmap";
            });
        }
    })
    .on("click", "#view-per_change", function() {
        $("#view-abs_score").removeClass(
            "border-primary border-bottom border-2 text-primary active"
        );
        $("#view-abs_score").addClass("text-color1");
        $("#view-abs_score .dot").addClass("d-none");
        $("#view-per_change .dot").removeClass("d-none");
        $("#view-per_change").removeClass("text-color1");
        $(".percent-block").css("display", "block ");
        $(".score-block").css("display", "none");
        $("#header-score span#header-score-text").text("% Change");
        $(".col-rank-percent").attr("data-value", function() {
            $(this).attr("data-value", $(this).attr("data-per-change-value"));
        });
        let url_tab = {};
        url_tab["score_type"] = ["per_change"];
        url = g1.url.parse(location.href).update(url_tab);
        history.pushState({}, "", url.toString());
    })
    .on("click", "#view-abs_score", function() {
        $("#view-per_change").removeClass(
            "border-primary border-bottom border-2 text-primary active"
        );
        $("#view-per_change").addClass("text-color1");
        $("#view-abs_score .dot").removeClass("d-none");
        $("#view-per_change .dot").addClass("d-none");
        $("#view-abs_score").removeClass("text-color1");
        $(".percent-block").css("display", "none");
        $(".score-block").css("display", "block");
        $(".col-rank-percent").attr("data-value", function() {
            $(this).attr("data-value", $(this).attr("data-abs-value"));
        });
        $("#header-score span#header-score-text").text("Score");
        let url_tab = {};
        url_tab["score_type"] = ["abs_score"];
        url = g1.url.parse(location.href).update(url_tab);
        history.pushState({}, "", url.toString());
    })
    .on("click", "#view1-table tr", function() {
        let url_tab = {};
        url_tab["tab"] = "rankings";
        url_tab["state_id"] = $(this)
            .children(".state_col")
            .attr("data-state_id");
        url = g1.url.parse(location.href).update(url_tab);
        window.location = url;
    })
    .on("click", "#table-download-button", function() {
        let filename =
            "TB Score - state-" +
            options["quarter"][0] +
            " " +
            options["year"][0];
        if (options["district_view"][0] == 1) {
            window.open(
                "download_district_view_data?quarter=" +
                    options["quarter"][0] +
                    "&year=" +
                    options["year"][0] +
                    "&state_id=" +
                    options["state_id"][0] +
                    "&_format=xlsx&_download=" +
                    filename +
                    ".xlsx"
            );
        } else {
            window.open(
                "download_table_view_data?quarter=" +
                    options["quarter"][0] +
                    "&year=" +
                    options["year"][0] +
                    "&_format=xlsx&_download=" +
                    filename +
                    ".xlsx"
            );
        }
    })
    .on("click", "#mapview-download-button", function() {
        let filename =
            "TB Score - " + options["quarter"][0] + " " + options["year"][0];
        if (options["district_view"][0] == 1) {
            window.open(
                "download_district_view_data?quarter=" +
                    options["quarter"][0] +
                    "&year=" +
                    options["year"][0] +
                    "&state_id=" +
                    options["state_id"][0] +
                    "&_format=xlsx&_download=" +
                    filename +
                    ".xlsx"
            );
        } else {
            window.open(
                "download_table_view_data?quarter=" +
                    options["quarter"][0] +
                    "&year=" +
                    options["year"][0] +
                    "&_format=xlsx&_download=" +
                    filename +
                    ".xlsx"
            );
        }
    })
    .on("change", "#state-select-dropdown", function() {
        let state = $(this).val();
        let dt = state == "IN" ? "0" : "1";
        url.update({
            state_id: state,
            // 'district_id': '',
            district_view: dt
        });
        history.pushState({}, "", url.toString());
        redraw();
    })
    .on("change", "#district-select-dropdown", function() {
        let dist = $(this).val();
        url.update({
            // 'district_id': dist
        });
        history.pushState({}, "", url.toString());
        if (dist) redirect_profile(1);
    })
    .on("click", "#names", function() {
        url.update({
            m_names: $(this)
                .prop("checked")
                .toString()
        });
        history.pushState({}, "", url.toString());
        options["m_names"] = [
            $(this)
                .prop("checked")
                .toString()
        ];
        show_hide_name_score();
    })
    .on("click", "#score", function() {
        url.update({
            m_score: $(this)
                .prop("checked")
                .toString()
        });
        history.pushState({}, "", url.toString());
        options["m_score"] = [
            $(this)
                .prop("checked")
                .toString()
        ];
        show_hide_name_score();
    })
    .on("click", "#annotations", function() {
        draw_annotations();
        url.update({
            m_annotations: $(this)
                .prop("checked")
                .toString()
        });
        history.pushState({}, "", url.toString());
        options["m_annotations"] = [
            $(this)
                .prop("checked")
                .toString()
        ];
        $(".annotation-group").toggleClass("d-none");
    })
    .on(
        "changed.bs.select",
        "#indicator_dropdown_wrapper .selectpicker",
        function() {
            $(".selected-indicator-header").html(
                $("#indicator_dropdown_wrapper .selectpicker option:selected")
                    .text()
                    .split("(")[0]
            );
            options["min_value"] = [];
            options["max_value"] = [];
            url.update(
                {
                    min_value: url.searchList["min_value"] || [],
                    max_value: url.searchList["max_value"] || []
                },
                "del"
            );
            url.update({
                ind: $(this).val()
            });
            history.pushState({}, "", url.toString());
            redraw();
        }
    )
    .on("click", ".fa-chevron-left", function() {
        let year = parseInt($(".year").attr("data-attr"));
        $(".year").text(year - 1 + " - " + year);
        $(".year").attr("data-attr", year - 1);
        $(".month").each(function() {
            $(this).attr("data-year", parseInt($(this).attr("data-year")) - 1);
        });
    })
    .on("click", ".fa-chevron-right", function() {
        let year = parseInt($(".year").attr("data-attr")) + 1;
        $(".year").text(year + " - " + (year + 1));
        $(".year").attr("data-attr", year);
        $(".month").each(function() {
            $(this).attr("data-year", parseInt($(this).attr("data-year")) + 1);
        });
    })
    .on("click", "#header-score", function() {
        let sort_col_type = $(this).attr("data-col_type");
        let sort_order = options["mat_sort_odr"];
        sort_order = sort_img[sort_order].dir;
        $("#state_img").attr("src", "assets/img/icon-nosort.svg");
        $("#score_img").attr("src", "assets/img/icon-nosort.svg");
        $("#score_img").attr("src", sort_img[sort_order].img);
        sort_table("rank-table1", 2, sort_col_type);
        options["mat_sort_odr"] = sort_order;
    })
    .on("click", "#header-statename", function() {
        let sort_col_type = $(this).attr("data-col_type");
        let sort_order = options["mat_sort_odr"];
        sort_order = sort_img[sort_order].dir;
        $("#score_img").attr("src", "assets/img/icon-nosort.svg");
        $("#state_img").attr("src", "assets/img/icon-nosort.svg");
        $("#state_img").attr("src", sort_img[sort_order].img);
        sort_table("rank-table1", 1, sort_col_type);
        options["mat_sort_odr"] = sort_order;
    })
    .on("click", ".profile_link", function() {
        // redirect_profile(0)
    })
    .on("click", ".district-drill-down", function(e) {
        if (options["state_id"][0] === "IN") return;
        else if (options["district_view"][0] === "1")
            // redirect_profile(1)
            e.stopPropagation();
        url.update(
            {
                // 'district_id': url.searchList['district_id'] || ''
            },
            "del"
        );
        url.update({
            state_id: $(this).attr("id"),
            district_view: "1"
        });
        history.pushState({}, "", url.toString());
        redraw();
    })
    .on("click", ".back-arrow", function() {
        url.update(
            {
                // 'district_id': url.searchList['district_id'] || ''
            },
            "del"
        );
        url.update({
            state_id: "IN",
            district_view: "0"
        });
        options["min_value"] = [];
        options["max_value"] = [];
        history.pushState({}, "", url.toString());
        $(".data-empty-text").addClass("d-none");
        redraw();
    })
    .on("click", ".table-rows", function() {
        if (options["district_view"][0] === "0") {
            url.update({
                state_id: $(this).attr("data-state_id"),
                district_view: "1"
            });
            history.pushState({}, "", url.toString());
            redraw();
        }
        // else if (options['district_view'][0] === '1') {
        //     url.update({
        //         district_id: $(this).attr('data-state_id'),
        //         district_view: '1'
        //     })
        //     history.pushState({}, '', url.toString())
        //     redirect_profile(1)
        // }
    })
    .on(
        "mouseover",
        "#view1_table  th:not(:first-child):not(.dont-highlight)",
        function() {
            $(
                "td:not(:first-child)[data-seq='" +
                    $(this).attr("data-seq") +
                    "']"
            ).addClass("highlighted_row_col");
        }
    )
    .on("mouseout", "#view1_table th:not(:first-child)", function() {
        $(
            "td:not(:first-child)[data-seq='" + $(this).attr("data-seq") + "']"
        ).removeClass("highlighted_row_col");
    })
    .on("mouseover", "#view1_table  td:first-child", function() {
        $(this)
            .parent()
            .addClass("highlighted_row_col");
    })
    .on("mouseout", "#view1_table td:first-child", function() {
        $($(this).parent()).removeClass("highlighted_row_col");
    })
    .on("click", ".view1-table-rows td:first-child", function() {
        if (options["district_view"][0] == "0") {
            $(".map_view_text").addClass("d-none");
            $(this)
                .children()
                .removeClass("d-none");
        }
    })
    .on("click", ".map_view_text", function() {
        url.update({
            state_id: $(this)
                .closest("tr")
                .attr("data-state_id"),
            tab: "rankings",
            district_view: 1
        });
        window.location = url;
    })
    .on("click", ".drilldown_map", function(event) {
        let _id = $(this).attr("data-attr");
        event.stopPropagation();
        let level = $(this).attr("level");
        // console.log('drilldown', level, _id);
        if (level === "india") {
            url.update({
                state_id: _id,
                district_view: "1"
            });
            history.pushState({}, "", url.toString());
            $("#state").val(_id);
            $("#btn-loader").click();
            // redraw()
        } else if (level === "state") {
            url.update({
                // district_id: _id,
                district_view: "1"
            });
            history.pushState({}, "", url.toString());
            redirect_profile(1);
        }
    })
    .on("click", ".map-container", function(event) {
        event.stopPropagation();
        if (
            options["district_view"][0] == 0 &&
            options["state_id"][0] !== "IN"
        ) {
            //call index function
            click_state_view("IN", false);
        } else if (
            options["district_view"][0] == 1 &&
            options["district_id"][0] !== ""
        ) {
            click_district_view("", false);
        }
    })
    .on("click", "img.pos-bc, #table_view_expand_btn", function(event) {
        event.stopPropagation();
        let cont_id = $(this).attr("data-id");
        let data_sel = $(this)
            .closest("th")
            .attr("data-key");
        let tab_sel = $($(this).closest("th")[0]).attr("data-seq");
        if ($("[id=" + cont_id + "]").hasClass("d-none")) {
            $("[id=" + cont_id + "]").removeClass("d-none");
            $(this).attr("src", "assets/img/icon-collapse.svg");
            $(this)
                .closest("th")
                .addClass("bg-color55");
            $("td[data-key='" + data_sel + "']").removeClass("bg-color53");
            $("td[data-key='" + data_sel + "']").addClass("bg-color55");
            $("td[data-seq='" + tab_sel + "']").addClass("bg-color55");
        } else {
            $("[id=" + cont_id + "]").addClass("d-none");
            $(this).attr("src", "assets/img/icon-expand.svg");
            $(this)
                .closest("th")
                .removeClass("bg-color55");
            $("td[data-key='" + data_sel + "']").removeClass("bg-color55");
            $("td[data-key='" + data_sel + "']").addClass("bg-color53");
            $("td[data-seq='" + tab_sel + "']").removeClass("bg-color55");
        }
    })
    .on("click", ".table_sort", function(event) {
        event.stopPropagation();
        let sort_image = {
            asc: {
                dir: "desc",
                img: "assets/img/sort-bottom.svg"
            },
            desc: {
                dir: "asc",
                img: "assets/img/sort-top.svg"
            }
        };
        let sort_key = $(this).attr("data-key");
        let sort_col_pos = $(this).attr("data-col_num");
        let sort_col_type = $(this).attr("data-col_type");
        let ex_sort_key = options["mat_sort_key"];
        let sort_order = options["mat_sort_odr"];
        if (sort_key === ex_sort_key) {
            sort_order = sort_image[sort_order].dir;
        }
        $(".table_sort").attr("src", "assets/img/icon-nosort.svg");
        $(this).attr("src", sort_image[sort_order].img);
        sort_col_pos =
            options["district_view"][0] == "1" && sort_col_pos > 0
                ? sort_col_pos - 1
                : sort_col_pos;
        sort_table("view1_table", sort_col_pos, sort_col_type, sort_order);
        options["mat_sort_key"] = sort_key;
        options["mat_sort_odr"] = sort_order;
    })
    .on("click", ".matrix_sort", function(event) {
        event.stopPropagation();
        let sort_switch = {
            asc: {
                dir: "desc",
                img: "assets/img/sort-bottom.svg"
            },
            desc: {
                dir: "asc",
                img: "assets/img/sort-top.svg"
            }
        };
        let sort_key = $(this).attr("data-key");
        let sort_col_pos = $(this).attr("data-col_num");
        let sort_col_type = $(this).attr("data-col_type");
        let ex_sort_key = options["mat_sort_key"];
        let sort_order = options["mat_sort_odr"];
        if (sort_key === ex_sort_key) {
            sort_order = sort_switch[sort_order].dir;
        }
        $(".matrix_sort").attr("src", "assets/img/icon-nosort.svg");
        $(this).attr("src", sort_switch[sort_order].img);
        sort_table("matrix_table", sort_col_pos, sort_col_type, sort_order);
        options["mat_sort_key"] = sort_key;
        options["mat_sort_odr"] = sort_order;
    })
    .on("click", "#close-link", function() {
        $(".table_header_row").toggleClass("border-dot");
        $(".search_bar_row").toggleClass("d-none");
        $("#state_search").val("");
        $("#rank-table1 tr").filter(function() {
            $(this).toggle(
                $(this)
                    .text()
                    .toLowerCase()
                    .indexOf($("#state_search").text()) > -1
            );
        });
    })
    .on("mouseover", ".info-icon-hover", function() {
        $(".info-icon-hover").attr("src", "assets/img/info_icon.svg");
        $(this).attr("src", "assets/img/info-more.svg");
    })
    .on("mouseout", ".info-icon-hover", function() {
        $(".info-icon-hover").attr("src", "assets/img/info_icon.svg");
    });
$("#state_search").on("keyup", function() {
    let value = $(this)
        .val()
        .toLowerCase();
    $("#rank-table1 tr").filter(function() {
        $(this).toggle(
            $(this)
                .text()
                .toLowerCase()
                .indexOf(value) > -1
        );
    });
});
$("#view1-search").focusout(function() {
    $("#view1-search").val("");
    $("#GFG_DOWN").addClass("d-none");
    draw_view1_table();
});
$("#view1-search").on("keyup", function() {
    let value = $(this)
        .val()
        .toLowerCase();
    let flag = 0;
    $("#view1_table").removeClass("d-none");
    $("#GFG_DOWN").addClass("d-none");
    if (value.length) {
        $("#view1_table thead th:not(:first-child)").filter(function() {
            if (
                $(this)
                    .text()
                    .toLowerCase()
                    .indexOf(value) > -1
            ) {
                flag = 1;
            }
        });
        if (!flag) {
            let state_flag = 1;
            $("#view1_table tbody tr").filter(function() {
                if (
                    $(this)
                        .text()
                        .toLowerCase()
                        .indexOf(value) > -1
                ) {
                    state_flag = 0;
                }
                $(this).toggle(
                    $(this)
                        .text()
                        .toLowerCase()
                        .indexOf(value) > -1
                );
            });
            if (state_flag) {
                $("#view1_table").addClass("d-none");
                $("#GFG_DOWN").removeClass("d-none");
            }
            $("#GFG_DOWN").text("Try searching for other keywords");
            $("#view1_table thead th:not(:first-child)").toggle(true);
            $("#view1_table tbody td:not(:first-child)").toggle(true);
        } else {
            $("#view1_table thead th:not(:first-child)").filter(function() {
                if (
                    $(this)
                        .text()
                        .toLowerCase()
                        .indexOf(value) < 0
                ) {
                    let col_num = $(this).attr("data-seq");
                    $(
                        "#view1_table tbody td:not(:first-child)[data-seq='" +
                            col_num +
                            "']"
                    ).toggle(false);
                }
                $(this).toggle(
                    $(this)
                        .text()
                        .toLowerCase()
                        .indexOf(value) > -1
                );
            });
        }
    } else {
        draw_view1_table();
    }
});

// $(".last_update").text("Data Last Updated on 23 April 2020")
// function redirect_profile(dist_view) {
//     options = $.extend({}, options, url.searchList)
//     window.location.href = "profile?state_id=" + options['state_id'][0] + "&district_id=" + options['district_id'][0] + "&district_view=" + dist_view
// }
