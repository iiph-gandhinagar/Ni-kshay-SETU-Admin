import React, { useContext, useEffect, useState } from 'react';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import CountUp from 'react-countup';
import { UsageCardsSkeletonLoader } from '../components/SkeletonLoader';
const UsageCards = () => {
  const [loading, setLoading] = useState(false)
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState({})
  useEffect(() => {
    setLoading(true)
    try {
      fetch(BASE_URL + `get-usage-count?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
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
    <React.Fragment>
      {loading ?
        <UsageCardsSkeletonLoader />
        :
        <>
          <div className="col-xxl-4 col-xl-4 mb-3">
            <div className="card info-card users-card h-100 mb-0">
              <div className="card-body d-flex align-items-center">
                <div className="d-flex align-items-center">
                  <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                    <img src="/images/openmoji_hourglass-not-done.png" alt="icon" style={{ width: 75 }} />
                  </div>
                  <div className="ps-3">
                    <h5 className="mb-0"> <CountUp end={data?.total_time_spent || 0} duration={4} /> Min.</h5>
                    <span className="pt-1 fw-semibold">Total Time Spent by Users</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="col-xxl-4 col-xl-4 col-md-6 mb-3">
            <div className="card info-card chatbot-usage-card h-100 mb-0">
              <div className="card-body d-flex align-items-center">
                <div className="d-flex align-items-center">
                  <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                    <img src="/images/chatbotface.png" alt="icon" style={{ width: 75 }} />
                  </div>
                  <div className="ps-3">
                    <h5 className="mb-0"><CountUp end={data?.chatbot_usage_count || 0} duration={4} /></h5>
                    <span className="pt-1 fw-semibold">Chatbot Usage Count</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="col-xxl-4 col-xl-4 col-md-6 mb-3">
            <div className="card info-card resource-material-usage-card h-100 mb-0">
              <div className="card-body d-flex align-items-center">
                <div className="d-flex align-items-center">
                  <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                    <img src="/images/screening.png" alt="icon" style={{ width: 75 }} />
                  </div>
                  <div className="ps-3">
                    <h5 className="mb-0"><CountUp end={data?.screening_tool || 0} duration={4} /></h5>
                    <span className="pt-1 fw-semibold">Screening Tool Usage Count</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </>
      }
    </React.Fragment>
  );
}
export default UsageCards;