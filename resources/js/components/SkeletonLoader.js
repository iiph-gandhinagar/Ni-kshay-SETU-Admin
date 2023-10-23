import React from 'react';
import Skeleton from 'react-loading-skeleton';

export const ActivitiesCardsSkeletonLoader = () => {
  return (
    <>
      <div className="col-xxl-4 col-xl-4 col-md-6">
        <div className="card bg-FFFBFB info-card activity-card mb-0">
          <div className="card-body p-0">
            <div className="d-flex align-items-center py-3 info-area">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <Skeleton width={50} height={50} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton />
                <Skeleton />
              </div>
            </div>
            <div className="area-chart px-3">
              <div className="area-chart-container">
                <Skeleton height={100} />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="col-xxl-4 col-xl-4 col-md-6">
        <div className="card bg-FFFBFB info-card activity-card mb-0">
          <div className="card-body p-0">
            <div className="d-flex align-items-center py-3 info-area">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <Skeleton width={50} height={50} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton />
                <Skeleton />
              </div>
            </div>
            <div className="area-chart px-3">
              <div className="area-chart-container">
                <Skeleton height={100} />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="col-xxl-4 col-xl-4">
        <div className="card bg-FFFBFB info-card activity-card mb-0">
          <div className="card-body p-0">
            <div className="d-flex align-items-center py-3 info-area">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <Skeleton width={50} height={50} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton />
                <Skeleton />
              </div>
            </div>
            <div className="area-chart px-3">
              <div className="area-chart-container">
                <Skeleton height={100} />
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
export const ModuleUsageCardSkeletonLoader = () => {
  return (
    <Skeleton width={"100%"} height={300} />
  );
}
export const CadreWiseSubscribersCardSkeletonLoader = () => {
  return (
    <Skeleton borderRadius={300} width={300} height={300} />
  );
}
export const UsageCardsSkeletonLoader = () => {
  return (
    <>
      <div className="col-xxl-4 col-xl-4 mb-3">
        <div className="card info-card users-card h-100 mb-0">
          <div className="card-body d-flex align-items-center">
            <div className="d-flex align-items-center w-100">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                <Skeleton width={75} height={75} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton height={30} />
                <Skeleton className="mt-2" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="col-xxl-4 col-xl-4 mb-3">
        <div className="card info-card users-card h-100 mb-0">
          <div className="card-body d-flex align-items-center">
            <div className="d-flex align-items-center w-100">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                <Skeleton width={75} height={75} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton height={30} />
                <Skeleton className="mt-2" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="col-xxl-4 col-xl-4 mb-3">
        <div className="card info-card users-card h-100 mb-0">
          <div className="card-body d-flex align-items-center">
            <div className="d-flex align-items-center w-100">
              <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-1">
                <Skeleton width={75} height={75} />
              </div>
              <div className="ps-3 w-100">
                <Skeleton height={30} />
                <Skeleton className="mt-2" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
export const StateWiseListCardSkeletonLoader = () => {
  return (
    <div className="state-wise-list-card p-1 mb-1">
      <div className="row">
        <div className="col-4">
          <Skeleton className="w-100" />
          <div className="text-center">
            <Skeleton width={100} />
            <Skeleton width={40} />
          </div>
        </div>
        <div className="col-4">
          <div className="d-flex flex-column justify-content-end h-100 align-items-center">
            <div className="text-center">
              <Skeleton width={100} />
            </div>
            <Skeleton width={40} />
          </div>
        </div>
        <div className="col-4">
          <div className="d-flex justify-content-center align-items-center h-100">
            <div className="circular-progress-bar">
              <Skeleton circle width={52} height={52} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export const LeaderboardLevelsCardLoader = () => {
  return (
    <Skeleton height={300} />
  );
}
export const ChatbotCardLoader = () => {
  return (
    [1, 2, 3, 4, 5, 6, 7, 8, 0]?.map((e, i) => {
      return (
        <div className="d-flex align-items-center justify-content-between chatbot-list-card me-xl-2 mr-0" key={e + "- ChatbotCardLoader -" + i} >
          <h6 className="fs-12-Raleway mb-0 color-353535 col"> <Skeleton /></h6>
          <span className="badge rounded-pill count-pill">
            <Skeleton />
          </span>
        </div>)
    })

  );
}