let state_name_mapping = {
    "Andaman & Nicobar Island": "AN",
    "Andhra Pradesh": "AP",
    "Arunachal Pradesh": "AR",
    Assam: "AS",
    Bihar: "BI",
    Chandigarh: "CH",
    Chhattisgarh: "CG",
    "Dadara & Nagar Havelli": "DN",
    "Daman & Diu": "DD",
    Goa: "GA",
    Gujarat: "GU",
    Haryana: "HR",
    "Himachal Pradesh": "HP",
    "Jammu and Kashmir": "JK",
    Jharkhand: "JH",
    Karnataka: "KA",
    Kerala: "KE",
    Lakshadweep: "LK",
    "Madhya Pradesh": "MP",
    Maharashtra: "MH",
    Manipur: "MN",
    Meghalaya: "MG",
    Mizoram: "MZ",
    Nagaland: "NG",
    Delhi: "DL",
    Puducherry: "PD",
    Punjab: "PN",
    Rajasthan: "RJ",
    Sikkim: "SK",
    "Tamil Nadu": "TN",
    Telangana: "TS",
    Tripura: "TR",
    "Uttar Pradesh": "UP",
    Uttarakhand: "UR",
    "West Bengal": "WB",
    Odisha: "OR"
};
let state_filename_mapping = {
    "3": "Andaman & Nicobar",
    "4": "Andhra Pradesh",
    "5": "Arunachal Pradesh",
    "6": "Assam",
    "7": "Bihar",
    "9": "Chhattisgarh",
    "27": "Puducherry",
    "28": "Punjab",
    "29": "Rajasthan",
    "30": "Sikkim",
    "31": "Tamil Nadu",
    "8": "Chandigarh",
    "38": "Telangana",
    "33": "Tripura",
    "34": "Uttar Pradesh",
    "35": "Uttarakhand",
    "36": "West Bengal",
    "26": "Odisha",
    "11": "Daman & Diu",
    "13": "Goa",
    "1": "Gujarat",
    "14": "Haryana",
    "15": "Himachal Pradesh",
    "16": "Jammu And Kashmir",
    "2": "Jharkhand",
    "17": "Karnataka",
    "18": "Kerala",
    "19": "Lakshadweep",
    "20": "Madhya Pradesh",
    "21": "Maharashtra",
    "22": "Manipur",
    "23": "Meghalaya",
    "24": "Mizoram",
    "25": "Nagaland",
    "12": "Delhi",
    "10": "DADRA AND NAGAR HAVELI"
};
let state_code_mapping = {
    "Himachal Pradesh": "15",
    Punjab: "28",
    Chandigarh: "8",
    Uttarakhand: "35",
    Haryana: "14",
    Delhi: "12",
    Rajasthan: 29,
    "Uttar Pradesh": "34",
    Bihar: "7",
    Sikkim: "30",
    "Arunachal Pradesh": "5",
    Nagaland: "25",
    Manipur: "22",
    Mizoram: "24",
    Tripura: "33",
    Meghalaya: "23",
    Assam: "6",
    Jharkhand: "2",
    Odisha: "26",
    Chhattisgarh: "9",
    "Madhya Pradesh": "20",
    Gujarat: "1",
    "Daman & Diu": "11",
    Maharashtra: "21",
    Karnataka: "17",
    Goa: "13",
    Kerala: "18",
    "Tamil Nadu": "31",
    Puducherry: "27",
    Telangana: "38",
    "Andhra Pradesh": "4",
    "Andaman & Nicobar Island": "3",
    Lakshadweep: "19",
    "Dadara & Nagar Havelli": "10",
    "West Bengal": "36",
    "Jammu and Kashmir": "16"
};
let projection = d3.geoMercator();
let path = d3
    .geoPath()
    .projection(projection)
    .pointRadius(2);

