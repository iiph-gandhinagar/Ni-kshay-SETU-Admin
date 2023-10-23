// let sso_domain = "https://ssobeta.nikshay.in"
// let return_url = "https://beta-dashboards.nikshay.in/index/"
let url = g1.url.parse(location.href)
function checkSignature() {
    $.ajax({
        type: "get",
        url: "/index/ssotokenverify",
        success: function(resp) {
            let res = JSON.parse(resp)
            if (res.auth) {
                if (res.pass_res) {
                    window.location = sso_domain + "/v1/sso/resetPassword?returnUrl=" + return_url
                }
            } else {
                window.location = sso_domain + "/v1/sso/login?returnUrl=" + return_url
            }
        },
        error: function() {}
    });
}
function ajaxchain_fetch(url_) {
    // checkSignature()
    return {
        ajaxchain_instance: $.ajaxchain({
            data: {
                _offset: 0
            },
            chain: $.ajaxchain.list(url_),
            limit: url_.length + 1
        })
    }
}
function sort_table(table_id, column_position, column_type, dir) {
    dir = dir || 'asc'
    let table, rows, switching, i, x, y, shouldSwitch, switchcount = 0;
    table = document.getElementById(table_id);
    switching = true;
    while (switching) {
        switching = false;
        rows = $(table).children('tbody').children('tr');
        for (i = 0; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[column_position];
            y = rows[i + 1].getElementsByTagName("TD")[column_position];
            if (dir == "asc") {
                if (column_type != "text") {
                    if (Number(x.getAttribute("data-value")) > Number(y.getAttribute("data-value"))) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (x.getAttribute("data-value").toLowerCase() > y.getAttribute("data-value").toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            } else if (dir == "desc") {
                if (column_type != "text") {
                    if (Number(x.getAttribute("data-value")) < Number(y.getAttribute("data-value"))) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (x.getAttribute("data-value").toLowerCase() < y.getAttribute("data-value").toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
    for (i = 0; i < rows.length; i++) {
        x = rows[i].getElementsByTagName("TD")[0]
        $(x).find('.sr_no').text(i + 1)
    }
}
function loader_show() {
    $('.background').show()
    $('.loader').show()
}
function loader_hide() {
    $('.background').hide()
    $('.loader').hide()
}
function find_prev_quarter(current_quarter, crnt_year) {
    let quarter_seq = ["Q1", "Q2", "Q3", "Q4"]
    let current_quarter_index = quarter_seq.findIndex(function(d) {
        return d == current_quarter;
    })
    if (current_quarter_index == 0) {
        return {
            quarter: quarter_seq[3],
            year: crnt_year - 1
        }
    } else {
        return {
            quarter: quarter_seq[current_quarter_index - 1],
            year: crnt_year
        }
    }
}
function load_calendar(holder, quarter, year) {
    url = g1.url.parse(location.href)
    let active_tab = url.searchKey['active_cal'] || 'quarter'
    quarter = url.searchKey.quarter || quarter
    year = url.searchKey.year || year
    let qa_date = []
      , yr_date = []
    for (let i = 8; i >= 0; i--) {
        if (i < 3)
            qa_date.push(moment().subtract(i, 'year').format('YYYY'))
        yr_date.push(moment().subtract(i, 'year').format('YYYY'))
    }
    let label = ''
    if (active_tab == 'quarter') {
        label = quarter + '-' + year
    } else {
        label = year
    }
    moment().subtract(2, 'year').format('YYYY')
    $('.top_cal_tab').on('template', function() {
        if (active_tab == 'quarter') {
            $("#top_cal .quarter").removeClass("highlighted")
            $("#top_cal .quarter span[value=" + year + "][data-attr=" + quarter + "]").closest('.quarter').addClass('highlighted')
        } else {
            $("#top_cal .year").removeClass("highlighted")
            $("#top_cal .year[data-attr=" + year + "]").closest('.year').addClass('highlighted')
        }
    }).template({
        cal_type: ['Quarter', 'Year'],
        qr_year: qa_date,
        d_quarter: ['Q1', 'Q2', 'Q3', 'Q4'],
        d_year: yr_date,
        place_holder: holder,
        label: label,
        active_tab: url.searchKey['active_cal'] || 'quarter',
        type: url.file,
        district_view: url.searchKey.district_view
    })
}
$(document).on('click', '.fa-chevron-left', function() {
    let year = parseInt($('.year').attr('data-attr'))
    $('.year').text((year - 1) + ' - ' + (year))
    $('.year').attr('data-attr', (year - 1))
    $('.month').each(function() {
        $(this).attr('data-year', parseInt($(this).attr('data-year')) - 1)
    })
}).on('click', '.fa-chevron-right', function() {
    let year = parseInt($('.year').attr('data-attr')) + 1
    $('.year').text((year) + ' - ' + (year + 1))
    $('.year').attr('data-attr', year)
    $('.month').each(function() {
        $(this).attr('data-year', parseInt($(this).attr('data-year')) + 1)
    })
}).on('click', '.quarter, .year', function() {
    $('.quarter, .year').removeClass('highlighted')
    $(this).addClass('highlighted')
}).on('click', '.apply', function() {
    let cal_holder = $(this).attr('id').includes('side_cal') ? 'side_cal' : 'top_cal'
    let active_cal = $("#" + cal_holder + ' .cal_active.active').attr('id')
    let date_update = {}
    let active_qa = ''
      , active_yr = ''
    if (active_cal.includes('quarter')) {
        active_qa = $("#" + cal_holder + ' .quarter.highlighted span').attr('data-attr')
        active_yr = $("#" + cal_holder + ' .quarter.highlighted span').attr('value')
        date_update['quarter'] = active_qa
        date_update['year'] = active_yr
        date_update['active_cal'] = 'quarter'
        url = g1.url.parse(location.href).update(date_update)
        history.pushState({}, '', url.toString());
        $('.date-label-' + cal_holder).text(active_qa + ' - ' + active_yr)
        $('#' + cal_holder).removeClass('show')
        if (url.file == 'profile') {
            get_accordion_data()
        } else {
            redraw()
        }
    } else {
        active_yr = $("#" + cal_holder + ' .year.highlighted').attr('data-attr')
        date_update['quarter'] = ''
        date_update['year'] = active_yr
        date_update['active_cal'] = 'year'
        url = g1.url.parse(location.href).update(date_update)
        history.pushState({}, '', url.toString());
        $('.date-label-' + cal_holder).text(active_yr)
        $('#' + cal_holder).removeClass('show')
        if (url.file == 'profile') {
            get_accordion_data()
        } else {
            redraw()
        }
    }
})
window.addEventListener('mouseup', function(e) {
    let container = $("#top_cal");
    if ((!container.is(e.target) && ((container.has(e.target).length === 0)))) {
        container.removeClass('show')
    }
});
function show_checkbox(id, option_name) {
    if (option_name === 'true')
        $("#" + id).prop('checked', true)
    else
        $("#" + id).prop('checked', false)
}
function round_val(value) {
    return Math.round(value)
}
function getMinValue(data, key) {
    return data.reduce(function(min, p) {
        return p[key] < min ? p[key] : min;
    }, data[0][key]);
}
function getMaxValue(data, key) {
    return data.reduce(function(max, p) {
        return p[key] > max ? p[key] : max;
    }, data[0][key]);
}
