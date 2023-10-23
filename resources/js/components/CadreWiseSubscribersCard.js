import React, { useContext, useEffect, useRef, useState } from 'react';
import Chart from 'react-apexcharts';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import { CadreWiseSubscribersCardSkeletonLoader } from '../components/SkeletonLoader';
import domtoimage from "dom-to-image-more";
import Lottie from "lottie-react";
import noData from "../json/nodata.json"
import { saveAs } from 'file-saver';
const CadreWiseSubscribersCard = ({ }) => {
  const ref = useRef(null)
  const [loader, setLoader] = useState(true)
  const [total, setTotal] = useState(0)
  const [dateFilter, setdateFilter] = useState('overall')
  const [options, setOptions] = useState({
    chart: {
      height: 250,
      type: 'radialBar',
    },
    plotOptions: {
      radialBar: {
        offsetY: 0,
        startAngle: 0,
        endAngle: 270,
        hollow: {
          margin: 5,
          size: '8%',
          background: 'transparent',
          image: undefined,
        },
        dataLabels: {
          name: {
            show: false,
          },
          value: {
            show: false,
          }
        },
        track: {
          show: true,
          startAngle: undefined,
          endAngle: undefined,
          background: "#DFDFDF",
          strokeWidth: '100%',
          opacity: 1,
          margin: 10,
          dropShadow: {
            enabled: false,
            top: 0,
            left: 0,
            blur: 3,
            opacity: 0.5
          }
        },
      },
    },
    stroke: {
      lineCap: 'round'
    },
    colors: ["#03DAC5", "#B27CFF", "#AEE537", "#FFD776", "#84A6FF"],

    legend: {
      show: true,
      floating: true,
      fontSize: '12px',
      fontFamily: "'Nunito', sans-serif",
      position: 'left',
      offsetX: 0,
      offsetY: 15,
      labels: {
        colors: "#22577E"
        // useSeriesColors: true,
      },
      markers: {
        width: 0,
        height: 0,
      },
      formatter: function (seriesName, opts) {
        return seriesName
      },
      itemMargin: {
        vertical: 3
      }
    },
    responsive: [{
      breakpoint: 768,
      options: {
        legend: {
           fontSize: '8px',
          offsetX: 0,
          offsetY: 0,
          floating: false,
          position: "bottom",
          markers: {
            show: true,
            width: 9,
            height: 9,
          },
        }
      }
    },
    ],
    labels: [],
    tooltip: {
      enabled: true,
    }
  });
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState([])
  useEffect(() => {
    setLoader(true)
    setTotal(0)
    try {
      fetch(BASE_URL + `get-cadre-wise-subscriber-count?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
        .then((json) => {
          var total = 0
          for (let index = 0; index < json?.data.length; index++) {
            const element = json?.data[index];
            total = total + element.TotalCadreCount
          }
          setTotal(total)
          setOptions(old => {
            return {
              ...old,
              ...{
                labels: json?.data?.map((e) => e.CadreName),
                plotOptions: {
                  ...old.plotOptions,
                  radialBar: {
                    ...old.plotOptions.radialBar,
                    hollow: {
                      margin: 5,
                      size: json?.data?.length > 3 ? '8%' : json?.data?.length < 3 ? "60%" : "20%",
                      background: 'transparent',
                      image: undefined,
                    }
                  }
                }
              }
            }
          })
          setData(json?.data)
          setTimeout(() => {
            setLoader(false)
          }, 500);
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
      <div className="card-body">
        <div className="d-flex align-items-center justify-content-between mb-3 row p-0 m-0">
          <h5 className="section-title mb-0 col-12 col-sm-5 p-0 m-0">Cadre Wise Subscribers</h5>
          <div className="d-sm-flex d-block align-items-center justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0">
            <button onClick={() => {
              domtoimage.toBlob(document.getElementById('CadreWiseSubscribersChart')).then(function (blob) {
                saveAs(blob, 'CadreWiseSubscribers.png');
              }).catch((error) => {
                console.error("oops, something went wrong!", error);
              });
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-image"></i></button>
            <button onClick={() => {
              window.location = `export-cadre-wise-subscribers?state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-download"></i></button>
            <button
              onClick={() => setdateFilter("overall")}
              type="button"
              className={
                dateFilter === 'overall' ? "btn btn-primary btn-green me-sm-3 me-1 py-1" :
                  "btn btn-outline-secondary btn-white me-sm-3 me-1 py-1"}>
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
        {loader ?
          <div className="d-flex align-items-center justify-content-center mb-3">
            <CadreWiseSubscribersCardSkeletonLoader />
          </div> :
          <div id="CadreWiseSubscribersChart">
            {data.length > 0 ? <Chart
              ref={ref}
              options={options}
              series={data?.map((e) => (e.TotalCadreCount * 100 / total).toFixed(2))}
              type="radialBar"
              height={350} />
              : <Lottie style={{ height: 300 }} animationData={noData} loop={true} />}
          </div>
        }

      </div>
    </div>
  );
}
export default CadreWiseSubscribersCard;