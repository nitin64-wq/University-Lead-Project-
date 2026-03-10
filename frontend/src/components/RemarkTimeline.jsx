import { useState } from 'react';
import { remarkService } from '../services/leadService';
import { useAuth } from '../context/AuthContext';
import toast from 'react-hot-toast';
import styles from './RemarkTimeline.module.css';

const ACTION_ICONS = {
    'Created': '🆕',
    'Assigned': '📋',
    'Reassigned': '🔄',
    'Status Changed': '⚡',
    'Remark Added': '💬',
    'Updated': '✏️',
};

const RemarkTimeline = ({ remarks = [], history = [], leadId, onRefresh }) => {
    const { user } = useAuth();
    const [showForm, setShowForm] = useState(false);
    const [remark, setRemark] = useState('');
    const [followUpDate, setFollowUpDate] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [activeTab, setActiveTab] = useState('remarks');

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!remark.trim()) return;
        setSubmitting(true);
        try {
            await remarkService.add(leadId, { remark, followUpDate: followUpDate || undefined });
            toast.success('Remark added successfully');
            setRemark('');
            setFollowUpDate('');
            setShowForm(false);
            onRefresh?.();
        } catch {
            toast.error('Failed to add remark');
        } finally {
            setSubmitting(false);
        }
    };

    const formatDate = (d) =>
        new Date(d).toLocaleString('en-IN', {
            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
        });

    const formatShortDate = (d) =>
        new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

    return (
        <div className={styles.wrapper}>
            <div className={styles.header}>
                <div className={styles.tabs}>
                    <button
                        className={`${styles.tab} ${activeTab === 'remarks' ? styles.tabActive : ''}`}
                        onClick={() => setActiveTab('remarks')}
                    >
                        💬 Remarks ({remarks.length})
                    </button>
                    <button
                        className={`${styles.tab} ${activeTab === 'history' ? styles.tabActive : ''}`}
                        onClick={() => setActiveTab('history')}
                    >
                        📋 History ({history.length})
                    </button>
                </div>
                {activeTab === 'remarks' && (
                    <button
                        className="btn btn-primary btn-sm"
                        onClick={() => setShowForm((s) => !s)}
                    >
                        {showForm ? '✕ Cancel' : '+ Add Remark'}
                    </button>
                )}
            </div>

            {/* Add Remark Form */}
            {activeTab === 'remarks' && showForm && (
                <form onSubmit={handleSubmit} className={styles.addForm}>
                    <div className={styles.formAvatar}>
                        {user?.name?.charAt(0).toUpperCase()}
                    </div>
                    <div className={styles.formFields}>
                        <textarea
                            className={`form-control ${styles.textarea}`}
                            placeholder="Write your remark about this lead..."
                            value={remark}
                            onChange={(e) => setRemark(e.target.value)}
                            rows={3}
                            required
                        />
                        <div className={styles.formRow}>
                            <div className="form-group" style={{ flex: 1 }}>
                                <label className="form-label">Next Follow-up Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={followUpDate}
                                    onChange={(e) => setFollowUpDate(e.target.value)}
                                />
                            </div>
                            <button type="submit" className="btn btn-primary" disabled={submitting} style={{ alignSelf: 'flex-end' }}>
                                {submitting ? <><div className="spinner" />Saving...</> : '💬 Add Remark'}
                            </button>
                        </div>
                    </div>
                </form>
            )}

            {/* Remarks Timeline */}
            {activeTab === 'remarks' && (
                <div className={styles.timeline}>
                    {remarks.length === 0 ? (
                        <div className="empty-state" style={{ padding: '40px' }}>
                            <span style={{ fontSize: 36 }}>💬</span>
                            <h3>No remarks yet</h3>
                            <p>Add the first remark for this lead</p>
                        </div>
                    ) : (
                        remarks.map((r, idx) => (
                            <div key={r._id} className={styles.timelineItem}>
                                <div className={styles.timelineLeft}>
                                    <div className={styles.timelineAvatar}>
                                        {r.counsellor?.name?.charAt(0).toUpperCase() || '?'}
                                    </div>
                                    {idx < remarks.length - 1 && <div className={styles.timelineLine} />}
                                </div>
                                <div className={styles.timelineCard}>
                                    <div className={styles.timelineCardHeader}>
                                        <span className={styles.counsellorName}>{r.counsellor?.name || 'Unknown'}</span>
                                        <span className={styles.timelineDate}>{formatDate(r.createdAt)}</span>
                                    </div>
                                    <p className={styles.remarkText}>{r.remark}</p>
                                    {r.followUpDate && (
                                        <div className={styles.followUpTag}>
                                            📅 Follow-up: {formatShortDate(r.followUpDate)}
                                        </div>
                                    )}
                                    {r.statusAtTime && (
                                        <span className={`badge badge-new ${styles.statusTag}`}>{r.statusAtTime}</span>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            )}

            {/* History Timeline */}
            {activeTab === 'history' && (
                <div className={styles.timeline}>
                    {history.length === 0 ? (
                        <div className="empty-state" style={{ padding: '40px' }}>
                            <span style={{ fontSize: 36 }}>📋</span>
                            <h3>No history yet</h3>
                        </div>
                    ) : (
                        history.map((h, idx) => (
                            <div key={h._id} className={styles.timelineItem}>
                                <div className={styles.timelineLeft}>
                                    <div className={styles.historyIcon}>
                                        {ACTION_ICONS[h.action] || '📌'}
                                    </div>
                                    {idx < history.length - 1 && <div className={styles.timelineLine} />}
                                </div>
                                <div className={styles.timelineCard}>
                                    <div className={styles.timelineCardHeader}>
                                        <span className={styles.historyAction}>{h.action}</span>
                                        <span className={styles.timelineDate}>{formatDate(h.createdAt)}</span>
                                    </div>
                                    <p className={styles.historyNote}>{h.note}</p>
                                    {h.previousCounsellor && (
                                        <p className={styles.historyMeta}>
                                            <span style={{ color: '#f87171' }}>From: {h.previousCounsellor.name}</span>
                                            {h.newCounsellor && (
                                                <span style={{ color: '#34d399' }}> → To: {h.newCounsellor.name}</span>
                                            )}
                                        </p>
                                    )}
                                    {h.previousStatus && (
                                        <p className={styles.historyMeta}>
                                            <span style={{ color: '#f87171' }}>{h.previousStatus}</span>
                                            {h.newStatus && (
                                                <span style={{ color: '#34d399' }}> → {h.newStatus}</span>
                                            )}
                                        </p>
                                    )}
                                    <p className={styles.historyBy}>by {h.performedBy?.name || 'System'}</p>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            )}
        </div>
    );
};

export default RemarkTimeline;
