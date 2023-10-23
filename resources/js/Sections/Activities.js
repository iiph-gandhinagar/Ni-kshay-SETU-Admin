import React, { useContext, useEffect, useState } from "react";
import { AppStoreContext, BASE_URL } from "../components/DashboardApp";
import ActivitiesCards from "../components/ActivitiesCards";
import { ActivitiesCardsSkeletonLoader } from "../components/SkeletonLoader";
const month = [
    "",
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec"
];
const time = [
    "00 AM",
    "01 AM",
    "02 AM",
    "03 AM",
    "04 AM",
    "05 AM",
    "06 AM",
    "07 AM",
    "08 AM",
    "09 AM",
    "10 AM",
    "11 AM",
    "12 PM",
    "13 PM",
    "14 PM",
    "15 PM",
    "16 PM",
    "17 PM",
    "18 PM",
    "19 PM",
    "20 PM",
    "21 PM",
    "22 PM",
    "23 PM",
    "24 PM"
];
function last12Month() {
    var dates = [];
    var d = new Date();
    var y = d
        .getFullYear()
        .toString()
        .substr(-2);
    var m = d.getMonth();
    if (m === 11) {
        for (var i = 1; i < 13; i++) {
            dates.push(month[i] + "-" + y);
        }
    } else {
        for (var i = m + 1; i < m + 13; i++) {
            if (i % 12 > m) {
                dates.push(month[i + 1] + "-" + (y - 1));
            } else {
                dates.push(month[(i % 12) + 1] + "-" + y);
            }
        }
    }
    return dates;
}
const ActivitiesSection = ({}) => {
    const { filters } = useContext(AppStoreContext);
    const [dateFilter, setdateFilter] = useState("total");
    const [data, setData] = useState({});
    const [loading, setLoading] = useState(false);
    const [months, setMonths] = useState(last12Month());
    useEffect(() => {
        setMonths(last12Month());
        setLoading(true);
        try {
            fetch(
                BASE_URL +
                    `get-count-list-data?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            )
                .then(res => res.json())
                .then(json => {
                    setData(json?.data);
                    setLoading(false);
                })
                .catch(error => {
                    console.log("response catch", error);
                    setLoading(false);
                });
        } catch (error) {
            console.log("response catch", error);
            setLoading(false);
        }
    }, [dateFilter, filters]);
    return (
        <div className="card p-3">
            <div className="d-flex align-items-center justify-content-between mb-3">
                <h5 className="section-title mb-0">Activities</h5>
                <div>
                    <button
                        onClick={() => setdateFilter("total")}
                        type="button"
                        className={
                            dateFilter === "total"
                                ? "btn btn-primary btn-green me-3"
                                : "btn btn-outline-secondary btn-white me-3"
                        }
                        style={{ width: 85 }}
                    >
                        Total
                    </button>
                    <button
                        disabled={filters.date}
                        onClick={() => setdateFilter("today")}
                        type="button"
                        className={
                            dateFilter === "total"
                                ? "btn btn-outline-secondary btn-white"
                                : "btn btn-primary btn-green"
                        }
                        style={{ width: 85 }}
                    >
                        Today
                    </button>
                </div>
            </div>
            <div className="row g-3">
                {loading ? (
                    <ActivitiesCardsSkeletonLoader />
                ) : (
                    <ActivitiesCards
                        dateFilter={dateFilter}
                        Assessment={data?.total_assessment_completed}
                        AssessmentGrowth={
                            filters.date
                                ? data?.assessment_graph?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : dateFilter == "total"
                                ? data?.assessment_graph?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : time?.map((e, i) => {
                                      return {
                                          date: e,
                                          "Subscriber Count":
                                              data?.assessment_graph?.find(
                                                  data => data.date == e
                                              )?.subscriber_count || 0
                                      };
                                  })
                        }
                        Subscribers={data?.total_subscriber}
                        SubscribersGrowth={
                            filters.date
                                ? data?.subscriber_growth?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : dateFilter == "total"
                                ? data?.subscriber_growth?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : time?.map((e, i) => {
                                      return {
                                          date: e,
                                          "Subscriber Count":
                                              data?.subscriber_growth?.find(
                                                  data => data.date == e
                                              )?.subscriber_count || 0
                                      };
                                  })
                        }
                        Visit={data?.total_subscriber_activity}
                        VisitGrowth={
                            filters.date
                                ? data?.subscriber_activity_growth?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : dateFilter == "total"
                                ? data?.subscriber_activity_growth?.map(e => {
                                      return {
                                          date: e?.date,
                                          "Subscriber Count": e?.subscriber_count
                                      };
                                  })
                                : time?.map((e, i) => {
                                      return {
                                          date: e,
                                          "Subscriber Count":
                                              data?.subscriber_activity_growth?.find(
                                                  data => data.date == e
                                              )?.subscriber_count || 0
                                      };
                                  })
                        }
                    />
                )}
            </div>
        </div>
    );
};
export default ActivitiesSection;
