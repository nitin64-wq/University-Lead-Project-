import { useEffect, useState } from 'react';
import { userService } from '../services/leadService';
import { useAuth } from '../context/AuthContext';
import toast from 'react-hot-toast';
import styles from './Users.module.css';
import { Navigate } from 'react-router-dom';

const ROLES = ['admin', 'counsellor'];

const emptyForm = { name: '', email: '', password: '', role: 'counsellor', phone: '' };

const Users = () => {
    const { isAdmin } = useAuth();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [modal, setModal] = useState(false);
    const [editUser, setEditUser] = useState(null);
    const [form, setForm] = useState(emptyForm);
    const [submitting, setSubmitting] = useState(false);
    const [deleting, setDeleting] = useState(null);

    if (!isAdmin) return <Navigate to="/dashboard" />;

    const fetchUsers = () => {
        setLoading(true);
        userService.getAll()
            .then(r => setUsers(r.data))
            .catch(() => toast.error('Failed to load users'))
            .finally(() => setLoading(false));
    };

    useEffect(() => { fetchUsers(); }, []);

    const openCreate = () => {
        setEditUser(null);
        setForm(emptyForm);
        setModal(true);
    };

    const openEdit = (u) => {
        setEditUser(u);
        setForm({ name: u.name, email: u.email, password: '', role: u.role, phone: u.phone || '' });
        setModal(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            if (editUser) {
                const payload = { ...form };
                if (!payload.password) delete payload.password;
                await userService.update(editUser._id, payload);
                toast.success('User updated!');
            } else {
                await userService.create(form);
                toast.success('User created!');
            }
            setModal(false);
            fetchUsers();
        } catch (err) {
            toast.error(err.response?.data?.message || 'Failed to save user');
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (u) => {
        if (!window.confirm(`Delete user "${u.name}"? This cannot be undone.`)) return;
        setDeleting(u._id);
        try {
            await userService.delete(u._id);
            toast.success('User deleted');
            fetchUsers();
        } catch {
            toast.error('Failed to delete user');
        } finally {
            setDeleting(null);
        }
    };

    const formatDate = (d) => new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

    return (
        <div className={styles.page}>
            <div className="section-header">
                <div>
                    <h2 className="section-title">User Management</h2>
                    <p className="section-subtitle">{users.length} users registered</p>
                </div>
                <button className="btn btn-primary" onClick={openCreate}>+ Add User</button>
            </div>

            {loading ? (
                <div className="page-loader"><div className="spinner" /><span>Loading users...</span></div>
            ) : (
                <div className="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.map((u, idx) => (
                                <tr key={u._id}>
                                    <td className={styles.idx}>{idx + 1}</td>
                                    <td>
                                        <div className={styles.userCell}>
                                            <div className={styles.avatar}>{u.name?.charAt(0).toUpperCase()}</div>
                                            <div>
                                                <p className={styles.name}>{u.name}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className={styles.email}>{u.email}</td>
                                    <td className={styles.phone}>{u.phone || '—'}</td>
                                    <td>
                                        <span className={`${styles.roleBadge} ${u.role === 'admin' ? styles.admin : styles.counsellor}`}>
                                            {u.role === 'admin' ? '🔑 Admin' : '🎓 Counsellor'}
                                        </span>
                                    </td>
                                    <td>
                                        <span className={`${styles.statusDot} ${u.isActive ? styles.active : styles.inactive}`}>
                                            {u.isActive ? '● Active' : '● Inactive'}
                                        </span>
                                    </td>
                                    <td className={styles.date}>{formatDate(u.createdAt)}</td>
                                    <td>
                                        <div className={styles.actions}>
                                            <button className="btn-icon" onClick={() => openEdit(u)} title="Edit">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <button
                                                className="btn-icon"
                                                onClick={() => handleDelete(u)}
                                                disabled={deleting === u._id}
                                                title="Delete"
                                                style={{ color: '#f87171' }}
                                            >
                                                {deleting === u._id ? <div className="spinner" style={{ width: 14, height: 14 }} /> : (
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                        <polyline points="3,6 5,6 21,6" />
                                                        <path d="M19 6l-1 14H6L5 6" /><path d="M10 11v6M14 11v6" /><path d="M9 6V4h6v2" />
                                                    </svg>
                                                )}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {/* Modal */}
            {modal && (
                <div className="modal-overlay" onClick={() => setModal(false)}>
                    <div className="modal-box" style={{ maxWidth: 480 }} onClick={e => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3 className="modal-title">{editUser ? '✏️ Edit User' : '+ Create New User'}</h3>
                            <button className="modal-close" onClick={() => setModal(false)}>✕</button>
                        </div>
                        <form onSubmit={handleSubmit}>
                            <div className="modal-body">
                                <div className={styles.formGrid}>
                                    <div className="form-group">
                                        <label className="form-label">Full Name *</label>
                                        <input className="form-control" value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))} placeholder="John Doe" required />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Email *</label>
                                        <input className="form-control" type="email" value={form.email} onChange={e => setForm(p => ({ ...p, email: e.target.value }))} placeholder="john@university.edu" required />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">{editUser ? 'New Password (leave blank to keep)' : 'Password *'}</label>
                                        <input className="form-control" type="password" value={form.password} onChange={e => setForm(p => ({ ...p, password: e.target.value }))} placeholder={editUser ? 'Leave blank to keep current' : 'Min 6 characters'} required={!editUser} />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Phone</label>
                                        <input className="form-control" value={form.phone} onChange={e => setForm(p => ({ ...p, phone: e.target.value }))} placeholder="10-digit phone" />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Role *</label>
                                        <select className="form-control" value={form.role} onChange={e => setForm(p => ({ ...p, role: e.target.value }))}>
                                            <option value="counsellor">🎓 Counsellor</option>
                                            <option value="admin">🔑 Admin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-secondary" onClick={() => setModal(false)}>Cancel</button>
                                <button type="submit" className="btn btn-primary" disabled={submitting}>
                                    {submitting ? <><div className="spinner" />Saving...</> : (editUser ? '✅ Update User' : '✅ Create User')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Users;
