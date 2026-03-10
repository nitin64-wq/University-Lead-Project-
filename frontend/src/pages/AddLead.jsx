import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { leadService } from '../services/leadService';
import LeadForm from '../components/LeadForm';
import toast from 'react-hot-toast';
import styles from './AddLead.module.css';

const AddLead = () => {
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const handleSubmit = async (formData) => {
        setLoading(true);
        try {
            const { data } = await leadService.create(formData);
            toast.success('Lead created successfully! 🎉');
            navigate(`/leads/${data._id}`);
        } catch (err) {
            toast.error(err.response?.data?.message || 'Failed to create lead');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className={styles.page}>
            <div className="section-header">
                <div>
                    <h2 className="section-title">Add New Lead</h2>
                    <p className="section-subtitle">Fill in all sections to create a complete student enquiry record</p>
                </div>
                <button className="btn btn-secondary" onClick={() => navigate('/leads')}>
                    ← Back to Leads
                </button>
            </div>
            <div className={styles.formCard}>
                <LeadForm onSubmit={handleSubmit} loading={loading} />
            </div>
        </div>
    );
};

export default AddLead;
