import { useEffect, useState } from 'react';
import { dashboardService, leadService } from '../services/leadService';
import {
    BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, Legend,
} from 'recharts';
import * as XLSX from 'xlsx';
import toast from 'react-hot-toast';
import styles from './Reports.module.css';

const COLORS = ['#3b82f6', '#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#0d9488', '#f97316'];

const Reports = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [exporting, setExporting] = useState(false);

    useEffect(() => {
        dashboardService.get()
            .then(r => setData(r.data))
            .catch(() => { })
            .finally(() => setLoading(false));
    }, []);

    const handleExport = async () => {
        setExporting(true);
        try {
            const { data: leads } = await leadService.exportAll();
            const rows = leads.map(lead => ({
                'Student Name': lead.studentName,
                'Father Name': lead.fatherName || '',
                'Mobile': lead.mobile,
                'Email': lead.email || '',
                'Gender': lead.gender || '',
                'Category': lead.category || '',
                'City': lead.city || '',
                'State': lead.state || '',
                'Interested Course': lead.interestedCourse || '',
                'Specialization': lead.specialization || '',
                'Mode': lead.mode || '',
                'Lead Source': lead.leadSource || '',
                'Status': lead.status,
                'Assigned To': lead.assignedTo?.name || '',
                '10th Marks': lead.tenthMarks || '',
                '12th Marks': lead.twelfthMarks || '',
                'Stream': lead.stream || '',
                'School/College': lead.schoolCollegeName || '',
                'Follow-up Date': lead.followUpDate ? new Date(lead.followUpDate).toLocaleDateString('en-IN') : '',
                'Created Date': new Date(lead.createdAt).toLocaleDateString('en-IN'),
            }));
            const ws = XLSX.utils.json_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'All Leads');
            XLSX.writeFile(wb, `complete_leads_report_${new Date().toISOString().split('T')[0]}.xlsx`);
            toast.success('Report exported successfully!');
        } catch {
            toast.error('Export failed');
        } finally {
            setExporting(false);
        }
    };

    if (loading) return <div className="page-loader"><div className="spinner" /><span>Loading reports...</span></div>;

    const s = data?.stats || {};
    const statusData = (data?.statusBreakdown || []).map(i => ({ name: i._id, value: i.count }));
    const sourceData = (data?.sourceBreakdown || []).map(i => ({ name: i._id, count: i.count }));
    const courseData = (data?.courseBreakdown || []).map(i => ({ name: i._id, count: i.count }));

    const conversionRate = s.totalLeads > 0 ? ((s.admissionDone / s.totalLeads) * 100).toFixed(1) : 0;
    const interestedRate = s.totalLeads > 0 ? ((s.interested / s.totalLeads) * 100).toFixed(1) : 0;

    return (
        <div className={styles.page}>
            <div className="section-header">
                <div>
                    <h2 className="section-title">Reports & Analytics</h2>
                    <p className="section-subtitle">Comprehensive insights across all leads</p>
                </div>
                <button className="btn btn-success" onClick={handleExport} disabled={exporting}>
                    {exporting ? <><div className="spinner" />Exporting...</> : '📥 Export Full Report'}
                </button>
            </div>

            {/* KPI Cards */}
            <div className={styles.kpiGrid}>
                {[
                    { label: 'Total Leads', value: s.totalLeads || 0, icon: '🎯', color: '#3b82f6' },
                    { label: 'Conversion Rate', value: `${conversionRate}%`, icon: '📈', color: '#7c3aed' },
                    { label: 'Interest Rate', value: `${interestedRate}%`, icon: '✨', color: '#10b981' },
                    { label: 'Admissions', value: s.admissionDone || 0, icon: '🎓', color: '#f59e0b' },
                    { label: 'Not Interested', value: s.notInterested || 0, icon: '❌', color: '#ef4444' },
                    { label: 'Pending Calls', value: s.pendingFollowUps || 0, icon: '📞', color: '#0d9488' },
                ].map(k => (
                    <div key={k.label} className={styles.kpiCard} style={{ '--c': k.color }}>
                        <span className={styles.kpiIcon}>{k.icon}</span>
                        <p className={styles.kpiVal}>{k.value}</p>
                        <p className={styles.kpiLabel}>{k.label}</p>
                    </div>
                ))}
            </div>

            {/* Charts Row 1 */}
            <div className={styles.row}>
                <div className={`card ${styles.chart}`}>
                    <h3 className={styles.chartTitle}>Status Distribution</h3>
                    <ResponsiveContainer width="100%" height={280}>
                        <PieChart>
                            <Pie data={statusData} cx="50%" cy="50%" outerRadius={100} dataKey="value" label={({ name, percent }) => `${name} (${(percent * 100).toFixed(0)}%)`} labelLine={false}>
                                {statusData.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
                            </Pie>
                            <Tooltip contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8 }} />
                            <Legend />
                        </PieChart>
                    </ResponsiveContainer>
                </div>

                <div className={`card ${styles.chart}`}>
                    <h3 className={styles.chartTitle}>Lead Sources</h3>
                    <ResponsiveContainer width="100%" height={280}>
                        <BarChart data={sourceData}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#334155" />
                            <XAxis dataKey="name" stroke="#64748b" tick={{ fontSize: 10 }} angle={-30} textAnchor="end" height={60} />
                            <YAxis stroke="#64748b" tick={{ fontSize: 12 }} />
                            <Tooltip contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8 }} />
                            <Bar dataKey="count" radius={[4, 4, 0, 0]}>
                                {sourceData.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
                            </Bar>
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            </div>

            {/* Course Table */}
            <div className="card">
                <h3 className={styles.chartTitle} style={{ marginBottom: 16 }}>📚 Course-wise Enquiry Report</h3>
                {courseData.length > 0 ? (
                    <div className="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course</th>
                                    <th>Total Enquiries</th>
                                    <th>% Share</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                {courseData.map((c, i) => {
                                    const pct = ((c.count / (s.totalLeads || 1)) * 100).toFixed(1);
                                    const maxCount = courseData[0]?.count || 1;
                                    return (
                                        <tr key={c.name}>
                                            <td style={{ color: 'var(--text-muted)', fontSize: 12 }}>{i + 1}</td>
                                            <td><span style={{ color: '#60a5fa', fontWeight: 600 }}>{c.name}</span></td>
                                            <td><strong>{c.count}</strong></td>
                                            <td>{pct}%</td>
                                            <td>
                                                <div style={{ background: 'var(--bg-input)', borderRadius: 4, height: 8, width: 200 }}>
                                                    <div style={{
                                                        background: COLORS[i % COLORS.length],
                                                        height: '100%',
                                                        width: `${(c.count / maxCount) * 100}%`,
                                                        borderRadius: 4,
                                                    }} />
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="empty-state"><p>No course data available</p></div>
                )}
            </div>
        </div>
    );
};

export default Reports;
