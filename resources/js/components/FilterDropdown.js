import queryString from 'query-string';
import React, { useContext, useEffect, useState } from 'react';
import { AppStoreContext, BASE_URL } from '../components/DashboardApp';
import DatePicker from 'react-datepicker';
import "react-datepicker/dist/react-datepicker.css";

import moment from "moment"
const FilterDropdown = ({ }) => {
  const [dateRange, setDateRange] = useState([null, null]);
  const [startDate, endDate] = dateRange;
  const { filters, setFilterConfig } = useContext(AppStoreContext);
  const [stateData, setStateData] = useState([])
  const [districtData, setDistrictData] = useState([])
  const [blockData, setBlockData] = useState([])
  const [stateselect, setSelectedState] = useState(0)
  const [districtselect, setSelectedDistrict] = useState(0)
  const [blockselect, setSelectedBlock] = useState(0)
  const queryObj = queryString?.parse(window.location.search)
  const getAllState = () => {
    try {
      fetch(BASE_URL + 'get-role-wise-state').then((res) => res.json())
        .then((json) => {
          setStateData(json?.state)
        }).catch((error) => {
          console.log("response catch", error);
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }
  const getDistrictByID = (id) => {
    try {
      fetch(BASE_URL + 'api/get-district-by-state/' + id).then((res) => res.json())
        .then((json) => {
          setDistrictData(json?.data)
        }).catch((error) => {
          console.log("response catch", error);
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }
  const getBlockByID = (id) => {
    try {
      fetch(BASE_URL + 'api/get-block-by-district/' + id).then((res) => res.json())
        .then((json) => {
          setBlockData(json?.data)
        }).catch((error) => {
          console.log("response catch", error);
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }
  useEffect(() => {
    setSelectedState(parseInt(queryObj?.stateId || 0))
    setSelectedDistrict(parseInt(queryObj?.ditrictId || 0))
    setSelectedBlock(parseInt(queryObj?.blockId || 0))
    if (parseInt(queryObj?.stateId)) {
      getDistrictByID(parseInt(queryObj?.stateId))
    }
    if (parseInt(queryObj?.ditrictId)) {
      getBlockByID(parseInt(queryObj?.ditrictId))
    }
    if (queryObj?.date) {
      const splitdate = queryObj?.date?.split(" ");
      setDateRange([new Date(splitdate[0]), new Date(splitdate[2])])
    }
    getAllState()
  }, [])
  return (
    <div className="col col-md-3">
      <div className="dropdown ms-md-3 mt-3 mt-md-0">
        <div
          className={`top-cards px-3 py-2 mb-0 pointer ${(filters?.state || filters?.district || filters?.block || filters?.date) ? "bg-0D9488" : ""}`}
          data-bs-toggle="dropdown"
          aria-expanded="false"
          data-bs-auto-close="outside"
        >
          <div className="d-flex align-items-center justify-content-between">
            <h5 className={`font-family-Nunito mb-0 ${(filters?.state || filters?.district || filters?.block || filters?.date) ? "text-white lh-34" : "color-22577E lh-34"}`}>Filters</h5>
            <i className="bi bi-chevron-down me-1" style={{
              color: filters?.state || filters?.district || filters?.block || filters?.date ? "white" : "black"
            }}></i>
          </div>
        </div>
        <div className="dropdown-menu p-3 filters-dropdown">
          <h5 className="color-22577E mb-2 font-family-Nunito">Filters</h5>
          <div className="mb-2 pt-1">
            <label htmlFor="State" className="form-label">State</label>
            <select
              value={stateselect}
              onChange={(e) => {
                setSelectedState(e.target.value)
                setSelectedDistrict(0)
                setSelectedBlock(0)
                getDistrictByID(e.target.value)
                setDistrictData([])
                setBlockData([])
              }}
              className="form-select" id="State" aria-label="Default select example">
              <option value={0}>Select State</option>
              {stateData?.map((state) => {
                return (
                  <option

                    key={state?.title} value={state?.id}>{state?.title}</option>
                )
              })}
            </select>
          </div>
          <div className="mb-2 pt-1">
            <label htmlFor="District" className="form-label">District</label>
            <select
              value={districtselect}
              onChange={(e) => {
                setSelectedDistrict(e.target.value)
                setSelectedBlock(0)
                getBlockByID(e.target.value)
                setBlockData([])
              }}
              className="form-select" id="District" aria-label="Default select example">
              <option value={0}>Select District</option>
              {districtData?.map((dis) => {
                return (
                  <option

                    key={dis?.title} value={dis?.id}>{dis?.title}</option>
                )
              })}
            </select>
          </div>
          <div className="mb-2 pt-1">
            <label htmlFor="Block" className="form-label">Block</label>
            <select
              value={blockselect}
              onChange={(e) => {
                setSelectedBlock(e.target.value)
              }}
              className="form-select" id="Block" aria-label="Default select example">
              <option value={0}>Select TU</option>
              {blockData?.map((block) => {
                return (
                  <option

                    key={block?.title} value={block?.id}>{block?.title}</option>
                )
              })}
            </select>
          </div>
          <div className="mb-2 pt-1">
            <label htmlFor="Date" className="form-label">Date</label>
            <div>
              <DatePicker
                className={'form-select'}
                selectsRange={true}
                startDate={startDate}
                endDate={endDate}
                onChange={(update) => {
                  setDateRange(update);
                }}
                isClearable={true}
                maxDate={new Date()}
              />
            </div>
          </div>
          <div className="d-flex justify-content-between align-items-center pt-1">
            <button type="button"
              onClick={() => {
                window.location.replace(`/`)
              }}
              className="btn btn-outline-primary btn-out-apply">Remove</button>
            <button
              onClick={() => {
                window.location.replace(`/?stateId=${stateselect}&ditrictId=${districtselect}&blockId=${blockselect}&date=${startDate && endDate ? moment(startDate).format("YYYY-MM-DD") + "  " + moment(endDate).format("YYYY-MM-DD") : ''}`)
              }}
              className="btn btn-primary btn-apply">Apply</button>
          </div>
        </div>
      </div>
    </div>
  );
}
export default FilterDropdown;