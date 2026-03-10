import { useState, useEffect } from 'react';
import { userService } from '../services/leadService';
import styles from './LeadForm.module.css';

const COURSES = ['BCA', 'BTech', 'MBA', 'MCA', 'BBA', 'BSc', 'MSc', 'BCom', 'MCom', 'BA', 'MA', 'BPharm', 'MTech', 'BEd', 'MEd', 'BA LLB', 'LLB', 'BJMC', 'MJMC', 'BHM'];
const SPECIALIZATIONS = ['Artificial Intelligence', 'Machine Learning', 'Data Science', 'Cyber Security', 'Cloud Computing', 'Finance', 'Marketing', 'HR', 'Operations', 'IT', 'Computer Science', 'Electronics', 'Civil', 'Mechanical', 'Chemical', 'Biotechnology'];
const STREAMS = ['Science', 'Commerce', 'Arts', 'Computer', 'Other'];
const SOURCES = ['Website', 'Facebook', 'Instagram', 'Google Ads', 'Walk-in', 'Referral', 'Education Fair', 'Call Enquiry', 'Other'];
const MODES = ['Regular', 'Distance', 'Online'];
const CATEGORIES = ['General', 'SC', 'ST', 'OBC', 'EWS'];
const STATUSES = ['New Lead', 'Contacted', 'Interested', 'Not Interested', 'Admission Done'];
const GENDERS = ['Male', 'Female', 'Other'];

const initialState = {
    studentName: '', fatherName: '', motherName: '', gender: '', dateOfBirth: '',
    category: '', nationality: 'Indian', city: '', state: '', country: 'India', pinCode: '',
    mobile: '', alternateMobile: '', email: '', whatsapp: '',
    tenthMarks: '', twelfthMarks: '', graduationMarks: '', stream: '',
    schoolCollegeName: '', passingYear: '',
    interestedCourse: '', specialization: '', preferredCampus: '', mode: '',
    leadSource: '',
    assignedTo: '', status: 'New Lead', followUpDate: '',
};

