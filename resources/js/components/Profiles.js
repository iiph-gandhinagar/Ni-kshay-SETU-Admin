import React, { useState, useRef, useEffect } from 'react';
import { BASE_URL } from '../components/DashboardApp';

function UserProfileDropdown() {
  const [data, setData] = useState([]);
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef(null);
  const [loader, setLoader] = useState(true)

  const toggleMenu = () => {
    setIsOpen(!isOpen);
  };

  const handleClickOutside = (event) => {
    if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
      setIsOpen(false);
    }
  };

  useEffect(() => {
    document.addEventListener('click', handleClickOutside, true);
    return () => {
      document.removeEventListener('click', handleClickOutside, true);
    };
  }, []);

  useEffect(() => {
    setLoader(true)
    try {
      fetch(BASE_URL + `get-user-name`).then((res) => res.json())
        .then((json) => {
          console.log(json);
          setData(json);
          console.log(data);
          setLoader(false)
        }).catch((error) => {
          console.log("response catch", error);
          setLoader(false)
        })

    } catch (error) {
      console.log("response catch", error);
    }
  }, [])
console.log("response data", data);
  return (
    <div style={{display:'flex', alignItems:'center'}} className="position-relative float-right">
    <div style={{marginRight:'5px'}}>{data.user_name}</div>
    <div ref={dropdownRef}>
      <button
        className="btn btn-secondary rounded-circle "
        type="button"
        style={{
          width: 40,
          height: 39,
          paddingRight: 7,
          paddingLeft: 7,
          display: "inline-block",
          marginRight: 20,
          marginTop: 0,
          transition: "none",
          letterSpacing: 0,
          lineHeight: 35,
          textAlign: "center",
          border: "none",
          background: "#bdbdbd"

        }}
        onClick={toggleMenu}
      >
      
        <img
          className='rounded-circle '
          src="/images/user.jpg"
          alt="logos"
          style={{
            display: 'block',
            maxWidth: '100%',
            maxHeight: '100%'

          }}
        />

      </button>
      <div
        className={`dropdown-menu${isOpen ? ' show' : ''}`}
        style={{ position: 'absolute', right: 0 }}
      >
        <a className="dropdown-item" href="https://api.nikshay-setu.in/admin/profile">
          Profile
        </a>
        <a className="dropdown-item" href="https://api.nikshay-setu.in/admin/logout">
          Logout
        </a>
      </div>
    </div>
    </div>
  );
}

export default UserProfileDropdown;
