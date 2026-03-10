import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { leadService, userService } from '../services/leadService';
import { useAuth } from '../context/AuthContext';
import LeadTable from '../components/LeadTable';
import toast from 'react-hot-toast';
import * as XLSX from 'xlsx';
import styles from './Leads.module.css';

const STATUSES = ['', 'New Lead', 'Contacted', 'Interested', 'Not Interested', 'Admission Done'];

const Leads = () => {
    const { isAdmin } = useAuth();
    const navigate = useNavigate();
    const [leads, setLeads] = useState([]);
    const [counsellors, setCounsellors] = useState([]);
    const [loading, setLoading] = useState(true);
    const [total, setTotal] = useState(0);
    const [page, setPage] = useState(1);
    const [pages, setPages] = useState(1);
    const [exporting, setExporting] = useState(false);

    const [filters, setFilters] = useState({
        search: '', status: '', counsellor: '', course: '',
    });
    const [debouncedSearch, setDebouncedSearch] = useState('');

    useEffect(() => {
        if (isAdmin) {
            userService.getCounsellors().then(r => setCounsellors(r.data)).catch(() => { });
        }
    }, [isAdmin]);

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(filters.search), 400);
        return () => clearTimeout(timer);
    }, [filters.search]);

    const fetchLeads = useCallback(async () => {
        setLoading(true);
        try {
            const params = { page, limit: 10 };
            if (debouncedSearch) params.search = debouncedSearch;
            if (filters.status) params.status = filters.status;
            if (filters.counsellor) params.counsellor = filters.counsellor;
            if (filters.course) params.course = filters.course;

            const { data } = await leadService.getAll(params);
            setLeads(data.leads);
            setTotal(data.total);
            setPages(data.pages);
        } catch (err) {
            toast.error('Failed to fetch leads');
        } finally {
            setLoading(false);
        }
    }, [page, debouncedSearch, filters.status, filters.counsellor, filters.course]);

    useEffect(() => { fetchLeads(); }, [fetchLeads]);

    const setFilter = (key, val) => {
        setFilters(prev => ({ ...prev, [key]: val }));
        setPage(1);
    };

    const clearFilters = () => {
        setFilters({ search: '', status: '', counsellor: '', course: '' });
        setPage(1);
    };

    const handleExport = async () => {
        setExporting(true);
        try {
            const { data } = await leadService.exportAll();
            const rows = data.map(lead => ({
                'Student Name': lead.studentName,
                'Father Name': lead.fatherName,
                'Mobile': lead.mobile,
                'Email': lead.email,
                'Course': lead.interestedCourse,
                'Specialization': lead.specialization,
                'Status': lead.status,
                'Lead Source': lead.leadSource,
                'Assigned To': lead.assignedTo?.name || '',
                'City': lead.city,
                'State': lead.state,
                '10th Marks': lead.tenthMarks,
                '12th Marks': lead.twelfthMarks,
                'Stream': lead.stream,
                'Follow-up Date': lead.followUpDate ? new Date(lead.followUpDate).toLocaleDateString('en-IN') : '',
                'Created Date': new Date(lead.createdAt).toLocaleDateString('en-IN'),
            }));
            const ws = XLSX.utils.json_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Leads');
            XLSX.writeFile(wb, `leads_export_${new Date().toISOString().split('T')[0]}.xlsx`);
            toast.success('Leads exported to Excel!');
        } catch {
            toast.error('Export failed');
        } finally {
            setExporting(false);
        }
    };

    const hasFilters = Object.values(filters).some(Boolean);

    return (
        <div className={styles.page}>
            {/* Header */}
            <div className="section-header">
                <div>
                    <h2 className="section-title">All Leads</h2>
                    <p className="section-subtitle">{total} total leads found</p>
                </div>
                <div className={styles.headerActions}>
                    <button className="btn btn-secondary btn-sm" onClick={handleExport} disabled={exporting}>
                        {exporting ? <><div className="spinner" />Exporting...</> : '📥 Export Excel'}
                    </button>
                    {isAdmin && (
                        <button className="btn btn-primary" onClick={() => navigate('/leads/add')}>
                            + Add New Lead
                        </button>
                    )}
                </div>
            </div>

            {/* Filters */}
            <div className={styles.filters}>
                <div className={styles.searchWrap}>
                    <svg className={styles.searchIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input
                        className={`form-control ${styles.searchInput}`}
                        placeholder="Search by name, phone, email..."
                        value={filters.search}
                        onChange={e => setFilter('search', e.target.value)}
                    />
                </div>
                <select className={`form-control ${styles.select}`} value={filters.status} onChange={e => setFilter('status', e.target.value)}>
                    <option value="">All Statuses</option>
                    {STATUSES.slice(1).map(s => <option key={s} value={s}>{s}</option>)}
                </select>
                {isAdmin && (
                    <select className={`form-control ${styles.select}`} value={filters.counsellor} onChange={e => setFilter('counsellor', e.target.value)}>
                        <option value="">All Counsellors</option>
                        {counsellors.map(c => <option key={c._id} value={c._id}>{c.name}</option>)}
                    </select>
                )}
                <input
                    className={`form-control ${styles.select}`}
                    placeholder="Filter by course..."
                    value={filters.course}
                    onChange={e => setFilter('course', e.target.value)}
                />
                {hasFilters && (
                    <button className="btn btn-secondary btn-sm" onClick={clearFilters}>✕ Clear</button>
                )}
            </div>

            {/* Table */}
            <LeadTable
                leads={leads}
                loading={loading}
                total={total}
                page={page}
                pages={pages}
                onPageChange={setPage}
                onRefresh={fetchLeads}
            />
        </div>
    );
};

export default Leads;
