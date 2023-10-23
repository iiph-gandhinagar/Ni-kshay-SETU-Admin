import {
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip
} from "chart.js";
import React, { useContext, useEffect, useState } from "react";
import { Line } from "react-chartjs-2";
import { AppStoreContext, BASE_URL } from "../components/DashboardApp";
import Lottie from "lottie-react";
import noData from "../json/nodata.json";
import domtoimage from "dom-to-image-more";
import { saveAs } from "file-saver";
import moment from "moment";
import { LeaderboardLevelsCardLoader } from "./SkeletonLoader";
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend
);

export const options = {
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
                    size: 11,
                    weight: 500
                },
                color: "#000000de"
            },
            grid: {
                display: false
            }
        },
        y: {
            ticks: {
                font: {
                    family: "'Nunito', sans-serif",
                    size: 11,
                    weight: 500
                },
                color: "#000000de"
            },
            beginAtZero: true
        }
    },
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            display: false
        }
    }
};
const AssessmentSubmissionCard = ({}) => {
    const [loader, setLoader] = useState(true);
    const [dateFilter, setdateFilter] = useState("overall");
    const { filters } = useContext(AppStoreContext);
    const [data, setData] = useState({
        labels: [],
        datasets: []
    });
    useEffect(() => {
        setLoader(true);
        try {
            fetch(
                BASE_URL +
                    `get-assessment-submission-details?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            )
                .then(res => res.json())
                .then(json => {
                    setData({
                        labels: json?.data?.map(e =>
                            e.week
                                ? moment(e.week)
                                      .format("ll")
                                      ?.split(",")
                                : e?.date?.split("-")
                        ),
                        datasets: [
                            {
                                fill: true,
                                label: "Assessment submission",
                                data: json?.data?.map(e => e?.subscriber_count),
                                borderWidth: 3,
                                borderColor: "#4FEFF9",
                                backgroundColor: "#4feff929",
                                tension: 0.4
                            }
                        ]
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
        <div className="card h-100 mb-0">
            <div className="card-body">
                <div className="d-flex align-items-center justify-content-between mb-3 row p-0 m-0">
                    <h5 className="section-title mb-0 col-12 col-sm-5 p-0 m-0">
                        Assessment Submission{" "}
                    </h5>
                    <div className="d-flex align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
                        <button
                            onClick={() => {
                                domtoimage
                                    .toBlob(
                                        document.getElementById(
                                            "download_Assessment_Chart"
                                        )
                                    )
                                    .then(function(blob) {
                                        saveAs(
                                            blob,
                                            "AssessmentSubmission.png"
                                        );
                                    })
                                    .catch(error => {
                                        console.error(
                                            "oops, something went wrong!",
                                            error
                                        );
                                    });
                            }}
                            className="btn btn-primary btn-green me-sm-3 me-1 py-1"
                        >
                            <i className="fa fa-image"></i>
                        </button>
                        <button
                            onClick={() => setdateFilter("overall")}
                            type="button"
                            className={
                                dateFilter === "overall"
                                    ? "btn btn-primary btn-green me-3 py-1"
                                    : "btn btn-outline-secondary btn-white me-3 py-1"
                            }
                        >
                            Overall
                        </button>
                        <button
                            disabled={filters.date}
                            onClick={() => setdateFilter("last 30 days")}
                            type="button"
                            className={
                                dateFilter === "overall"
                                    ? "btn btn-outline-secondary btn-white py-1"
                                    : "btn btn-primary btn-green py-1"
                            }
                        >
                            Last 30 Days
                        </button>
                    </div>
                </div>
                <div
                    id="download_Assessment_Chart"
                    className="assessment-submission-card"
                >
                    {loader ? (
                        <LeaderboardLevelsCardLoader />
                    ) :data.labels.length > 0 ? (
                        <Line
                            options={options}
                            data={data}
                            style={{ height: 300 }}
                        />
                    ) : (
                        <Lottie
                            style={{ height: 300 }}
                            animationData={noData}
                            loop={true}
                        />
                    )}
                </div>
            </div>
        </div>
    );
};
export default AssessmentSubmissionCard;