const LeadForm = ({ initial = {}, onSubmit, loading = false, isEdit = false }) => {
    const [form, setForm] = useState({ ...initialState, ...initial });
    const [counsellors, setCounsellors] = useState([]);
    const [activeSection, setActiveSection] = useState(0);

    useEffect(() => {
        userService.getCounsellors().then(r => setCounsellors(r.data)).catch(() => { });
    }, []);

    useEffect(() => {
        if (initial && Object.keys(initial).length > 0) {
            const formatted = { ...initial };
            if (formatted.dateOfBirth) {
                formatted.dateOfBirth = new Date(formatted.dateOfBirth).toISOString().split('T')[0];
            }
            if (formatted.followUpDate) {
                formatted.followUpDate = new Date(formatted.followUpDate).toISOString().split('T')[0];
            }
            if (formatted.assignedTo && typeof formatted.assignedTo === 'object') {
                formatted.assignedTo = formatted.assignedTo._id;
            }
            setForm(prev => ({ ...prev, ...formatted }));
        }
    }, [initial?._id]);

    const set = (field, val) => setForm(prev => ({ ...prev, [field]: val }));

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit(form);
    };

    const sections = [
        {
            title: 'Basic Details',
            icon: '👤',
            fields: (
                <div className={styles.grid2}>
                    <Field label="Student Name *" value={form.studentName} onChange={v => set('studentName', v)} placeholder="Full name" required />
                    <Field label="Father Name" value={form.fatherName} onChange={v => set('fatherName', v)} placeholder="Father's name" />
                    <Field label="Mother Name" value={form.motherName} onChange={v => set('motherName', v)} placeholder="Mother's name" />
                    <SelectField label="Gender" value={form.gender} onChange={v => set('gender', v)} options={GENDERS} />
                    <Field label="Date of Birth" value={form.dateOfBirth} onChange={v => set('dateOfBirth', v)} type="date" />
                    <SelectField label="Category" value={form.category} onChange={v => set('category', v)} options={CATEGORIES} />
                    <Field label="Nationality" value={form.nationality} onChange={v => set('nationality', v)} />
                    <Field label="City" value={form.city} onChange={v => set('city', v)} placeholder="City" />
                    <Field label="State" value={form.state} onChange={v => set('state', v)} placeholder="State" />
                    <Field label="Country" value={form.country} onChange={v => set('country', v)} placeholder="Country" />
                    <Field label="PIN Code" value={form.pinCode} onChange={v => set('pinCode', v)} placeholder="PIN Code" />
                </div>
            ),
        },
        {
            title: 'Contact Details',
            icon: '📞',
            fields: (
                <div className={styles.grid2}>
                    <Field label="Mobile Number *" value={form.mobile} onChange={v => set('mobile', v)} placeholder="10-digit mobile" required type="tel" />
                    <Field label="Alternate Mobile" value={form.alternateMobile} onChange={v => set('alternateMobile', v)} placeholder="Alternate number" type="tel" />
                    <Field label="Email ID" value={form.email} onChange={v => set('email', v)} placeholder="student@example.com" type="email" />
                    <Field label="WhatsApp Number" value={form.whatsapp} onChange={v => set('whatsapp', v)} placeholder="WhatsApp number" type="tel" />
                </div>
            ),
        },
        {
            title: 'Academic Details',
            icon: '🎓',
            fields: (
                <div className={styles.grid2}>
                    <Field label="10th Marks / %" value={form.tenthMarks} onChange={v => set('tenthMarks', v)} placeholder="e.g. 85%" />
                    <Field label="12th Marks / %" value={form.twelfthMarks} onChange={v => set('twelfthMarks', v)} placeholder="e.g. 78%" />
                    <Field label="Graduation Marks / %" value={form.graduationMarks} onChange={v => set('graduationMarks', v)} placeholder="For PG courses" />
                    <SelectField label="Stream" value={form.stream} onChange={v => set('stream', v)} options={STREAMS} />
                    <Field label="School / College Name" value={form.schoolCollegeName} onChange={v => set('schoolCollegeName', v)} placeholder="Institution name" />
                    <Field label="Passing Year" value={form.passingYear} onChange={v => set('passingYear', v)} placeholder="e.g. 2024" />
                </div>
            ),
        },
        {
            title: 'Course Interest',
            icon: '📚',
            fields: (
                <div className={styles.grid2}>
                    <div className="form-group">
                        <label className="form-label">Interested Course</label>
                        <select className="form-control" value={form.interestedCourse} onChange={e => set('interestedCourse', e.target.value)}>
                            <option value="">Select course</option>
                            {COURSES.map(c => <option key={c} value={c}>{c}</option>)}
                        </select>
                    </div>
                    <div className="form-group">
                        <label className="form-label">Specialization</label>
                        <select className="form-control" value={form.specialization} onChange={e => set('specialization', e.target.value)}>
                            <option value="">Select specialization</option>
                            {SPECIALIZATIONS.map(s => <option key={s} value={s}>{s}</option>)}
                        </select>
                    </div>
                    <Field label="Preferred Campus / Branch" value={form.preferredCampus} onChange={v => set('preferredCampus', v)} placeholder="e.g. Main Campus" />
                    <SelectField label="Mode" value={form.mode} onChange={v => set('mode', v)} options={MODES} />
                </div>
            ),
        },
        {
            title: 'Lead Source',
            icon: '📣',
            fields: (
                <div className={styles.sourceGrid}>
                    {SOURCES.map(src => (
                        <label key={src} className={`${styles.sourceCard} ${form.leadSource === src ? styles.sourceActive : ''}`}>
                            <input type="radio" name="source" value={src} checked={form.leadSource === src} onChange={() => set('leadSource', src)} />
                            <span className={styles.sourceIcon}>{sourceIcon(src)}</span>
                            <span className={styles.sourceLabel}>{src}</span>
                        </label>
                    ))}
                </div>
            ),
        },
        {
            title: 'Counselling',
            icon: '🤝',
            fields: (
                <div className={styles.grid2}>
                    <div className="form-group">
                        <label className="form-label">Assign To Counsellor</label>
                        <select className="form-control" value={form.assignedTo} onChange={e => set('assignedTo', e.target.value)}>
                            <option value="">-- Select counsellor --</option>
                            {counsellors.map(c => <option key={c._id} value={c._id}>{c.name}</option>)}
                        </select>
                    </div>
                    <div className="form-group">
                        <label className="form-label">Lead Status</label>
                        <select className="form-control" value={form.status} onChange={e => set('status', e.target.value)}>
                            {STATUSES.map(s => <option key={s} value={s}>{s}</option>)}
                        </select>
                    </div>
                    <Field label="Follow-up Date" value={form.followUpDate} onChange={v => set('followUpDate', v)} type="date" />
                </div>
            ),
        },
    ];

    return (
        <form onSubmit={handleSubmit}>
            {/* Section Tabs */}
            <div className={styles.tabs}>
                {sections.map((s, i) => (
                    <button key={i} type="button"
                        className={`${styles.tab} ${activeSection === i ? styles.tabActive : ''}`}
                        onClick={() => setActiveSection(i)}>
                        <span>{s.icon}</span>
                        <span>{s.title}</span>
                    </button>
                ))}
            </div>

            {/* Section Content */}
            <div className={styles.sectionBody}>
                <h3 className={styles.sectionTitle}>
                    {sections[activeSection].icon} {sections[activeSection].title}
                </h3>
                {sections[activeSection].fields}
            </div>

            {/* Navigation + Submit */}
            <div className={styles.formFooter}>
                <div className={styles.navBtns}>
                    {activeSection > 0 && (
                        <button type="button" className="btn btn-secondary" onClick={() => setActiveSection(s => s - 1)}>
                            ← Previous
                        </button>
                    )}
                    {activeSection < sections.length - 1 && (
                        <button type="button" className="btn btn-primary" onClick={() => setActiveSection(s => s + 1)}>
                            Next →
                        </button>
                    )}
                </div>
                <button type="submit" className="btn btn-success" disabled={loading}>
                    {loading ? <><div className="spinner" />{isEdit ? 'Updating...' : 'Creating...'}</> : (isEdit ? '✅ Update Lead' : '✅ Create Lead')}
                </button>
            </div>
        </form>
    );
};

// Helper Components
const Field = ({ label, value, onChange, placeholder, type = 'text', required }) => (
    <div className="form-group">
        <label className="form-label">{label}</label>
        <input
            className="form-control"
            type={type}
            value={value || ''}
            onChange={e => onChange(e.target.value)}
            placeholder={placeholder}
            required={required}
        />
    </div>
);

const SelectField = ({ label, value, onChange, options }) => (
    <div className="form-group">
        <label className="form-label">{label}</label>
        <select className="form-control" value={value || ''} onChange={e => onChange(e.target.value)}>
            <option value="">Select {label}</option>
            {options.map(o => <option key={o} value={o}>{o}</option>)}
        </select>
    </div>
);

const sourceIcon = (src) => {
    const icons = {
        Website: '🌐', Facebook: '👥', Instagram: '📷', 'Google Ads': '🔍',
        'Walk-in': '🚶', Referral: '🤝', 'Education Fair': '🎪', 'Call Enquiry': '📞', Other: '📌',
    };
    return icons[src] || '📌';
};

export default LeadForm;
