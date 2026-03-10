import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { leadService } from '../services/leadService';
import StatusBadge from '../components/StatusBadge';
import styles from './FollowUps.module.css';

const FollowUps = () => {
    const [leads, setLeads] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all'); // all, overdue, today, upcoming
    const navigate = useNavigate();

    useEffect(() => {
        leadService.getFollowUps()
            .then(r => setLeads(r.data))
            .catch(() => { })
            .finally(() => setLoading(false));
    }, []);

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    const categorize = (lead) => {
        if (!lead.followUpDate) return 'no-date';
        const d = new Date(lead.followUpDate);
        const followDay = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        if (followDay < today) return 'overdue';
        if (followDay.getTime() === today.getTime()) return 'today';
        return 'upcoming';
    };

    const filtered = leads.filter(l => {
        if (filter === 'all') return true;
        return categorize(l) === filter;
    });

    const counts = {
        overdue: leads.filter(l => categorize(l) === 'overdue').length,
        today: leads.filter(l => categorize(l) === 'today').length,
        upcoming: leads.filter(l => categorize(l) === 'upcoming').length,
    };

    const formatDate = (d) =>
        new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

    if (loading) return <div className="page-loader"><div className="spinner" /><span>Loading follow-ups...</span></div>;

    return (
        <div className={styles.page}>
            <div className="section-header">
                <div>
                    <h2 className="section-title">Follow-ups</h2>
                    <p className="section-subtitle">Track and manage all scheduled follow-up calls</p>
                </div>
            </div>

            {/* Summary Cards */}
            <div className={styles.summaryCards}>
                <div className={`${styles.card} ${styles.cardOverdue}`} onClick={() => setFilter('overdue')}>
                    <div className={styles.cardIcon}>🔴</div>
                    <div>
                        <p className={styles.cardVal}>{counts.overdue}</p>
                        <p className={styles.cardLabel}>Overdue</p>
                    </div>
                </div>
                <div className={`${styles.card} ${styles.cardToday}`} onClick={() => setFilter('today')}>
                    <div className={styles.cardIcon}>🟡</div>
                    <div>
                        <p className={styles.cardVal}>{counts.today}</p>
                        <p className={styles.cardLabel}>Due Today</p>
                    </div>
                </div>
                <div className={`${styles.card} ${styles.cardUpcoming}`} onClick={() => setFilter('upcoming')}>
                    <div className={styles.cardIcon}>🟢</div>
                    <div>
                        <p className={styles.cardVal}>{counts.upcoming}</p>
                        <p className={styles.cardLabel}>Upcoming</p>
                    </div>
                </div>
                <div className={`${styles.card} ${styles.cardAll}`} onClick={() => setFilter('all')}>
                    <div className={styles.cardIcon}>📋</div>
                    <div>
                        <p className={styles.cardVal}>{leads.length}</p>
                        <p className={styles.cardLabel}>Total</p>
                    </div>
                </div>
            </div>

            {/* Filter Tabs */}
            <div className={styles.filterTabs}>
                {[
                    { key: 'all', label: 'All Follow-ups', count: leads.length },
                    { key: 'overdue', label: '🔴 Overdue', count: counts.overdue },
                    { key: 'today', label: '🟡 Due Today', count: counts.today },
                    { key: 'upcoming', label: '🟢 Upcoming', count: counts.upcoming },
                ].map(tab => (
                    <button
                        key={tab.key}
                        className={`${styles.filterTab} ${filter === tab.key ? styles.activeTab : ''}`}
                        onClick={() => setFilter(tab.key)}
                    >
                        {tab.label} <span className={styles.tabCount}>{tab.count}</span>
                    </button>
                ))}
            </div>

            {/* List */}
            {filtered.length === 0 ? (
                <div className="empty-state">
                    <span style={{ fontSize: 48 }}>📅</span>
                    <h3>No follow-ups in this category</h3>
                    <p>All caught up! Check another filter.</p>
                </div>
            ) : (
                <div className={styles.list}>
                    {filtered.map(lead => {
                        const cat = categorize(lead);
                        return (
                            <div
                                key={lead._id}
                                className={`${styles.leadCard} ${styles[`cat_${cat}`]}`}
                                onClick={() => navigate(`/leads/${lead._id}`)}
                            >
                                <div className={styles.cardLeft}>
                                    <div className={styles.avatar}>{lead.studentName?.charAt(0).toUpperCase()}</div>
                                    <div>
                                        <p className={styles.studentName}>{lead.studentName}</p>
                                        <p className={styles.mobile}>📞 {lead.mobile}</p>
                                    </div>
                                </div>
                                <div className={styles.cardMid}>
                                    <p className={styles.course}>{lead.interestedCourse || '—'}</p>
                                    <p className={styles.counsellor}>👤 {lead.assignedTo?.name || 'Unassigned'}</p>
                                </div>
                                <div className={styles.cardRight}>
                                    <StatusBadge status={lead.status} />
                                    <div className={`${styles.dateChip} ${cat === 'overdue' ? styles.dateOverdue : cat === 'today' ? styles.dateToday : styles.dateUpcoming}`}>
                                        📅 {formatDate(lead.followUpDate)}
                                        {cat === 'overdue' && <span className={styles.label}> Overdue</span>}
                                        {cat === 'today' && <span className={styles.label}> Today</span>}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
};

export default FollowUps;
