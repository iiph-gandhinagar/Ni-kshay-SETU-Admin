import {
  BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title,
  Tooltip
} from 'chart.js';
import domtoimage from "dom-to-image-more";
import React, { useContext, useEffect, useState } from 'react';
import { Bar } from "react-chartjs-2";
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import { LeaderboardLevelsCardLoader } from '../components/SkeletonLoader';
import Lottie from "lottie-react";
import noData from "../json/nodata.json"
import { saveAs } from 'file-saver';
const color = ["#16BFD6", "#F765A3", "#1DDD8D", "#A155B9", "#165BAA",]
const order = {
  National_Level: "National Level",
  State_Level: "State Level",
  District_Level: "District Level",
  Block_Level: "Block Level",
  "Health-facility_Level": "Health-facility Level"
}
const orderKey = Object.keys(order)
const levelOrder = ['Beginner', "Advanced Beginner", "Competent", "Proficient", "Expert"]
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);
function groupByKey(array, key) {
  return array
    .reduce((hash, obj) => {
      if (obj[key] === undefined) return hash;
      return Object.assign(hash, { [obj[key]]: (hash[obj[key]] || []).concat(obj) })
    }, {})
}
const options = {
  elements: {
    point: {
      radius: 30
    }
  },
  plugins: {
    legend: {
      labels: {
        font: {
          family: "'Nunito', sans-serif",
          size: 11,
          weight: 500
        },
        padding: 20,
        usePointStyle: true,
        color: "#000000",
      },
      position: "right",
    },
  },
  responsive: true,
  scales: {
    x: {
      ticks: {
        font: {
          family: "'Raleway', sans-serif",
          size: 15,
          weight: 600
        },
        color: "#000000de",
      },
      grid: {
        display: false,
      },
      stacked: true,
    },
    y: {
      display: false,
      grid: {
        display: false,
      },
      stacked: true,
    },
  },
};

const LeaderboardLevelsCard = ({ }) => {
  const [loader, setLoader] = useState(true)
  const [dateFilter, setdateFilter] = useState('overall')
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState({ labels: [], datasets: [] })
  useEffect(() => {
    try {
      setLoader(true)
      fetch(BASE_URL + `get-leaderboard-count?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
        .then((json) => {
          const dataByLevelID = groupByKey(json?.data?.map((e) => {
            return {
              ...e,
              ...{
                level: JSON.parse(e.level)?.en
              }
            }
          }), 'level');
          const dataBycadre_type = groupByKey(json?.data, 'cadre_type');
          var total = {}
          for (let index = 0; index < orderKey.length; index++) {
            const element = dataBycadre_type[orderKey[index]];
            if (element) {
              for (let index2 = 0; index2 < element?.length; index2++) {
                total = {
                  ...total,
                  ...{
                    [orderKey[index]]: (total[orderKey[index]] || 0) + (element[index2]?.count_data || 0)
                  }
                };
              }
            }
            else {
              total = {
                ...total,
                ...{
                  [orderKey[index]]: 0
                }
              };
            }
          }

          setData({
            labels: orderKey?.map((e) => order[e]),
            datasets: levelOrder?.map((lOrderData, lIndex) => {
              return {
                label: lOrderData,
                data: orderKey?.map((e, i) => {
                  const findIndex = dataByLevelID[lOrderData]?.findIndex((j) => j.cadre_type == e)
                  if (findIndex !== -1) {
                    return dataByLevelID[lOrderData]?.[findIndex]?.count_data * 100 / total[e] || 0
                  } else {
                    return 0
                  }

                }),
                backgroundColor: color[lIndex]
              }
            })
          })
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
    <div className="card">
      <div className="card-body">
        <div className="d-flex align-items-center justify-content-between mb-3 row p-0 m-0">
          <h5 className="section-title mb-0 col-12 col-sm-5 p-0 m-0">Leaderboard Current Levels</h5>
          <div className="d-flex align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
          <button onClick={() => {
              window.location = `export-leader-board?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-download"></i></button>
            <button onClick={() => {
              domtoimage.toBlob(document.getElementById('LeaderBoardChart')).then(function (blob) {
                saveAs(blob, 'LeaderboardLevels.png');
              }).catch((error) => {
                console.error("oops, something went wrong!", error);
              });
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-image"></i></button>
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
        {loader ? <LeaderboardLevelsCardLoader /> :
          data.datasets.length > 0 ? <Bar id='LeaderBoardChart' options={options} data={data} className="pt-1" style={{ maxHeight: 280 }} />
            :
            <Lottie style={{ height: 300 }} animationData={noData} loop={true} />
        }</div>
    </div>
  );
}
export default LeaderboardLevelsCard;