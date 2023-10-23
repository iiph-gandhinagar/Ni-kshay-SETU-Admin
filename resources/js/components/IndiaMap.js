import React, { useContext, useEffect, useState } from "react";
import { AppStoreContext } from "../components/DashboardApp";
export default function IndiaMap({ subscribers_presence }) {
  const [loader, setLoader] = useState(true);
  const { setFilterConfig, filters } = useContext(AppStoreContext);
  useEffect(() => {
    var w = 650;
    var h = 800;
    var proj = window.d3.geo.mercator();
    var path = window.d3.geo.path().projection(proj);
    var data_max_val = Math.max.apply(
      Math,
      subscribers_presence?.map((o) => o?.TotalCount)
    );
    var map = window.d3
      .select("#chart")
      .append("svg:svg")
      .attr("width", w)
      .attr("height", h)
      .attr("viewBox", "0 0 " + w + " " + h)
      .attr("preserveAspectRatio", "xMinYMin")
      //.call(window.d3.behavior.zoom().on("zoom", redraw))
      .call(initialize);
    var india = map.append("svg:g").attr("id", "india");
    var g = map.append("svg:g").attr("id", "circle");

    var div = window.d3
      .select("body")
      .append("div")
      .attr("class", "tooltip")
      .attr("id", "tooltip")

      .style("opacity", 0);
    switch (filters.state) {
      case 1 || "1":
        window.d3.json("/data/GUJARAT.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );
              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 2 || "2":
        window.d3.json("/data/JHARKHAND.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 3 || "3":
        window.d3.json("/data/ANDAMAN & NICOBAR.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 4 || "4":
        window.d3.json("/data/ANDHRAPRADESH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 5 || "5":
        window.d3.json("/data/ARUNACHALPRADESH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 6 || "6":
        window.d3.json("/data/ASSAM.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 7 || "7":
        window.d3.json("/data/BIHAR.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 8 || "8":
        window.d3.json("/data/CHANDIGARH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 9 || "9":
        window.d3.json("/data/CHHATTISGARH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 10 || "10":
        window.d3.json("/data/DADRAANDNAGARHAVELI.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 11 || "11":
        window.d3.json("/data/DAMAN&DIU.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 12 || "12":
        window.d3.json("/data/DELHI.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 13 || "13":
        window.d3.json("/data/GOA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 14 || "14":
        window.d3.json("/data/HARYANA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 15 || "15":
        window.d3.json("/data/HIMACHALPRADESH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 16 || "16":
        window.d3.json("/data/JAMMUANDKASHMIR.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 17 || "17":
        window.d3.json("/data/KARNATAKA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 18 || "18":
        window.d3.json("/data/KERALA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 20 || "20":
        window.d3.json("/data/MADHYAPRADESH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 21 || "21":
        window.d3.json("/data/MAHARASHTRA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 22 || "22":
        window.d3.json("/data/MANIPUR.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 23 || "23":
        window.d3.json("/data/MEGHALAYA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 24 || "24":
        window.d3.json("/data/MIZORAM.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 25 || "25":
        window.d3.json("/data/NAGALAND.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 26 || "26":
        window.d3.json("/data/ODISHA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 27 || "27":
        window.d3.json("/data/PUDUCHERRY.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 28 || "28":
        window.d3.json("/data/PUNJAB.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 29 || "29":
        window.d3.json("/data/RAJASTHAN.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 30 || "30":
        window.d3.json("/data/SIKKIM.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 31 || "31":
        window.d3.json("/data/TAMILNADU.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 33 || "33":
        window.d3.json("/data/TRIPURA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 34 || "34":
        window.d3.json("/data/UTTARPRADESH.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 35 || "35":
        window.d3.json("/data/UTTARAKHAND.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      case 36 || "36":
        window.d3.json("/data/WESTBENGAL.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368AC7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );
              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });
        break;
      case 38 || "38":
        window.d3.json("/data/TELANGANA.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.DISTRICTS_2018
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.district == d?.properties?.D_CODE) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${filters?.state}&ditrictId=${d?.properties?.D_CODE}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.district_id == d?.properties?.D_CODE
              )?.TotalCount;
              if (counts) {
                return 0;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
      default:
        window.d3.json("/data/india.topo.json", function (json) {
          const data = window.topojson.feature(
            json,
            json.objects.india
          ).features;
          india
            .selectAll("path")
            .data(data)
            .enter()
            .append("path")
            .attr("d", path)
            .style("stroke", "#22577E")
            .style("fill", function (d, i) {
              if (filters?.state == d?.properties?.state_id) {
                return "#368ac7";
              }
              return "#FFF";
            })
            .style("opacity", 1)
            .on("mouseover", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 0.5);
              div.transition().duration(300).style("opacity", 1);
              const counts = subscribers_presence?.find(
                (e) => e?.state_id == d?.properties?.state_id
              );

              if (counts) {
                let tooltip = document.getElementById("tooltip");
                tooltip.innerHTML =
                  counts?.title + " ( " + counts?.TotalCount + " ) ";
                tooltip.style.display = "block";
                tooltip.style.left = window.d3.event.pageX + "px";
                tooltip.style.top = window.d3.event.pageY - 30 + "px";
              }
            })
            .on("click", function (d, i) {
              window.location.replace(`/?stateId=${d?.properties?.state_id}&ditrictId=${filters.district}&blockId=${filters.block}&date=${filters.date}`)
            })
            .on("mouseout", function (d, i) {
              window.d3
                .select(this)
                .transition()
                .duration(300)
                .style("opacity", 1);
              div.transition().duration(300).style("opacity", 1);
              var tooltip = document.getElementById("tooltip");
              tooltip.style.display = "none";
            });
          g.selectAll("circle")
            .data(data)
            .enter()
            .append("circle")
            .attr("class", "bubble")
            .attr("r", function (d) {
              const counts = subscribers_presence?.find(
                (e) => e?.state_id == d?.properties?.state_id
              )?.TotalCount;
              if (counts) {
                return 3;
              }
              return 0;
            })
            .attr("cx", function (d) {
              var t = path.centroid(d?.geometry);
              d.x = t[0];
              d.y = t[1];
              return d.x;
            })
            .attr("cy", function (d) {
              return d.y;
            })
            .style("fill", "#165BAA");
          setLoader(false);
        });

        break;
    }
    function initialize() {
      switch (filters.state) {
        case 1 || "1":
          proj.scale(28990);
          proj.translate([-5450, 2300]);

          break;
        case 2 || "2":
          proj.scale(45900);
          proj.translate([-10600, 3500]);
          break;
        case 3 || "3":
          proj.scale(37000);
          proj.translate([-9300, 1500]);

          break;
        case 4 || "4":
          proj.scale(26000);
          proj.translate([-5500, 1600]);

          break;
        case 5 || "5":
          proj.scale(38990);
          proj.translate([-9900, 3500]);
          break;
        case 6 || "6":
          proj.scale(35000);
          proj.translate([-8700, 3100]);
          break;
        case 7 || "7":
          proj.scale(41200);
          proj.translate([-9500, 3400]);

          break;
        case 8 || "8":
          proj.scale(466900);
          proj.translate([-99300, 42300]);
          break;
        case 9 || "9":
          proj.scale(40000);
          proj.translate([-8800, 2800]);

          break;
        case 10 || "10":
          proj.scale(95900);
          proj.translate([-19200, 5900]);

          break;
        case 11 || "11":
          proj.scale(78000);
          proj.translate([-15200, 4800]);
          break;
        case 12 || "12":
          proj.scale(254200);
          proj.translate([-54200, 21500]);

          break;
        case 13 || "13":
          proj.scale(154000);
          proj.translate([-31400, 7000]);
          break;
        case 14 || "14":
          proj.scale(57000);
          proj.translate([-11750, 5200]);
          break;
        case 15 || "15":
          proj.scale(58300);
          proj.translate([-12200, 5790]);
          break;
        case 16 || "16":
          proj.scale(39000);
          proj.translate([-7980, 4300]);
          break;
        case 17 || "17":
          proj.scale(27900);
          proj.translate([-5599, 1600]);
          break;
        case 18 || "18":
          proj.scale(52900);
          proj.translate([-10900, 2000]);
          break;
        case 20 || "20":
          proj.scale(22990);
          proj.translate([-4700, 1980]);

          break;
        case 21 || "21":
          proj.scale(22990);
          proj.translate([-4600, 1680]);

          break;
        case 22 || "22":
          proj.scale(93900);
          proj.translate([-24200, 7100]);
          break;
        case 23 || "23":
          proj.scale(77000);
          proj.translate([-19200, 6000]);
          break;
        case 24 || "24":
          proj.scale(93900);
          proj.translate([-23900, 6630]);
          break;
        case 25 || "25":
          proj.scale(76000);
          proj.translate([-19600, 6000]);
          break;
        case 26 || "26":
          proj.scale(34000);
          proj.translate([-7630, 2400]);
          break;
        case 27 || "27":
          proj.scale(43900);
          proj.translate([-9100, 1900]);
          break;
        case 28 || "28":
          proj.scale(59900);
          proj.translate([-12200, 5840]);

          break;
        case 29 || "29":
          proj.scale(24990);
          proj.translate([-4800, 2350]);

          break;
        case 30 || "30":
          proj.scale(98300);
          proj.translate([-23900, 8300]);
          break;
        case 31 || "31":
          proj.scale(41000);
          proj.translate([-8650, 1650]);
          break;
        case 33 || "33":
          proj.scale(82010);
          proj.translate([-20600, 5950]);
          break;
        case 34 || "34":
          proj.scale(24990);
          proj.translate([-5300, 2350]);

          break;
        case 35 || "35":
          proj.scale(43000);
          proj.translate([-9200, 4150]);

          break;
        case 36 || "36":
          proj.scale(34000);
          proj.translate([-8000, 2800]);
          break;
        case 38 || "38":
          proj.scale(46000);
          proj.translate([-9800, 2700]);

          break;
        default:
          proj.scale(7000);
          proj.translate([-1310, 860]);
          break;
      }
    }

    return function clenUP() {
      window.d3.selectAll("#chart svg").remove();
      window.d3.selectAll("#tooltip").remove();
    };
  }, [subscribers_presence, filters]);
  return (
    <div id="chart" className="india-map">
    </div>
  );
}
