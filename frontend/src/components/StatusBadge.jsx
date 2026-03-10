const STATUS_CONFIG = {
    'New Lead': { class: 'badge-new', dot: '#60a5fa' },
    'Contacted': { class: 'badge-contacted', dot: '#fbbf24' },
    'Interested': { class: 'badge-interested', dot: '#34d399' },
    'Not Interested': { class: 'badge-not-interested', dot: '#f87171' },
    'Admission Done': { class: 'badge-admission', dot: '#a78bfa' },
};

const StatusBadge = ({ status }) => {
    const config = STATUS_CONFIG[status] || { class: 'badge-new', dot: '#60a5fa' };
    return (
        <span className={`badge ${config.class}`}>
            <span style={{
                width: 6, height: 6, borderRadius: '50%',
                background: config.dot, display: 'inline-block', flexShrink: 0
            }} />
            {status}
        </span>
    );
};

export default StatusBadge;