function draw_map(level, state_id, mp_data, c_data) {
    let topojson_file = "/india.topo.json";
    let obj_key = "india";
    if (level === "state") {
        topojson_file = `/${state_filename_mapping[
            state_id
        ].toUpperCase()}.topo.json`;
        obj_key = "DISTRICTS_2018";
        console.log(topojson_file);
    }
    d3.select(".map-container")
        .select("svg")
        .remove();
    d3.select(".d3-tip").remove();

    let width = $(".map-container").width();
    let height = $(".map-container").height();
    console.log("width",$(".map-container").width());
    let zoom = d3
        .zoom()
        .scaleExtent([1, 40])
        .translateExtent([
            [0, 0],
            [width, height]
        ])
        .extent([
            [0, 0],
            [width, height]
        ])
        .on("zoom", zoomed);

    function zoomed() {
        var scale = d3.zoomTransform(this).k;
        var fontsize = 0.656;
        if (scale != 1) {
            g.attr("transform", d3.event.transform);
            fontsize = fontsize / scale;
            d3.selectAll("text").attr("style", function() {
                return (
                    "font-size:" +
                    fontsize +
                    "rem !important; text-transform:uppercase"
                );
            });
        } else {
            d3.selectAll("text").attr("style", function() {
                return (
                    "font-size:" +
                    fontsize +
                    "rem !important; text-transform:uppercase"
                );
            });
        }
    }
    

    let svg = d3
        .select(".map-container")
        .append("svg")
        .attr("width", width)
        .attr("height", height)
        .attr("filter", "url(#dropshadow)")
        .attr("viewBox", "0 0 " + width.toFixed(0) + " " + height.toFixed(0));

        var defs = svg.append("defs");

        var filter = defs.append("filter").attr("id", "dropshadow");

        filter
            .append("feGaussianBlur")
            .attr("in", "SourceAlpha")
            .attr("stdDeviation", 1)
            .attr("result", "blur");
        filter
            .append("feOffset")
            .attr("in", "blur")
            .attr("dx", 5)
            .attr("dy", 5)
            .attr("result", "offsetBlur");
        filter
            .append("feFlood")
            .attr("in", "offsetBlur")
            .attr("flood-color", "#36454F")
            .attr("flood-opacity", "2")
            .attr("result", "offsetColor");
        filter
            .append("feComposite")
            .attr("in", "offsetColor")
            .attr("in2", "offsetBlur")
            .attr("operator", "in")
            .attr("result", "offsetBlur");

        var feMerge = filter.append("feMerge");

        feMerge.append("feMergeNode").attr("in", "offsetBlur");
        feMerge.append("feMergeNode").attr("in", "SourceGraphic");


    let tool_tip = d3
        .tip()
        .attr("class", "d3-tip text-black bg-white shadow py-2 px-3 tail-lt")
        .offset([0, 0])
        .direction("e")
        .html(function(d) {
            let temp = "";
            if (level === "india") {
                breakPoint = false;
                _.each(c_data, function(e) {
                    if (e.state_id == state_code_mapping[d.properties.ST_NM]) {
                        temp =
                            "<table><tr><td class='py-1' style='font-size:14px;line-height:20px; font-weight:bold'>" +
                            d.properties.ST_NM +
                            "</td><td class='text-right py-1 cursor-pointer district-drill-down' id='" +
                            e.state_id +
                            "'></td></tr><tr><td class='py-1' style='font-size:10px'>Subscribers</td><td class='text-right py-1 font-weight-800' style='font-size:12px; color:#0D72E8'>" +
                            round_val(e[options["ind"][0]]) +
                            "</td></tr></table>";
                        breakPoint = true;
                    }
                    else{
                        temp =
                            "<table><tr><td class='py-1' style='font-size:14px;line-height:20px; font-weight:bold'>" +
                            d.properties.ST_NM + "<tr><td class='py-1' style='font-size:10px'>Subscribers</td><td class='text-right py-1 font-weight-700' style='font-size:12px; color:#0D72E8'>" +
                            0 + "</td></tr></table>";
                    }
                    if(breakPoint) {
                        breakPoint = false;
                        return false;
                    } 
                });
            } else if (level === "state") {
                breakOut = false;
                _.each(c_data, function(e) {
                    
                    if (e.district_id == d.properties.D_CODE) {
                        temp =
                            "<table><tr><td class='py-1' style='font-size:14px;line-height:20px; font-weight:bold'>" +
                            e.district_name +
                            "</td><td class='text-right py-1 cursor-pointer district-drill-down' id='" +
                            e.district_id +
                            "'></td></tr><tr><td class='py-1' style='font-size:10px'>Subscribers</td><td class='text-right py-1 font-weight-700' style='font-size:12px; color:#0D72E8'>" +
                            round_val(e[options["ind"][0]]) +
                            "</td></tr></table>";
                            breakOut = true;
                    }
                    else{
                        temp =
                            "<table><tr><td class='py-1' style='font-size:14px;line-height:20px; font-weight:bold'>" +
                            d.properties.D_NAME + "<tr><td class='py-1' style='font-size:10px'>Subscribers</td><td class='text-right py-1 font-weight-700' style='font-size:12px; color:#0D72E8'>" +
                            0 + "</td></tr></table>";
                    }
                    if(breakOut) {
                        breakOut = false;
                        return false;
                    } 
                });
            }
            return temp;
        });
    let g;
    if (level == "india") {
        g = svg
            .append("g")
            .attr("class", "map-wrapper");
            // .style("transform", "translate(50px , 0px)");
    } else {
        g = svg
            .append("g")
            .attr("class", "map-wrapper")
            .call(zoom);
    }
    let c = d3
        .scaleSequential()
        .domain([d3.min(d3.values(mp_data)), d3.max(d3.values(mp_data))])
        .interpolator(d3.interpolatePiYG);
    d3.json(topojson_file, function(data) {
        let boundary = centerZoom(data);
        drawOuterBoundary(boundary);
        let subunits = drawSubUnits(data);
        colorSubunits(level, subunits, mp_data, indicator_list);
        drawSubUnitLabels(level, data, obj_key, mp_data);
        if (options["district_view"][0] == 0) {
            draw_annotations();
        }
    });

    function centerZoom(data) {
        let o = topojson.mesh(data, data.objects[obj_key], function(a, b) {
            return a === b;
        });
        projection.scale(1).translate([0, 0]);
        let b = path.bounds(o),
            s =
                1 /
                Math.max(
                    (b[1][0] - b[0][0]) / width,
                    (b[1][1] - b[0][1]) / height
                ),
            t = [
                (width - s * (b[1][0] + b[0][0])) / 2,
                (height - s * (b[1][1] + b[0][1])) / 2
            ];
        projection.scale(s).translate(t);
        return o;
    }

    function drawOuterBoundary(boundary) {
        g.append("path")
            .datum(boundary)
            .attr("d", path);
    }
    function drawSubUnits(data) {
        let subunits = g
            .selectAll(".subunit")
            .data(topojson.feature(data, data.objects[obj_key]).features)
            .enter()
            .append("path")
            .attr("d", path)
            .attr("class", function() {
                return "drilldown_map";
            })
            .attr("level", level)
            .attr("data-attr", function(d) {
                if (level === "india") {
                    return (
                        state_code_mapping[d.properties.ST_NM] ||
                        d.properties.ST_NM
                    );
                } else return d.properties.D_CODE;
            })
            .attr("data-points", function(d) {
                if (level === "india") {
                    return mp_data[state_code_mapping[d.properties.ST_NM]];
                } else return d.properties.D_NAME;
            })
            .attr("data-orig_color", function(d) {
                return c(mp_data[state_code_mapping[d.properties.ST_NM]]);
            })
            .attr("data-placement", "right")
            .on("mouseover", tool_tip.show)
            .on("mouseout", tool_tip.hide)
            .style("cursor", "pointer")
            .style("stroke", "#000")
            .style("stroke-width", "1px")
            .style("cursor", function() {
                return "pointer";
            });
        svg.call(tool_tip);
        return subunits;
    }
}

