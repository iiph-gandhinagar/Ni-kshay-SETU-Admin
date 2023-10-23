import {
  Chart as ChartJS, Filler, Legend, LineElement, PointElement, RadialLinearScale, Tooltip
} from 'chart.js';
import React, { useContext, useEffect, useState } from 'react';
import { Radar } from 'react-chartjs-2';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import domtoimage from "dom-to-image-more";
import { saveAs } from 'file-saver';
ChartJS.register(
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend
);

const options = {
  elements: {
    point: {
      radius: 3
    }
  },
  plugins: {
    legend: {
      display: false,
    },
  },
  scales: {
    r: {
       min: 0,
       max: 5,
       beginAtZero: true,
      grid: {
        color: "#D9D9D9"
      },
      angleLines: {
        color: "#DFDEDE"
      },
      ticks: {
        beginAtZero: true,
        max: 5,
        min: 0,
        color: "#808080",
        font: {
          family: "'Nunito', sans-serif",
          size: 11,
          weight: 500
        },
        stepSize: 1,
        callback: function (value, index, ticks) {
          return "0" + value + " " + "Star";
        },

      },
      pointLabels: {
        color: "#000000de",
        font: {
          family: "'Raleway', sans-serif",
          size: 15,
          weight: 600
        },
      }
    }
  },
};

const UserFeedbackCard = ({ }) => {
  const [dateFilter, setdateFilter] = useState('overall')
  const { filters } = useContext(AppStoreContext);
  const [data, setData] = useState({
    labels: ["User Interface", "Module Content", "Chatbot"],
    datasets: [],
  })
  useEffect(() => {
    try {
      setData({
        labels: ["User Interface", "Module Content", "Chatbot"],
        datasets: [],
      })
      fetch(BASE_URL + `get-user-feedback-details?type=${dateFilter}&state_id=${filters.state}&district_id=${filters.district}&block_id=${filters.block}&date=${filters.date}`).then((res) => res.json())
        .then((json) => {
          setData({
            labels: data.labels,
            datasets: [{
              data: data.labels?.map((e) => json?.data?.find((j) => JSON.parse(j.feedback_question)?.en === e)?.avg || 0),
              backgroundColor: "#ffede066",
              borderColor: "#FF6800",
              borderWidth: 1.5,
            }],
          })
        }).catch((error) => {
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
          <div className='col-12 col-sm-5 p-0 m-0'>
            <h5 className="section-title mb-0">User Feedback</h5>
            {filters?.state || filters?.district || filters?.block || filters?.date ? <span className="message-text" role="alert">
              * Filters can not be applied to this field
            </span> : null}
          </div>
          <div className='d-flex justify-content-end col-12 col-sm-7 p-0 m-0 mt-1 mt-sm-0'>
            <button onClick={() => {
              domtoimage.toBlob(document.getElementById('download_User_Feedback_Chart')).then(function (blob) {
                saveAs(blob, 'User Feedback.png');
              }).catch((error) => {
                console.error("oops, something went wrong!", error);
              });
            }} className="btn btn-primary btn-green me-sm-3 me-1 py-1"><i className="fa fa-image"></i></button>
            <button
              type="button"
              className="btn btn-primary btn-green me-3 py-1">
              Overall
            </button>
          </div>
        </div>
        <Radar id='download_User_Feedback_Chart' options={options} data={data} style={{ maxHeight: 300 }} />
      </div>
    </div>
  );
}
export default UserFeedbackCard;