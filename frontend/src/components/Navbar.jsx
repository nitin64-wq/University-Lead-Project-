import { useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import styles from './Navbar.module.css';

const pageTitles = {
    '/dashboard': { title: 'Dashboard', subtitle: 'Overview of your leads & performance' },
    '/leads': { title: 'Leads', subtitle: 'Manage all student enquiries' },
    '/leads/add': { title: 'Add New Lead', subtitle: 'Create a new student enquiry' },
    '/followups': { title: 'Follow-ups', subtitle: 'Track upcoming and overdue follow-ups' },
    '/users': { title: 'User Management', subtitle: 'Manage admin and counsellor accounts' },
    '/reports': { title: 'Reports & Analytics', subtitle: 'Detailed insights and data export' },
};

const Navbar = () => {
    const { user } = useAuth();
    const location = useLocation();

    const path = location.pathname;
    let pageInfo = pageTitles[path];

    if (!pageInfo) {
        if (path.includes('/leads/') && path.includes('/edit')) {
            pageInfo = { title: 'Edit Lead', subtitle: 'Update student enquiry details' };
        } else if (path.includes('/leads/')) {
            pageInfo = { title: 'Lead Details', subtitle: 'View complete lead information' };
        } else {
            pageInfo = { title: 'UniLead CRM', subtitle: '' };
        }
    }

    const now = new Date();
    const dateStr = now.toLocaleDateString('en-IN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    return (
        <header className={styles.navbar}>
            <div className={styles.left}>
                <div>
                    <h1 className={styles.title}>{pageInfo.title}</h1>
                    {pageInfo.subtitle && <p className={styles.subtitle}>{pageInfo.subtitle}</p>}
                </div>
            </div>
            <div className={styles.right}>
                <div className={styles.date}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <span>{dateStr}</span>
                </div>
                <div className={styles.userChip}>
                    <div className={styles.avatar}>{user?.name?.charAt(0).toUpperCase()}</div>
                    <div>
                        <p className={styles.userName}>{user?.name}</p>
                        <p className={styles.userRole}>{user?.role === 'admin' ? 'Administrator' : 'Counsellor'}</p>
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Navbar;