function drawSubUnitLabels(level, data, obj_key, mp_data) {
    d3.select(".map-container svg g")
        .selectAll(".map-label")
        .remove();
    d3.select(".map-container svg g")
        .selectAll(".map-name")
        .remove();
    d3.select(".map-container svg g")
        .selectAll(".subunit-label")
        .data(topojson.feature(data, data.objects[obj_key]).features)
        .attr("y", "-5")
        .attr("rx", "5")
        .attr("ry", "5")
        .attr("width", function() {
            if (level === "india") return "40";
            else if (level == "state") return "80";
        })
        .attr("height", "10")
        .attr("fill", "#fff")
        .attr("opacity", function() {
            if (
                options["m_names"][0] === "true" ||
                options["m_score"][0] === "true"
            )
                return 1;
            return 0;
        })
        .attr("class", function(d) {
            if (
                level === "india" &&
                (options["state_id"][0] === "IN" ||
                    options["state_id"][0] ===
                        state_code_mapping[d.properties.ST_NM])
            )
                return "map-label";
            else if (
                level === "state" &&
                (options["district_id"][0] === "" ||
                    options["district_id"][0] === d.properties.D_CODE)
            )
                return "map-label";
            else return "map-label d-none";
        });
    d3.select(".map-container svg g")
        .selectAll(".subunit-label")
        .data(topojson.feature(data, data.objects[obj_key]).features)
        .enter()
        .append("text")
        .attr("transform", function(d) {
            return "translate(" + path.centroid(d) + ")";
        })
        .attr("dy", ".35em")
        .attr("text-anchor", "middle")
        .style("font-size", ".625rem")
        .style("text-transform", "uppercase")
        .attr("class", "map-name")
        .attr("data-state-id", function(d) {
            return d.properties.ST_NM;
        })
        .text(function(d) {
            if (level === "india") {
                if (
                    options["state_id"][0] === "IN" ||
                    options["state_id"][0] ===
                        state_code_mapping[d.properties.ST_NM]
                ) {
                    if (
                        options["m_names"][0] === "true" &&
                        options["m_score"][0] === "true"
                    )
                        return (
                            state_name_mapping[d.properties.ST_NM] +
                            ":" +
                            round_val(
                                mp_data[state_code_mapping[d.properties.ST_NM]]
                            )
                        );
                    else if (options["m_names"][0] === "true")
                        return state_name_mapping[d.properties.ST_NM];
                    else if (options["m_score"][0] === "true")
                        // return round_val(
                        //     mp_data[state_code_mapping[d.properties.ST_NM]]
                        // );
                        return "";
                    else return;
                }
            } else {
                let value;
                if (typeof mp_data[d.properties.D_CODE] !== "undefined") {
                    value = mp_data[d.properties.D_CODE];
                } else {
                    // value = "NA";
                    value = "0";
                }
                if (
                    options["district_id"][0] === "" ||
                    options["district_id"][0] === d.properties.D_CODE
                ) {
                    if (
                        options["m_names"][0] === "true" &&
                        options["m_score"][0] === "true"
                    ){
                    console.log('mp_data val',value);
                        return d.properties.D_NAME + ":" + round_val(value);
                    }
                    else if (options["m_names"][0] === "true")
                        return d.properties.D_NAME;
                    else if (options["m_score"][0] === "true")
                        return round_val(value);
                    else return;
                }
            }
        });
}

