import React, { useContext, useEffect, useMemo, useState } from "react";
import AppOpenCountCard from "./AppOpenCountCard";
import Profiles from "./Profiles";
import AssessmentSubmissionCard from "./AssessmentSubmissionCard";
import CadreWiseSubscribersCard from "./CadreWiseSubscribersCard";
import ChatbotKeyboardHitsCard from "./ChatbotKeyboardHitsCard";
import ChatbotQuestionsHitsCard from "./ChatbotQuestionsHitsCard";
import FilterDropdown from "./FilterDropdown";
import LeaderboardLevelsCard from "./LeaderboardLevelsCard";
import ModuleUsageCard from "./ModuleUsageCard";
// import IndiaMap from './IndiaMap';
import StateWiseListCard from "./StateWiseListCard";
import UsageCards from "./UsageCards";
import UserFeedbackCard from "./UserFeedbackCard";
import ActivitiesSection from "../Sections/Activities";
import MapSection from "../Sections/MapSection";
export const AppStoreContext = React.createContext({});
export const BASE_URL = "/";
import ReactDOM from "react-dom/client";
import "react-loading-skeleton/dist/skeleton.css";
import queryString from "query-string";
import Lottie from "lottie-react";
import loader from "../json/loader.json";
import "react-circular-progressbar/dist/styles.css";
import Navbar from "./Navbar";
const DashboardApp = () => {
    return (
        <section className="section dashboard">
              <div className='container'>
                <Navbar />
                <ActivitiesSection />
                <div className="row">
                    <UsageCards />
                </div>
                <MapSection />
                <div className="row">
                    <div className="col-lg-6 mb-3">
                        <CadreWiseSubscribersCard />
                    </div>

                    <div className="col-lg-6 mb-3">
                        <ModuleUsageCard />
                    </div>
                </div>
                <div className="row">
                    <div className="col-lg-12">
                        <LeaderboardLevelsCard />
                    </div>
                </div>
                <div className="row">
                    <div className="col-lg-6 mb-3">
                        <ChatbotQuestionsHitsCard />
                    </div>
                    <div className="col-lg-6 mb-3">
                        <ChatbotKeyboardHitsCard />
                    </div>
                </div>
                <div className="row">
                    <div className="col-lg-6 mb-3">
                        <UserFeedbackCard />
                    </div>
                    <div className="col-lg-6 mb-3">
                        <AssessmentSubmissionCard />
                    </div>
                </div>
                <div className="row">
                    <div className="col-lg-12">
                        <AppOpenCountCard />
                    </div>
                </div>
            </div>
        </section>
    );
};

const RootApp = () => {
    const [isLoading, setLoading] = useState(true);
    const queryObj = queryString?.parse(window.location.search);
    const [filters, setFilters] = useState({
        state: parseInt(queryObj?.stateId || 0),
        district: parseInt(queryObj?.ditrictId || 0),
        block: parseInt(queryObj?.blockId || 0),
        date: queryObj?.date || ""
    });
    const Context = () => {
        return {
            setFilterConfig: data => {
                setFilters(old => data);
            },
            filters: filters
        };
    };
    const appContext = useMemo(() => Context(), [filters]);
    const getrolewiseState = () => {
        try {
            fetch(BASE_URL + "get-role-wise-state")
                .then(res => res.json())
                .then(json => {
                    console.log(
                        "filters.state == parseInt(json?.state_id)",
                        filters.state,
                        json?.state_id || 0,
                        filters.state == (json?.state_id || 0)
                    );
                    if (json?.state_id) {
                        if (filters.state == json?.state_id) {
                            setLoading(false);
                        } else {
                            window.location.replace(
                                `/?stateId=${parseInt(json?.state_id) ||
                                    0}&ditrictId=${parseInt(
                                    queryObj?.ditrictId || 0
                                )}&blockId=${parseInt(
                                    queryObj?.blockId || 0
                                )}&date=${queryObj?.date || ""}`
                            );
                        }
                    } else {
                        setLoading(false);
                    }
                })
                .catch(error => {
                    console.log("response catch", error);
                    setLoading(false);
                });
        } catch (error) {
            console.log("response catch", error);
        }
    };
    useEffect(() => {
        setLoading(true);
        getrolewiseState();
    }, []);
    return (
        <AppStoreContext.Provider value={appContext}>
            {isLoading ? (
                <div className="vh-100 d-flex align-items-center justify-content-center">
                    <Lottie
                        style={{ height: 300 }}
                        animationData={loader}
                        loop={true}
                    />
                </div>
            ) : (
                <DashboardApp />
            )}
        </AppStoreContext.Provider>
    );
};

const root = ReactDOM.createRoot(document.getElementById("dashboard-app"));
export default RootApp;
if (document.getElementById("dashboard-app")) {
    root.render(<RootApp />);
}
