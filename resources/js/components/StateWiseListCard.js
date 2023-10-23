import React from 'react';
import {
  CircularProgressbar
} from "react-circular-progressbar";
const StateWiseListCard = ({ title, SubscribersTotal = 0, SubscribersToday = 0, percentage = 66 }) => {
  return (
    <div className="state-wise-list-card p-1 mb-1">
      <div className="row">
        <div className="col-4">
          <h5 className="fs-16-Nunito mb-1 color-22577E">{title}</h5>
          <div className="text-center">
            <h5 className="title mb-1">Subscribers Today</h5>
            <h5 className="fs-16-Nunito mb-0 color-5584AC">{SubscribersToday}</h5>
          </div>
        </div>
        <div className="col-4">
          <div className="d-flex flex-column justify-content-end h-100 align-items-center">
            <h5 className="title mb-1">Subscribers Total</h5>
            <h5 className="fs-16-Nunito mb-0 color-5584AC">{SubscribersTotal}</h5>
          </div>
        </div>
        <div className="col-4">
          <div className="d-flex justify-content-center align-items-center h-100">
            <div className="circular-progress-bar">
              <CircularProgressbar value={percentage} text={`${percentage}%`} styles={{ path: { stroke: "#FF8310", }, trail: { stroke: "#FEDCC9", }, text: { fontFamily: "'Nunito', sans-serif", fontWeight: 500, fill: "#5584AC" } }} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
export default StateWiseListCard;