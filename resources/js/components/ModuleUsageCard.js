import React, { useContext, useEffect, useState } from 'react';
import { Chart } from "react-google-charts";
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import { ModuleUsageCardSkeletonLoader } from '../components/SkeletonLoader';
import domtoimage from "dom-to-image-more";
import noData from "../json/nodata.json"
import Lottie from "lottie-react";
import { saveAs } from 'file-saver';
const ModuleUsageCard = ({ }) => {
  const [loader, setLoader] = useState(true)
  const [dateFilter, setdateFilter] = useState('overall')
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState([])
  useEffect(() => {
    setLoader(true)
    try {
      fetch(BASE_URL + `get-module-wise-subscriber-count?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
        .then((json) => {
          setData([...[["Cader", " total Cadre Count"]], ...json?.data?.map((e) => [e.action, e.TotalCount])])
          setLoader(false)
        }).catch((error) => {
          console.log("response catch", error);
          setLoader(false)
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }, [filters, dateFilter])
  return (
    <div className="card h-100 mb-0">
      <div className="card-body">
        <div className="d-flex align-items-center justify-content-between mb-3 row p-0 m-0">
          <h5 className="section-title mb-0 col-12 col-sm-5 p-0 m-0">Module Usage</h5>
          <div className="d-sm-flex d-block align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
            <button onClick={() => {
              domtoimage.toBlob(document.getElementById('ModuleUsageChart')).then(function (blob) {
                saveAs(blob, 'ModuleUsage.png');
              }).catch((error) => {
                console.error("oops, something went wrong!", error);
              });
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-image"></i></button>

            <button onClick={() => {
              window.location = `export-module-usage?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-download"></i></button>
            <button
              onClick={() => setdateFilter("overall")}
              type="button"
              className={
                dateFilter === 'overall' ? "btn btn-primary btn-green me-sm-3 me-1 py-1" :
                  "btn btn-outline-secondary btn-white me-sm-3 me-1 py-1"}            >
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
        {loader ? <ModuleUsageCardSkeletonLoader /> :
          data.length > 1 ? <div id="ModuleUsageChart" style={{ height: 330 }} className="pt-3">
            <Chart
              chartType="PieChart"
              data={data}
              options={{
                colors: ["#81FFA4", "#E3B5FF", "#DF7070", "#7A97FF", "#FDFFAC", "#FFB0E0", "#A074FD", "#90BE6D", "#88F8FF", "#F9C74F"],
                is3D: true,
                legend: {
                  position: 'left',
                  alignment:'center'
                },
                chartArea: {
                  height: 300,
                  width: "100%"
                },
                height: 300,
              }}
              width={"100%"}
              height={"100%"}
            />
          </div>
            : <Lottie style={{ height: 300 }} animationData={noData} loop={true} />
        }
      </div>
    </div>
  );
}
export default ModuleUsageCard;