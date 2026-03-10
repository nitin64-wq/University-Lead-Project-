import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { dashboardService } from '../services/leadService';
import { useAuth } from '../context/AuthContext';
import {
    BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, LineChart, Line, Legend,
} from 'recharts';
import StatusBadge from '../components/StatusBadge';
import styles from './Dashboard.module.css';

const COLORS = ['#3b82f6', '#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#0d9488', '#f97316'];

const StatCard = ({ title, value, icon, color, sub, trend }) => (
    <div className={styles.statCard} style={{ '--accent': color }}>
        <div className={styles.statTop}>
            <div className={styles.statIcon}>{icon}</div>
            <div className={styles.statTrend} style={{ color: trend >= 0 ? '#34d399' : '#f87171' }}>
                {trend !== undefined && (trend >= 0 ? '↑' : '↓')}
            </div>
        </div>
        <p className={styles.statValue}>{value}</p>
        <p className={styles.statTitle}>{title}</p>
        {sub && <p className={styles.statSub}>{sub}</p>}
    </div>
);

const Dashboard = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const { isAdmin } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        dashboardService.get()
            .then(r => setData(r.data))
            .catch(() => { })
            .finally(() => setLoading(false));
    }, []);

    if (loading) {
        return <div className="page-loader"><div className="spinner" /><span>Loading dashboard...</span></div>;
    }

    const s = data?.stats || {};
    const monthlyData = (data?.monthlyTrend || []).map(item => ({
        name: `${item._id.month}/${item._id.year.toString().slice(-2)}`,
        Leads: item.count,
    }));

    const statusData = (data?.statusBreakdown || []).map(item => ({
        name: item._id || 'Unknown',
        value: item.count,
    }));

    const sourceData = (data?.sourceBreakdown || []).slice(0, 6).map(item => ({
        name: item._id || 'Unknown',
        count: item.count,
    }));

    const formatDate = (d) =>
        new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

    return (
        <div className={styles.page}>
            {/* Stats Row */}
            <div className={styles.statsGrid}>
                <StatCard title="Total Leads" value={s.totalLeads || 0} icon="🎯" color="#3b82f6" sub="All time" />
                <StatCard title="New Leads" value={s.newLeads || 0} icon="🆕" color="#60a5fa" sub="Awaiting contact" />
                <StatCard title="Interested" value={s.interested || 0} icon="✨" color="#10b981" sub="High potential" />
                <StatCard title="Admission Done" value={s.admissionDone || 0} icon="🎓" color="#7c3aed" sub="Converted" />
                <StatCard title="Pending Follow-ups" value={s.pendingFollowUps || 0} icon="📅" color="#f59e0b" sub="Need attention" />
                {isAdmin && (
                    <StatCard title="Active Counsellors" value={s.totalCounsellors || 0} icon="👥" color="#0d9488" sub="Team members" />
                )}
            </div>

            {/* Charts Row */}
            <div className={styles.chartsRow}>
                {/* Monthly Trend */}
                <div className={`card ${styles.chartCard}`}>
                    <h3 className={styles.chartTitle}>📈 Monthly Lead Trend</h3>
                    <p className={styles.chartSub}>Last 6 months</p>
                    {monthlyData.length > 0 ? (
                        <ResponsiveContainer width="100%" height={220}>
                            <LineChart data={monthlyData}>
                                <CartesianGrid strokeDasharray="3 3" stroke="#334155" />
                                <XAxis dataKey="name" stroke="#64748b" tick={{ fontSize: 12 }} />
                                <YAxis stroke="#64748b" tick={{ fontSize: 12 }} />
                                <Tooltip
                                    contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8 }}
                                    labelStyle={{ color: '#f1f5f9' }}
                                />
                                <Line type="monotone" dataKey="Leads" stroke="#3b82f6" strokeWidth={2} dot={{ fill: '#3b82f6', r: 4 }} />
                            </LineChart>
                        </ResponsiveContainer>
                    ) : (
                        <div className="empty-state"><p>No data available yet</p></div>
                    )}
                </div>

                {/* Status Breakdown */}
                <div className={`card ${styles.chartCard} ${styles.pieCard}`}>
                    <h3 className={styles.chartTitle}>🎯 Status Distribution</h3>
                    <p className={styles.chartSub}>Current lead pipeline</p>
                    {statusData.length > 0 ? (
                        <div className={styles.pieContainer}>
                            <ResponsiveContainer width="100%" height={180}>
                                <PieChart>
                                    <Pie data={statusData} cx="50%" cy="50%" innerRadius={50} outerRadius={80} dataKey="value" paddingAngle={3}>
                                        {statusData.map((_, idx) => <Cell key={idx} fill={COLORS[idx % COLORS.length]} />)}
                                    </Pie>
                                    <Tooltip contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8 }} />
                                </PieChart>
                            </ResponsiveContainer>
                            <div className={styles.pieLegend}>
                                {statusData.map((item, idx) => (
                                    <div key={item.name} className={styles.legendItem}>
                                        <span className={styles.legendDot} style={{ background: COLORS[idx % COLORS.length] }} />
                                        <span className={styles.legendName}>{item.name}</span>
                                        <span className={styles.legendVal}>{item.value}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ) : (
                        <div className="empty-state"><p>No data available yet</p></div>
                    )}
                </div>
            </div>

            {/* Source + Course Row */}
            <div className={styles.chartsRow}>
                {/* Source Breakdown */}
                <div className={`card ${styles.chartCard}`}>
                    <h3 className={styles.chartTitle}>📣 Lead Sources</h3>
                    <p className={styles.chartSub}>Where leads are coming from</p>
                    {sourceData.length > 0 ? (
                        <ResponsiveContainer width="100%" height={220}>
                            <BarChart data={sourceData} layout="vertical">
                                <CartesianGrid strokeDasharray="3 3" stroke="#334155" />
                                <XAxis type="number" stroke="#64748b" tick={{ fontSize: 12 }} />
                                <YAxis type="category" dataKey="name" stroke="#64748b" tick={{ fontSize: 11 }} width={90} />
                                <Tooltip contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8 }} />
                                <Bar dataKey="count" fill="#3b82f6" radius={[0, 4, 4, 0]}>
                                    {sourceData.map((_, idx) => <Cell key={idx} fill={COLORS[idx % COLORS.length]} />)}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                    ) : (
                        <div className="empty-state"><p>No data available yet</p></div>
                    )}
                </div>

                {/* Course Interest */}
                <div className={`card ${styles.chartCard}`}>
                    <h3 className={styles.chartTitle}>📚 Top Courses</h3>
                    <p className={styles.chartSub}>Most enquired programs</p>
                    {(data?.courseBreakdown || []).length > 0 ? (
                        <div className={styles.courseList}>
                            {(data?.courseBreakdown || []).map((c, idx) => (
                                <div key={c._id} className={styles.courseItem}>
                                    <span className={styles.courseRank} style={{ background: COLORS[idx % COLORS.length] + '22', color: COLORS[idx % COLORS.length] }}>
                                        #{idx + 1}
                                    </span>
                                    <span className={styles.courseName}>{c._id}</span>
                                    <div className={styles.courseBar}>
                                        <div className={styles.courseBarFill} style={{
                                            width: `${(c.count / (data?.courseBreakdown[0]?.count || 1)) * 100}%`,
                                            background: COLORS[idx % COLORS.length],
                                        }} />
                                    </div>
                                    <span className={styles.courseCount}>{c.count}</span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="empty-state"><p>No data available yet</p></div>
                    )}
                </div>
            </div>

            {/* Recent Leads */}
            <div className={`card ${styles.recentCard}`}>
                <div className="section-header" style={{ marginBottom: 16 }}>
                    <div>
                        <h3 className={styles.chartTitle}>🕒 Recent Leads</h3>
                        <p className={styles.chartSub}>Latest 5 leads added</p>
                    </div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/leads')}>
                        View All →
                    </button>
                </div>
                {(data?.recentLeads || []).length > 0 ? (
                    <div className="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Mobile</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(data?.recentLeads || []).map(lead => (
                                    <tr key={lead._id} style={{ cursor: 'pointer' }} onClick={() => navigate(`/leads/${lead._id}`)}>
                                        <td><strong>{lead.studentName}</strong></td>
                                        <td>{lead.mobile}</td>
                                        <td>{lead.interestedCourse || '—'}</td>
                                        <td><StatusBadge status={lead.status} /></td>
                                        <td>{lead.assignedTo?.name || <span style={{ color: 'var(--text-muted)' }}>Unassigned</span>}</td>
                                        <td style={{ color: 'var(--text-muted)', fontSize: 12 }}>{formatDate(lead.createdAt)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="empty-state">
                        <p>No leads yet. <button className="btn btn-primary btn-sm" onClick={() => navigate('/leads/add')}>Add first lead →</button></p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default Dashboard;