function colorSubunits(level, subunits, mp_data, indicator_list) {
    let indicator_selected = indicator_list?.find(function(d) {
        return d.mapping_name == options.ind[0];
    });
    indicator_selected;
    let data_min_val = d3.min(d3.values(mp_data));
    let data_max_val = d3.max(d3.values(mp_data));
    let data_mid_val = (data_min_val + data_max_val) / 2;
    if (data_min_val == data_max_val) {
        data_min_val = 0;
    }
    let c = d3
        .scaleLinear()
        .domain([data_min_val, data_mid_val, data_max_val])
        .interpolate(d3.interpolateHcl)
        .range(["#99d98c", "#168aad", "#1e6091"]);
    subunits.style("fill", function(d) {
        if (level === "india") {
            // console.log(
            //     "options ",
            //     options["state_id"][0],
            //     state_code_mapping[d.properties.ST_NM]
            // );
            if (
                options["state_id"][0] === "IN" &&
                (options["min_value"][0] || options["max_value"][0])
            ) {
                if (
                    _.toNumber(options["min_value"][0]) <=
                        _.toNumber(
                            mp_data[state_code_mapping[d.properties.ST_NM]]
                        ) &&
                    _.toNumber(options["max_value"][0]) >=
                        _.toNumber(
                            mp_data[state_code_mapping[d.properties.ST_NM]]
                        )
                )
                    return c(mp_data[state_code_mapping[d.properties.ST_NM]]);
                else return "#fff";
            } else if (options["state_id"][0] === "IN")
                return c(mp_data[state_code_mapping[d.properties.ST_NM]]);
            else {
                if (
                    options["state_id"][0] ===
                    state_code_mapping[d.properties.ST_NM]
                )
                    return c(mp_data[state_code_mapping[d.properties.ST_NM]]);
                else return "#EBEBEB";
            }
        } else if (level === "state") {
            if (
                options["district_id"][0] === "" &&
                (options["min_value"][0] || options["max_value"][0])
            ) {
                if (
                    _.toNumber(options["min_value"][0]) <=
                        _.toNumber(mp_data[d.properties.D_CODE]) &&
                    _.toNumber(options["max_value"][0]) >=
                        _.toNumber(mp_data[d.properties.D_CODE])
                )
                    return c(mp_data[d.properties.D_CODE]);
                else return "#808080";
            } else if (options["district_id"][0] === "")
                return mp_data[d.properties.D_CODE]
                    ? c(mp_data[d.properties.D_CODE])
                    : "#808080";
            else if (options["district_id"][0] === d.properties.D_CODE)
                return mp_data[d.properties.D_CODE]
                    ? c(mp_data[d.properties.D_CODE])
                    : "#808080";
            else return "#EBEBEB";
        }
    });
}

