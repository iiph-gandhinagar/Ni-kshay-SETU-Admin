import React, { useContext, useEffect, useState } from "react";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from "chart.js";
import { Line } from "react-chartjs-2";
import { AppStoreContext, BASE_URL } from "../components/DashboardApp";
import moment from "moment";
import { LeaderboardLevelsCardLoader } from "../components/SkeletonLoader";
import domtoimage from "dom-to-image-more";
import { saveAs } from "file-saver";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);
const options = {
    elements: {
        point: {
            radius: 3
        }
    },
    scales: {
        x: {
            ticks: {
                font: {
                    family: "'Nunito', sans-serif",
                    size: 12,
                    weight: 400
                },
                color: "#707070"
            }
        },
        y: {
            ticks: {
                font: {
                    family: "'Nunito', sans-serif",
                    size: 12,
                    weight: 400
                },
                color: "#707070"
            }
        }
    },
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            labels: {
                font: {
                    family: "'Raleway', sans-serif",
                    size: 15,
                    weight: 600
                },
                padding: 20,
                usePointStyle: true,
                color: "#000000"
            },
            position: "right",
            display: true
        }
    }
};
const backgroundColor = ["#FF8C00", "#0795F4", "#8541FF", "#8DCD03"];
const AppOpenCountCard = () => {
    const [loader, setLoader] = useState(true);
    const [dateFilter, setdateFilter] = useState("weekly");
    const { filters } = useContext(AppStoreContext);
    const [datas, setData] = useState({
        label: [],
        datasets: []
    });
    useEffect(() => {
        try {
            setLoader(true);
            fetch(
                BASE_URL +
                    `get-app-opened-count?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            )
                .then(res => res.json())
                .then(json => {
                    setData({
                        labels: json?.data[
                            Object.keys(json?.data)[0]
                        ]?.map((e, i) => [
                            `${e.month ? "Month" : "Week"} ${i + 1}`,
                            e.month ? e.month : e.week
                        ]),
                        datasets: Object.keys(json?.data).map((key, i) => {
                            return {
                                label: key,
                                data: json?.data[key]?.map(e => e.count),
                                tension: 0.4,
                                backgroundColor: backgroundColor[i],
                                borderColor: backgroundColor[i]
                            };
                        })
                    });
                    setLoader(false);
                })
                .catch(error => {
                    setLoader(false);
                    console.log("response catch", error);
                });
        } catch (error) {
            console.log("response catch", error);
        }
    }, [filters, dateFilter]);
    return (
        <div className="card">
            <div className="card-body">
                <div className="d-flex align-items-center justify-content-between mb-3 row p-0 m-0">
                    <div className="col-12 col-sm-5 p-0 m-0">
                        <h5 className="section-title mb-0">App. Open Count </h5>
                        {filters?.state ||
                        filters?.district ||
                        filters?.block ||
                        filters?.date ? (
                            <span className="message-text" role="alert">
                                * Filters can not be applied to this field
                            </span>
                        ) : null}
                    </div>
                    <div className="d-flex align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
                        <div className="dropdown me-sm-3 me-1 py-1">
                            <button
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                data-bs-auto-close="outside"
                                className="btn btn-primary btn-green me-sm-3 me-1 py-1"
                            >
                                <i className="fa fa-download"></i>
                            </button>
                            <div className="dropdown-menu p-3">
                                <a
                                    class="dropdown-item"
                                    href="export-app-opened-count-3-to-5"
                                >
                                    <i className="fa fa-download"></i>{" >3-5"}
                                </a>
                                <a
                                    class="dropdown-item"
                                    href="export-app-opened-count-5-to-7"
                                >
                                    <i className="fa fa-download"></i>{" >5-7"}
                                </a>
                                <a
                                    class="dropdown-item"
                                    href="export-app-opened-count-7-to-9"
                                >
                                    <i className="fa fa-download"></i>{" >7-9"}
                                </a>
                                <a
                                    class="dropdown-item"
                                    href="export-app-opened-count-10"
                                >
                                    <i className="fa fa-download"></i>{" >10"}
                                </a>
                            </div>
                        </div>
                                    <button onClick={() => {
              domtoimage.toBlob(document.getElementById('download_App_open_Chart')).then(function (blob) {
                saveAs(blob, 'AppOpenCount.png');
              }).catch((error) => {
                console.error("oops, something went wrong!", error);
              });
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-image"></i></button>
                        <button
                            onClick={() => setdateFilter("weekly")}
                            type="button"
                            className={
                                dateFilter === "weekly"
                                    ? "btn btn-primary btn-green me-3 py-1"
                                    : "btn btn-outline-secondary btn-white me-3 py-1"
                            }
                        >
                            Weekly
                        </button>
                        <button
                            onClick={() => setdateFilter("monthly")}
                            type="button"
                            className={
                                dateFilter === "weekly"
                                    ? "btn btn-outline-secondary btn-white py-1"
                                    : "btn btn-primary btn-green py-1"
                            }
                        >
                            Monthly
                        </button>
                    </div>
                </div>
                <div>
                    {loader ? (
                        <LeaderboardLevelsCardLoader />
                    ) : (
                        <Line
                            id="download_App_open_Chart"
                            options={options}
                            data={datas}
                            style={{ height: 300, backgroundColor: "white" }}
                        />
                    )}
                </div>
            </div>
        </div>
    );
};
export default AppOpenCountCard;
