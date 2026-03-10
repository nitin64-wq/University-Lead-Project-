import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { leadService } from '../services/leadService';
import StatusBadge from './StatusBadge';
import toast from 'react-hot-toast';
import styles from './LeadTable.module.css';

const LeadTable = ({
    leads = [],
    onRefresh,
    loading = false,
    total = 0,
    page = 1,
    pages = 1,
    onPageChange,
}) => {
    const { isAdmin } = useAuth();
    const navigate = useNavigate();
    const [deleting, setDeleting] = useState(null);

    const handleDelete = async (id, e) => {
        e.stopPropagation();
        if (!window.confirm('Are you sure you want to delete this lead?')) return;
        setDeleting(id);
        try {
            await leadService.delete(id);
            toast.success('Lead deleted successfully');
            onRefresh?.();
        } catch (err) {
            toast.error(err.response?.data?.message || 'Failed to delete lead');
        } finally {
            setDeleting(null);
        }
    };

    const formatDate = (d) => {
        if (!d) return '—';
        return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    const isOverdue = (date) => {
        if (!date) return false;
        return new Date(date) < new Date() && new Date(date).toDateString() !== new Date().toDateString();
    };

    if (loading) {
        return (
            <div className="page-loader"><div className="spinner" /><span>Loading leads...</span></div>
        );
    }

    if (leads.length === 0) {
        return (
            <div className="empty-state">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="23" y1="11" x2="17" y2="11" />
                </svg>
                <h3>No leads found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        );
    }

    return (
        <div className={styles.container}>
            <div className="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Contact</th>
                            <th>Course</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Follow-up</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {leads.map((lead, idx) => (
                            <tr
                                key={lead._id}
                                className={styles.row}
                                onClick={() => navigate(`/leads/${lead._id}`)}
                            >
                                <td className={styles.num}>{(page - 1) * 10 + idx + 1}</td>
                                <td>
                                    <div className={styles.student}>
                                        <div className={styles.studentAvatar}>
                                            {lead.studentName?.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <p className={styles.studentName}>{lead.studentName}</p>
                                            {lead.fatherName && (
                                                <p className={styles.studentMeta}>F: {lead.fatherName}</p>
                                            )}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p className={styles.mobile}>📞 {lead.mobile}</p>
                                    {lead.email && <p className={styles.email}>✉ {lead.email}</p>}
                                </td>
                                <td>
                                    <p className={styles.course}>{lead.interestedCourse || '—'}</p>
                                    {lead.specialization && (
                                        <p className={styles.spec}>{lead.specialization}</p>
                                    )}
                                </td>
                                <td>
                                    <span className={styles.source}>{lead.leadSource || '—'}</span>
                                </td>
                                <td><StatusBadge status={lead.status} /></td>
                                <td>
                                    <span className={styles.counsellor}>
                                        {lead.assignedTo?.name || (
                                            <span style={{ color: 'var(--text-muted)' }}>Unassigned</span>
                                        )}
                                    </span>
                                </td>
                                <td>
                                    <span className={`${styles.followUp} ${isOverdue(lead.followUpDate) ? styles.overdue : ''}`}>
                                        {formatDate(lead.followUpDate)}
                                    </span>
                                </td>
                                <td className={styles.date}>{formatDate(lead.createdAt)}</td>
                                <td>
                                    <div className={styles.actions} onClick={(e) => e.stopPropagation()}>
                                        <button
                                            className="btn-icon"
                                            onClick={() => navigate(`/leads/${lead._id}`)}
                                            title="View Details"
                                        >
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </button>
                                        {isAdmin && (
                                            <>
                                                <button
                                                    className="btn-icon"
                                                    onClick={() => navigate(`/leads/${lead._id}/edit`)}
                                                    title="Edit Lead"
                                                >
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    className="btn-icon"
                                                    style={{ color: deleting === lead._id ? '#f87171' : undefined }}
                                                    onClick={(e) => handleDelete(lead._id, e)}
                                                    title="Delete Lead"
                                                    disabled={deleting === lead._id}
                                                >
                                                    {deleting === lead._id ? (
                                                        <div className="spinner" style={{ width: 14, height: 14 }} />
                                                    ) : (
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                            <polyline points="3,6 5,6 21,6" />
                                                            <path d="M19 6l-1 14H6L5 6" />
                                                            <path d="M10 11v6M14 11v6" />
                                                            <path d="M9 6V4h6v2" />
                                                        </svg>
                                                    )}
                                                </button>
                                            </>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            <div className={styles.footer}>
                <p className={styles.totalInfo}>
                    Showing {(page - 1) * 10 + 1}–{Math.min(page * 10, total)} of{' '}
                    <strong>{total}</strong> leads
                </p>
                <div className="pagination">
                    <button
                        onClick={() => onPageChange?.(1)}
                        disabled={page === 1}
                        title="First"
                    >«</button>
                    <button
                        onClick={() => onPageChange?.(page - 1)}
                        disabled={page === 1}
                    >‹</button>
                    {Array.from({ length: Math.min(pages, 5) }, (_, i) => {
                        let p = i + 1;
                        if (pages > 5) {
                            if (page <= 3) p = i + 1;
                            else if (page >= pages - 2) p = pages - 4 + i;
                            else p = page - 2 + i;
                        }
                        return (
                            <button key={p} onClick={() => onPageChange?.(p)} className={page === p ? 'active' : ''}>
                                {p}
                            </button>
                        );
                    })}
                    <button
                        onClick={() => onPageChange?.(page + 1)}
                        disabled={page === pages}
                    >›</button>
                    <button
                        onClick={() => onPageChange?.(pages)}
                        disabled={page === pages}
                        title="Last"
                    >»</button>
                </div>
            </div>
        </div>
    );
};

export default LeadTable;