function getBoundingBoxCenter(selection) {
    var element = selection.node();
    var bbox = element.getBBox();
    return [bbox.x + bbox.width / 2, bbox.y + bbox.height / 2];
}

function draw_annotations() {
    $(".annotation-group").remove();
    let prev_quarter = find_prev_quarter(
        options["quarter"][0],
        options["year"][0]
    );
    ajaxchain_fetch([
        "get_state_lowscore?ind=" +
            options["ind"][0] +
            "&quarter=" +
            options["quarter"][0] +
            "&year=" +
            options["year"][0],
        "get_state_highscore?ind=" +
            options["ind"][0] +
            "&quarter=" +
            options["quarter"][0] +
            "&year=" +
            options["year"][0],
        "get_state_highchange?ind=" +
            options["ind"][0] +
            "&quarter=" +
            options["quarter"][0] +
            "&prev_quarter=" +
            prev_quarter.quarter +
            "&year=" +
            options["year"][0] +
            "&prev_year=" +
            prev_quarter.year,
        "get_state_lowchange?ind=" +
            options["ind"][0] +
            "&quarter=" +
            options["quarter"][0] +
            "&prev_quarter=" +
            prev_quarter.quarter +
            "&year=" +
            options["year"][0] +
            "&prev_year=" +
            prev_quarter.year
    ]).ajaxchain_instance.on("done", function(e) {
        let lowest_score, highest_score, highest_change, lowest_change;
        let annotations = [];
        if (e.response[1] != "NA") {
            lowest_score = e.response[1][0];
            let points = getBoundingBoxCenter(
                d3.select("path[data-attr='" + lowest_score.state_id + "']")
            );
            let temp = {
                note: {
                    label: "Scored the lowest this quarter",
                    title: lowest_score.state
                },
                nx: 0,
                ny: 0,
                x: points[0],
                y: points[1]
            };
            annotations.push(temp);
        }
        if (e.response[2] != "NA") {
            highest_score = JSON.parse(e.response[2])[0];
            let points = getBoundingBoxCenter(
                d3.select("path[data-attr='" + highest_score.state_id + "']")
            );
            let temp = {
                note: {
                    label: "Scored the highest this quarter",
                    title: highest_score.state
                },
                nx: 0,
                ny: 0,
                x: points[0],
                y: points[1]
            };
            annotations.push(temp);
        }
        if (e.response[3] != "NA") {
            highest_change = JSON.parse(e.response[3])[0];
            let points = getBoundingBoxCenter(
                d3.select("path[data-attr='" + highest_change.state_id + "']")
            );
            let temp = {
                note: {
                    label: "made the highest positive jump this quarter",
                    title: highest_change.state
                },
                nx: 0,
                ny: 0,
                x: points[0],
                y: points[1]
            };
            annotations.push(temp);
        }
        if (e.response[4] != "NA") {
            lowest_change = JSON.parse(e.response[4])[0];
            let points = getBoundingBoxCenter(
                d3.select("path[data-attr='" + lowest_change.state_id + "']")
            );
            let temp = {
                note: {
                    label: "made the least positive change this quarter",
                    title: lowest_change.state
                },
                nx: 0,
                ny: 0,
                x: points[0],
                y: points[1]
            };
            annotations.push(temp);
        }
        let annotations_points = annotations_coordinates;
        let annotations_counter = annotations.length;
        let i = 0;
        while (i < annotations_counter) {
            let min = 10000;
            let min_annotation;
            for (let j in annotations) {
                let x = annotations[j]["x"] - annotations_points[i]["nx"];
                let y = annotations[j]["y"] - annotations_points[i]["ny"];
                let distance = Math.sqrt(x * x + y * y);
                if (distance < min && annotations[j]["nx"] === 0) {
                    min = distance;
                    min_annotation = j;
                }
            }
            annotations[min_annotation]["nx"] = annotations_points[i]["nx"];
            annotations[min_annotation]["ny"] = annotations_points[i]["ny"];
            i++;
        }
        const type = d3.annotationCalloutElbow;
        const makeAnnotations = d3
            .annotation()
            .editMode(false)
            .notePadding(15)
            .type(type)
            .annotations(annotations);
        document.fonts.ready.then(function() {
            d3.select("div.map-container svg")
                .append("g")
                .attr("class", function() {
                    if (options["m_annotations"][0] == "false") {
                        return "annotation-group d-none";
                    } else {
                        return "annotation-group";
                    }
                })
                .style("transform", "translate(50px , 0px)")
                .style("font-size", 11)
                .call(makeAnnotations);
        });
    });
}
