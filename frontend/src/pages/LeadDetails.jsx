import { useEffect, useState, useCallback } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { leadService, remarkService, historyService, userService } from '../services/leadService';
import { useAuth } from '../context/AuthContext';
import StatusBadge from '../components/StatusBadge';
import RemarkTimeline from '../components/RemarkTimeline';
import toast from 'react-hot-toast';
import styles from './LeadDetails.module.css';

const STATUSES = ['New Lead', 'Contacted', 'Interested', 'Not Interested', 'Admission Done'];

const Section = ({ title, icon, children }) => (
    <div className={styles.section}>
        <h4 className={styles.sectionTitle}>{icon} {title}</h4>
        <div className={styles.sectionContent}>{children}</div>
    </div>
);

const Field = ({ label, value }) => (
    <div className={styles.field}>
        <span className={styles.fieldLabel}>{label}</span>
        <span className={styles.fieldValue}>{value || '—'}</span>
    </div>
);

const LeadDetails = () => {
    const { id } = useParams();
    const { isAdmin, user } = useAuth();
    const navigate = useNavigate();

    const [lead, setLead] = useState(null);
    const [remarks, setRemarks] = useState([]);
    const [history, setHistory] = useState([]);
    const [counsellors, setCounsellors] = useState([]);
    const [loading, setLoading] = useState(true);
    const [assignModal, setAssignModal] = useState(false);
    const [statusModal, setStatusModal] = useState(false);
    const [selectedCounsellor, setSelectedCounsellor] = useState('');
    const [selectedStatus, setSelectedStatus] = useState('');
    const [selectedFollowUp, setSelectedFollowUp] = useState('');
    const [updating, setUpdating] = useState(false);

    const fetchAll = useCallback(async () => {
        try {
            const [leadRes, remarksRes, historyRes] = await Promise.all([
                leadService.getById(id),
                remarkService.getByLead(id),
                historyService.getByLead(id),
            ]);
            setLead(leadRes.data);
            setRemarks(remarksRes.data);
            setHistory(historyRes.data);
            setSelectedCounsellor(leadRes.data.assignedTo?._id || '');
            setSelectedStatus(leadRes.data.status);
            setSelectedFollowUp(leadRes.data.followUpDate ? new Date(leadRes.data.followUpDate).toISOString().split('T')[0] : '');
        } catch {
            toast.error('Failed to load lead details');
            navigate('/leads');
        } finally {
            setLoading(false);
        }
    }, [id]);

    useEffect(() => {
        fetchAll();
        if (isAdmin) {
            userService.getCounsellors().then(r => setCounsellors(r.data)).catch(() => { });
        }
    }, [fetchAll, isAdmin]);

    const handleAssign = async () => {
        setUpdating(true);
        try {
            await leadService.update(id, { assignedTo: selectedCounsellor || null });
            toast.success('Lead assigned successfully');
            setAssignModal(false);
            fetchAll();
        } catch { toast.error('Failed to assign lead'); }
        finally { setUpdating(false); }
    };

    const handleStatusUpdate = async () => {
        setUpdating(true);
        try {
            const payload = { status: selectedStatus };
            if (selectedFollowUp) payload.followUpDate = selectedFollowUp;
            await leadService.update(id, payload);
            toast.success('Status updated successfully');
            setStatusModal(false);
            fetchAll();
        } catch { toast.error('Failed to update status'); }
        finally { setUpdating(false); }
    };

    const formatDate = (d) => {
        if (!d) return '—';
        return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'long', year: 'numeric' });
    };

    if (loading) return <div className="page-loader"><div className="spinner" /><span>Loading lead details...</span></div>;
    if (!lead) return null;

    const isOverdue = lead.followUpDate && new Date(lead.followUpDate) < new Date();

    return (
        <div className={styles.page}>
            {/* Back + Actions */}
            <div className="section-header">
                <button className="btn btn-secondary" onClick={() => navigate('/leads')}>← Back to Leads</button>
                <div className={styles.actions}>
                    <button className="btn btn-secondary" onClick={() => setStatusModal(true)}>
                        ⚡ Update Status
                    </button>
                    {isAdmin && (
                        <>
                            <button className="btn btn-secondary" onClick={() => setAssignModal(true)}>
                                👤 {lead.assignedTo ? 'Reassign' : 'Assign'}
                            </button>
                            <button className="btn btn-primary" onClick={() => navigate(`/leads/${id}/edit`)}>
                                ✏️ Edit Lead
                            </button>
                        </>
                    )}
                </div>
            </div>

            {/* Lead Header */}
            <div className={styles.leadHeader}>
                <div className={styles.leadAvatar}>
                    {lead.studentName?.charAt(0).toUpperCase()}
                </div>
                <div className={styles.leadHeaderInfo}>
                    <h2 className={styles.leadName}>{lead.studentName}</h2>
                    <div className={styles.leadMeta}>
                        <span>📞 {lead.mobile}</span>
                        {lead.email && <span>✉ {lead.email}</span>}
                        {lead.city && <span>📍 {lead.city}, {lead.state}</span>}
                        {lead.interestedCourse && <span className={styles.course}>🎓 {lead.interestedCourse}{lead.specialization ? ` - ${lead.specialization}` : ''}</span>}
                    </div>
                </div>
                <div className={styles.leadBadges}>
                    <StatusBadge status={lead.status} />
                    {lead.leadSource && (
                        <span className={styles.sourceBadge}>{lead.leadSource}</span>
                    )}
                </div>
            </div>

            {/* Info Grid */}
            <div className={styles.grid}>
                {/* Left Column */}
                <div className={styles.leftCol}>
                    <Section title="Basic Information" icon="👤">
                        <Field label="Student Name" value={lead.studentName} />
                        <Field label="Father Name" value={lead.fatherName} />
                        <Field label="Mother Name" value={lead.motherName} />
                        <Field label="Gender" value={lead.gender} />
                        <Field label="Date of Birth" value={formatDate(lead.dateOfBirth)} />
                        <Field label="Category" value={lead.category} />
                        <Field label="Nationality" value={lead.nationality} />
                        <Field label="Address" value={[lead.city, lead.state, lead.country, lead.pinCode].filter(Boolean).join(', ')} />
                    </Section>

                    <Section title="Contact Details" icon="📞">
                        <Field label="Mobile" value={lead.mobile} />
                        <Field label="Alternate Mobile" value={lead.alternateMobile} />
                        <Field label="Email" value={lead.email} />
                        <Field label="WhatsApp" value={lead.whatsapp} />
                    </Section>

                    <Section title="Academic Details" icon="🎓">
                        <Field label="10th Marks" value={lead.tenthMarks} />
                        <Field label="12th Marks" value={lead.twelfthMarks} />
                        <Field label="Graduation Marks" value={lead.graduationMarks} />
                        <Field label="Stream" value={lead.stream} />
                        <Field label="School / College" value={lead.schoolCollegeName} />
                        <Field label="Passing Year" value={lead.passingYear} />
                    </Section>
                </div>

                {/* Right Column */}
                <div className={styles.rightCol}>
                    <Section title="Course Interest" icon="📚">
                        <Field label="Interested Course" value={lead.interestedCourse} />
                        <Field label="Specialization" value={lead.specialization} />
                        <Field label="Preferred Campus" value={lead.preferredCampus} />
                        <Field label="Mode" value={lead.mode} />
                    </Section>

                    <Section title="Counselling Info" icon="🤝">
                        <div className={styles.counsellingCard}>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Status</span>
                                <StatusBadge status={lead.status} />
                            </div>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Assigned To</span>
                                <span className={styles.counsellorChip}>
                                    {lead.assignedTo ? (
                                        <><div className={styles.counsellorDot} />{lead.assignedTo.name}</>
                                    ) : <span style={{ color: 'var(--text-muted)' }}>Not assigned</span>}
                                </span>
                            </div>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Follow-up Date</span>
                                <span className={isOverdue ? styles.overdue : styles.fieldValue}>
                                    {formatDate(lead.followUpDate)}
                                    {isOverdue && ' ⚠️ Overdue'}
                                </span>
                            </div>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Lead Source</span>
                                <span className={styles.fieldValue}>{lead.leadSource || '—'}</span>
                            </div>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Created On</span>
                                <span className={styles.fieldValue}>{formatDate(lead.createdAt)}</span>
                            </div>
                            <div className={styles.counsellingRow}>
                                <span className={styles.fieldLabel}>Last Updated</span>
                                <span className={styles.fieldValue}>{formatDate(lead.updatedAt)}</span>
                            </div>
                        </div>
                    </Section>
                </div>
            </div>

            {/* Timeline */}
            <RemarkTimeline
                remarks={remarks}
                history={history}
                leadId={id}
                onRefresh={fetchAll}
            />

            {/* Assign Modal */}
            {assignModal && (
                <div className="modal-overlay" onClick={() => setAssignModal(false)}>
                    <div className="modal-box" style={{ maxWidth: 420 }} onClick={e => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3 className="modal-title">👤 {lead.assignedTo ? 'Reassign' : 'Assign'} Lead</h3>
                            <button className="modal-close" onClick={() => setAssignModal(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            {lead.assignedTo && (
                                <div className={styles.currentCounsellor}>
                                    Current: <strong>{lead.assignedTo.name}</strong>
                                </div>
                            )}
                            <div className="form-group">
                                <label className="form-label">Select Counsellor</label>
                                <select className="form-control" value={selectedCounsellor} onChange={e => setSelectedCounsellor(e.target.value)}>
                                    <option value="">-- Unassign --</option>
                                    {counsellors.map(c => <option key={c._id} value={c._id}>{c.name} ({c.email})</option>)}
                                </select>
                            </div>
                        </div>
                        <div className="modal-footer">
                            <button className="btn btn-secondary" onClick={() => setAssignModal(false)}>Cancel</button>
                            <button className="btn btn-primary" onClick={handleAssign} disabled={updating}>
                                {updating ? <><div className="spinner" />Assigning...</> : '✅ Confirm Assignment'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Status Modal */}
            {statusModal && (
                <div className="modal-overlay" onClick={() => setStatusModal(false)}>
                    <div className="modal-box" style={{ maxWidth: 420 }} onClick={e => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3 className="modal-title">⚡ Update Lead Status</h3>
                            <button className="modal-close" onClick={() => setStatusModal(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <div className="form-group">
                                <label className="form-label">New Status</label>
                                <select className="form-control" value={selectedStatus} onChange={e => setSelectedStatus(e.target.value)}>
                                    {STATUSES.map(s => <option key={s} value={s}>{s}</option>)}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Follow-up Date</label>
                                <input type="date" className="form-control" value={selectedFollowUp} onChange={e => setSelectedFollowUp(e.target.value)} />
                            </div>
                        </div>
                        <div className="modal-footer">
                            <button className="btn btn-secondary" onClick={() => setStatusModal(false)}>Cancel</button>
                            <button className="btn btn-primary" onClick={handleStatusUpdate} disabled={updating}>
                                {updating ? <><div className="spinner" />Updating...</> : '✅ Update Status'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default LeadDetails;
