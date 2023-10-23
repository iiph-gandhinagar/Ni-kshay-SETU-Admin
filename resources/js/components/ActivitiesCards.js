import React, { useContext } from "react";
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer
} from "recharts";
import CountUp from "react-countup";
import { AppStoreContext } from "../components/DashboardApp";
import moment from "moment";
const ActivitiesCards = ({
    Subscribers = "0",
    Assessment = "0",
    Visit = "0",
    dateFilter,
    AssessmentGrowth = [],
    SubscribersGrowth = [],
    VisitGrowth = []
}) => {
    const { filters } = useContext(AppStoreContext);
    return (
        <>
            <div className="col-xxl-4 col-xl-4 col-md-6">
                <a
                    href={`/admin/subscribers?from_date=${
                        dateFilter === "total"
                            ? filters?.date?.split(" ")?.[0] || ""
                            : moment(new Date()).format("YYYY-MM-DD")
                    }&to_date=${filters?.date?.split(" ")?.[1] ||
                        ""}&state_id=${filters?.state ||
                        ""}&district_id[]=${filters?.district ||
                        []}&block_id=${filters?.block || ""}`}
                >
                    <div className="card bg-FFFBFB info-card activity-card mb-0 cursor-pointer">
                        <div className="card-body p-0">
                            <div className="d-flex align-items-center py-3 info-area">
                                <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <img
                                        src="/images/fluent_people-audience-24-regular.png"
                                        alt="icon"
                                        style={{ width: 50 }}
                                    />
                                </div>
                                <div className="ps-3">
                                    <h5 className="mb-0">
                                        <CountUp
                                            end={Subscribers || 0}
                                            duration={3}
                                        />
                                    </h5>
                                    <span className="pt-1 fw-semibold">
                                        {dateFilter === "total"
                                            ? "Total"
                                            : "Today"}{" "}
                                        Subscribers
                                    </span>
                                </div>
                            </div>
                            <div className="area-chart">
                                <div className="area-chart-container">
                                    <ResponsiveContainer
                                        width="100%"
                                        height="100%"
                                    >
                                        <AreaChart
                                            width={200}
                                            height={60}
                                            data={SubscribersGrowth}
                                            margin={{
                                                top: 5,
                                                right: 0,
                                                left: 0,
                                                bottom: 5
                                            }}
                                        >
                                            <Area
                                                type="monotone"
                                                dataKey="Subscriber Count"
                                                strokeWidth={3}
                                                stroke="#CF6E6E"
                                                fill="url(#color1)"
                                            />
                                            <defs>
                                                <linearGradient
                                                    id="color1"
                                                    x1="0"
                                                    y1="0"
                                                    x2="0"
                                                    y2="1"
                                                >
                                                    <stop
                                                        offset="25%"
                                                        stopColor="#FF0000"
                                                        stopOpacity={0.7}
                                                    />
                                                    <stop
                                                        offset="100%"
                                                        stopColor="#FFFFFF"
                                                        stopOpacity={0}
                                                    />
                                                </linearGradient>
                                            </defs>
                                            <Tooltip />
                                            <XAxis
                                                axisLine={false}
                                                dataKey="date"
                                                style={{ fontSize: 11 }}
                                            />
                                        </AreaChart>
                                    </ResponsiveContainer>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div className="col-xxl-4 col-xl-4 col-md-6">
                <a
                    href={`/admin/subscriber-activities?from_date=${
                        dateFilter === "total"
                            ? filters?.date?.split(" ")?.[0] || ""
                            : moment(new Date()).format("YYYY-MM-DD")
                    }&to_date=${filters?.date?.split(" ")?.[1] ||
                        ""}&state_id=${filters?.state ||
                        ""}&district_id=${filters?.district ||
                        ""}&block_id=${filters?.block || ""}`}
                >
                    <div className="card bg-FAFFF8 info-card activity-card mb-0 cursor-pointer">
                        <div className="card-body p-0">
                            <div className="d-flex align-items-center py-3 info-area">
                                <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <img
                                        src="/images/assessment-done.png"
                                        alt="icon"
                                        style={{ width: 50 }}
                                    />
                                </div>
                                <div className="ps-3">
                                    <h5 className="mb-0">
                                        <CountUp
                                            end={Visit || 0}
                                            duration={3}
                                        />
                                    </h5>
                                    <span className="pt-1 fw-semibold">
                                        {dateFilter === "total"
                                            ? "Total"
                                            : "Today"}{" "}
                                        Visit's
                                    </span>
                                </div>
                            </div>
                            <div className="area-chart">
                                <div className="area-chart-container">
                                    <ResponsiveContainer
                                        width="100%"
                                        height="100%"
                                    >
                                        <AreaChart
                                            width={200}
                                            height={60}
                                            data={VisitGrowth}
                                            margin={{
                                                top: 5,
                                                right: 0,
                                                left: 0,
                                                bottom: 5
                                            }}
                                        >
                                            <Tooltip />
                                            <Area
                                                type="monotone"
                                                dataKey="Subscriber Count"
                                                strokeWidth={3}
                                                stroke="#6CB04D"
                                                fill="url(#color2)"
                                            />
                                            <defs>
                                                <linearGradient
                                                    id="color2"
                                                    x1="0"
                                                    y1="0"
                                                    x2="0"
                                                    y2="1"
                                                >
                                                    <stop
                                                        offset="25%"
                                                        stopColor="#50FF00"
                                                        stopOpacity={0.7}
                                                    />
                                                    <stop
                                                        offset="100%"
                                                        stopColor="#FFFFFF"
                                                        stopOpacity={0}
                                                    />
                                                </linearGradient>
                                            </defs>
                                            <XAxis
                                                axisLine={false}
                                                dataKey="date"
                                                style={{ fontSize: 11 }}
                                            />
                                        </AreaChart>
                                    </ResponsiveContainer>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div className="col-xxl-4 col-xl-4">
                <a
                    href={`/admin/user-assessments?from_date=${
                        dateFilter === "total"
                            ? filters?.date?.split(" ")?.[0] || ""
                            : moment(new Date()).format("YYYY-MM-DD")
                    }&to_date=${filters?.date?.split(" ")?.[1] ||
                        ""}&state_id=${filters?.state ||
                        ""}&district_id=${filters?.district ||
                        ""}&block_id=${filters?.block || ""}`}
                >
                    <div className="card bg-F8FAFF info-card activity-card mb-0 cursor-pointer">
                        <div className="card-body p-0">
                            <div className="d-flex align-items-center py-3 info-area">
                                <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <img
                                        src="/images/fluent_people-add-20-regular.png"
                                        alt="icon"
                                        style={{ width: 50 }}
                                    />
                                </div>
                                <div className="ps-3">
                                    <h5 className="mb-0">
                                        <CountUp
                                            end={Assessment || 0}
                                            duration={3}
                                        />
                                    </h5>
                                    <span className="pt-1 fw-semibold">
                                        Assessment Completed
                                    </span>
                                </div>
                            </div>
                            <div className="area-chart">
                                <div className="area-chart-container">
                                    <ResponsiveContainer
                                        width="100%"
                                        height="100%"
                                    >
                                        <AreaChart
                                            width={200}
                                            height={60}
                                            data={AssessmentGrowth}
                                            margin={{
                                                top: 5,
                                                right: 0,
                                                left: 0,
                                                bottom: 5
                                            }}
                                        >
                                            <Tooltip />
                                            <Area
                                                type="monotone"
                                                dataKey="Subscriber Count"
                                                strokeWidth={3}
                                                stroke="#5870AC"
                                                fill="url(#color3)"
                                            />
                                            <defs>
                                                <linearGradient
                                                    id="color3"
                                                    x1="0"
                                                    y1="0"
                                                    x2="0"
                                                    y2="1"
                                                >
                                                    <stop
                                                        offset="25%"
                                                        stopColor="#0049FF"
                                                        stopOpacity={0.7}
                                                    />
                                                    <stop
                                                        offset="100%"
                                                        stopColor="#FFFFFF"
                                                        stopOpacity={0}
                                                    />
                                                </linearGradient>
                                            </defs>
                                            <XAxis
                                                axisLine={false}
                                                dataKey="date"
                                                style={{ fontSize: 11 }}
                                            />
                                        </AreaChart>
                                    </ResponsiveContainer>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </>
    );
};
export default ActivitiesCards;
