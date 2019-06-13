import request from '@/utils/request';

export function getReports(user_id) {
    return request({
        url: Routing.generate('get_reports_by_user',{user_id: user_id}),
        method: 'get'
    });
}

export function getReport(report_id) {
    return request({
        url: Routing.generate('get_report',{report_id: report_id}),
        method: 'get'
    })
}

export function getReportBreakdown(report_id) {
    return request({
        url: Routing.generate('get_report_breakdown',{report_id: report_id}),
        method: 'get'
    })
}