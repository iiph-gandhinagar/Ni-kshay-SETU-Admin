import React, { useContext, useEffect, useState } from 'react';
import Skeleton from 'react-loading-skeleton';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import IndiaMap from "../components/IndiaMap";
import { StateWiseListCardSkeletonLoader } from '../components/SkeletonLoader';
import StateWiseListCard from '../components/StateWiseListCard';
import Lottie from "lottie-react";
import noData from "../json/nodata.json"
const MapSection = () => {
    const [loading, setLoading] = useState(false)
    const { filters } = useContext(AppStoreContext);
    const [data, setData] = useState({})
    const [stateData, setStateData] = useState([])
    const getAllState = () => {
        try {
            fetch(BASE_URL + 'api/get-all-state').then((res) => res.json())
                .then((json) => {
                    setStateData(json?.data)
                    // console.log("State response json", json?.data);
                }).catch((error) => {
                    console.log("response catch", error);
                })

        } catch (error) {
            console.log("response catch", error);
        }
    }
    useEffect(() => {
        getAllState()
        setLoading(true)
        try {
            fetch(BASE_URL + `get-subscribers-count?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
                .then((json) => {
                    setData(json?.data)
                    setLoading(false)
                }).catch((error) => {
                    console.log("response catch", error);
                    setLoading(false)
                })

        } catch (error) {
            console.log("response catch", error);
            setLoading(false)
        }
    }, [filters])
    return (
        <div className="card p-0">
            <div className="row">
                <div className="col-xl-7">
                    <h5 className="section-title p-3 mb-0">Subscribers Count</h5>
                    <h6 className="card-tag px-2 text-end" style={{ maxWidth: 206, borderBottomRightRadius: 4, borderTopRightRadius: 4 }}>
                        {filters?.block ? "Health Facility Wise" : filters?.district ? "Block Wise" : filters?.state ? "District Wise" : "State Wise"}

                    </h6>
                    <div className="state-wise-list mb-3 custom-scrollbar">
                        {loading ?
                            <div key={"state_wise_subscriber- loader"} >
                                <StateWiseListCardSkeletonLoader />
                                <StateWiseListCardSkeletonLoader />
                                <StateWiseListCardSkeletonLoader />
                                <StateWiseListCardSkeletonLoader />
                            </div>
                            :
                            <>
                                {data?.state_wise_subscriber?.length > 0 ? data?.state_wise_subscriber?.map((item, i) => {
                                    return (
                                        <StateWiseListCard
                                            key={i + " - state_wise_subscriber - " + item?.title}
                                            title={item?.title}
                                            SubscribersToday={item?.todays_subscriber}
                                            SubscribersTotal={item?.TotalCount}
                                            percentage={item?.percentage}
                                        />
                                    )
                                })
                                    : <Lottie style={{ height: 300 }} animationData={noData} loop={true} />
                                }
                            </>
                        }

                    </div>
                </div>
                <div className="col-xl-5">
                    <h5 className="section-title p-3 mb-0" style={{ textAlign: 'end' }}>{filters.state == 0 ? "India" : ((stateData?.find((e) => e.id == filters.state)?.title||"") + " STATE")} Map</h5>
                    <div className="d-flex align-items-center justify-content-end">
                        {loading ? <h6 className="card-tag px-2 text-start" style={{ borderBottomLeftRadius: 4, borderTopLeftRadius: 4 }}><Skeleton width={110} /></h6> :
                            <h6 className="card-tag px-2 text-start"
                                style={{ borderBottomLeftRadius: 4, borderTopLeftRadius: 4 }}>
                                {filters.state == 0 ? "National " : filters.block != 0 ? "Block " : filters.district != 0 ? "District " : "State "}
                                Subscribers: {data?.state_level_subscriber || 0}</h6>
                        }</div>
                    <IndiaMap subscribers_presence={data?.state_wise_subscriber} />

                </div>
            </div>
        </div>
    );
}
export default MapSection;