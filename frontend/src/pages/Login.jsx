import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import toast from 'react-hot-toast';
import styles from './Login.module.css';

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPass, setShowPass] = useState(false);
    const [loading, setLoading] = useState(false);
    const { login } = useAuth();
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await login(email, password);
            toast.success('Welcome back!');
            navigate('/dashboard');
        } catch (err) {
            toast.error(err.response?.data?.message || 'Invalid credentials');
        } finally {
            setLoading(false);
        }
    };

    const fillDemo = (type) => {
        if (type === 'admin') { setEmail('admin@university.edu'); setPassword('admin123'); }
        else { setEmail('rahul@university.edu'); setPassword('rahul123'); }
    };

    return (
        <div className={styles.page}>
            {/* Background Effects */}
            <div className={styles.bgOrb1} />
            <div className={styles.bgOrb2} />

            <div className={styles.container}>
                {/* Left Panel */}
                <div className={styles.leftPanel}>
                    <div className={styles.brand}>
                        <div className={styles.brandIcon}>
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" fill="currentColor" />
                                <path d="M2 17l10 5 10-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" fill="none" opacity="0.7" />
                                <path d="M2 12l10 5 10-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" fill="none" opacity="0.5" />
                            </svg>
                        </div>
                        <div>
                            <h1 className={styles.brandName}>UniLead CRM</h1>
                            <p className={styles.brandTagline}>University Lead Management System</p>
                        </div>
                    </div>
                    <div className={styles.features}>
                        {[
                            { icon: '🎯', title: 'Smart Lead Tracking', desc: 'Track every student enquiry from first contact to admission.' },
                            { icon: '👥', title: 'Team Collaboration', desc: 'Assign and reassign leads across your counselling team.' },
                            { icon: '📊', title: 'Powerful Analytics', desc: 'Real-time insights to optimize your admission pipeline.' },
                            { icon: '📅', title: 'Follow-up Reminders', desc: 'Never miss a follow-up with intelligent date tracking.' },
                        ].map((f) => (
                            <div key={f.title} className={styles.featureItem}>
                                <span className={styles.featureIcon}>{f.icon}</span>
                                <div>
                                    <p className={styles.featureTitle}>{f.title}</p>
                                    <p className={styles.featureDesc}>{f.desc}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Right Panel - Login Form */}
                <div className={styles.rightPanel}>
                    <div className={styles.card}>
                        <h2 className={styles.formTitle}>Sign In</h2>
                        <p className={styles.formSubtitle}>Access your CRM dashboard</p>

                        {/* Quick Login Buttons */}
                        <div className={styles.demoSection}>
                            <p className={styles.demoLabel}>Quick Demo Login:</p>
                            <div className={styles.demoBtns}>
                                <button type="button" onClick={() => fillDemo('admin')} className={styles.demoBtn}>
                                    🔑 Admin
                                </button>
                                <button type="button" onClick={() => fillDemo('counsellor')} className={styles.demoBtn}>
                                    🎓 Counsellor
                                </button>
                            </div>
                        </div>

                        <div className={styles.divider}><span>or enter credentials</span></div>

                        <form onSubmit={handleSubmit} className={styles.form}>
                            <div className="form-group">
                                <label className="form-label">Email Address</label>
                                <div className={styles.inputWrapper}>
                                    <svg className={styles.inputIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                    </svg>
                                    <input
                                        className={`form-control ${styles.input}`}
                                        type="email"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        placeholder="admin@university.edu"
                                        required
                                        autoComplete="email"
                                    />
                                </div>
                            </div>

                            <div className="form-group">
                                <label className="form-label">Password</label>
                                <div className={styles.inputWrapper}>
                                    <svg className={styles.inputIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input
                                        className={`form-control ${styles.input}`}
                                        type={showPass ? 'text' : 'password'}
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        placeholder="Enter your password"
                                        required
                                    />
                                    <button type="button" className={styles.togglePass} onClick={() => setShowPass(!showPass)}>
                                        {showPass ? '🙈' : '👁️'}
                                    </button>
                                </div>
                            </div>

                            <button type="submit" className={`btn btn-primary ${styles.submitBtn}`} disabled={loading}>
                                {loading ? (
                                    <><div className="spinner" /> Signing in...</>
                                ) : (
                                    <>Sign In →</>
                                )}
                            </button>
                        </form>

                        <p className={styles.version}>UniLead CRM v1.0 · University Edition</p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Login;
