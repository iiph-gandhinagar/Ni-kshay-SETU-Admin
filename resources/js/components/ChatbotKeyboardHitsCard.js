import Lottie from "lottie-react";
import React, { useContext, useEffect, useState } from 'react';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import noData from "../json/nodata.json";
import { ChatbotCardLoader } from '../components/SkeletonLoader';

const ChatbotKeyboardHitsCard = ({ }) => {
  const [loader, setLoader] = useState(true)
  const [dateFilter, setdateFilter] = useState('overall')
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState([])
  useEffect(() => {
    try {
      setLoader(true)
      fetch(BASE_URL + `get-chatkeyword-hit-count?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
        .then((json) => {
          setData(json?.data)
          setLoader(false)
        }).catch((error) => {
          setLoader(false)
          console.log("response catch", error);
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }, [filters, dateFilter])
  return (
    <div className="card h-100 mb-0">
      <div className="card-body p-0">
        <div className="d-flex align-items-center justify-content-between p-3 row p-0 m-0">
          <a href='admin/chat-keyword-hits' className="col-12 col-sm-5 p-0 m-0">
            <h5 className="section-title mb-0 ">Chatbot Keyword Hits</h5>
          </a>
          <div className="d-flex align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
          <button onClick={() => {
              window.location = `export-chatbot-keyword?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-download"></i></button>
            <button
              onClick={() => setdateFilter("overall")}
              type="button"
              className={
                dateFilter === 'overall' ? "btn btn-primary btn-green me-3 py-1" :
                  "btn btn-outline-secondary btn-white me-3 py-1"}            >
              Overall
            </button>
            <button
              disabled={filters.date}
              onClick={() => setdateFilter("last 30 days")}
              type="button"
              className={
                dateFilter === "overall" ? "btn btn-outline-secondary btn-white py-1" : "btn btn-primary btn-green py-1"
              }            >
              Last 30 Days
            </button>
          </div>
        </div>
        <div className="d-flex align-items-center justify-content-between">
          <h6 className="card-tag px-2 text-end" style={{ width: 110, borderBottomRightRadius: 4, borderTopRightRadius: 4 }}>Keyword</h6>
          <h6 className="card-tag px-2 text-start" style={{ width: 110, borderBottomLeftRadius: 4, borderTopLeftRadius: 4 }}>Hits</h6>
        </div>
        {loader ?
          <div className="chatbot-list custom-scrollbar m-3">
            <ChatbotCardLoader id={'ChatbotKeyboardHitsCard'} />
          </div> :
          <div className="chatbot-list custom-scrollbar m-3">
            {data.length > 0 ?
              data?.map((item, i) => {
                var max = 1000
                var per = (item.count_data * 100) / max;
                return (
                  <div className="d-flex align-items-center justify-content-between chatbot-list-card me-xl-2 mr-0" key={i + " - ChatbotKeyboardHitsCard"}>
                    <h6 className="fs-16-Raleway mb-0 color-353535 col">{JSON.parse(item.title).en}</h6>
                    <span className="badge rounded-pill count-pill"
                      style={{ backgroundColor: per > 75 ? "#00AF31" : per > 35 ? "#FF9E69" : "#E77272" }}>
                      {item.count_data}
                    </span>
                  </div>

                )
              })
              : <Lottie style={{ height: 300 }} animationData={noData} loop={true} />
            }
          </div>}
      </div>
    </div>
  );
}
export default ChatbotKeyboardHitsCard;