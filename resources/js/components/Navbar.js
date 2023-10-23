import FilterDropdown from './FilterDropdown';
import Profiles from './Profiles';
import React, { useState } from 'react';

function Navbar() {
    const [fix, setFix] = useState(false);
    function setFixed() {
        if (window.scrollY >= 150) {
            setFix(true)
        }
        else {
            setFix(false)
        }
    }
    window.addEventListener("scroll", setFixed)
    return (
        <div className='container'>
            <div className={fix ? 'is-sticky  ' : 'is-stickys  '}>
                <div className="d-flex align-items-center justify-content-between mt-1">
                    <div className="d-flex align-items-center">
                        <img src="/images/tb_logo.png" alt="logo" style={{ width: 45 }} className="me-1" />
                        <h5 className="logo-text ps-2 fw-bold mb-0" >NI-KSHAY SETU ADMIN DASHBOARD</h5>
                    </div>
                    <Profiles />
                </div>
            </div>
            <div className="d-block d-md-flex align-items-center justify-content-end my-3">
                <div className="col col-sm-auto col-xl-3">
                    <div className="top-cards px-3 py-2 mb-0 management-top-card mt-3 mt-md-0">
                        <h5 className="font-family-Nunito mb-0 lh-34">
                            <i className="bi bi-chevron-left me-1"></i>
                            <a href="/admin/home" className="color-22577E ps-2" >Management</a>
                        </h5>
                    </div>
                </div>
                <div className="col col-sm-auto col-xl-3">
                    <div className="top-cards px-3 py-2 ga-top-card mb-0 management-top-card d-flex align-items-center ms-md-3 mt-3 mt-md-0">
                        <h5 className="font-family-Nunito mb-0 lh-34">
                            <a target="_blank" className="color-22577E" href='https://analytics.google.com/analytics/web/?authuser=2#/p349237802/reports/intelligenthome'> <i className="bi bi-grid-1x2-fill me-2"></i>Google Analytics</a>
                        </h5>
                    </div>
                </div>
                <FilterDropdown />
            </div>
        </div>
    )
}

export default Navbar;
