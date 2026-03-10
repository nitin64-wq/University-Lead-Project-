import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { leadService } from '../services/leadService';
import LeadForm from '../components/LeadForm';
import toast from 'react-hot-toast';
import styles from './AddLead.module.css';

const EditLead = () => {
    const { id } = useParams();
    const [lead, setLead] = useState(null);
    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        leadService.getById(id)
            .then(r => setLead(r.data))
            .catch(() => toast.error('Failed to load lead'))
            .finally(() => setFetching(false));
    }, [id]);

    const handleSubmit = async (formData) => {
        setLoading(true);
        try {
            await leadService.update(id, formData);
            toast.success('Lead updated successfully!');
            navigate(`/leads/${id}`);
        } catch (err) {
            toast.error(err.response?.data?.message || 'Failed to update lead');
        } finally {
            setLoading(false);
        }
    };

    if (fetching) return <div className="page-loader"><div className="spinner" /><span>Loading lead...</span></div>;

    return (
        <div className={styles.page}>
            <div className="section-header">
                <div>
                    <h2 className="section-title">Edit Lead</h2>
                    <p className="section-subtitle">Update information for {lead?.studentName}</p>
                </div>
                <button className="btn btn-secondary" onClick={() => navigate(`/leads/${id}`)}>
                    ← Back to Details
                </button>
            </div>
            <div className={styles.formCard}>
                {lead && <LeadForm initial={lead} onSubmit={handleSubmit} loading={loading} isEdit />}
            </div>
        </div>
    );
};

export default EditLead;
