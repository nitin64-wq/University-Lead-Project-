import api from './api';

export const leadService = {
    getAll: (params) => api.get('/leads', { params }),
    getById: (id) => api.get(`/leads/${id}`),
    create: (data) => api.post('/leads', data),
    update: (id, data) => api.put(`/leads/${id}`, data),
    delete: (id) => api.delete(`/leads/${id}`),
    getFollowUps: () => api.get('/leads/followups'),
    exportAll: () => api.get('/leads/export'),
};

export const userService = {
    getAll: () => api.get('/users'),
    getCounsellors: () => api.get('/users/counsellors'),
    getById: (id) => api.get(`/users/${id}`),
    create: (data) => api.post('/users', data),
    update: (id, data) => api.put(`/users/${id}`, data),
    delete: (id) => api.delete(`/users/${id}`),
};

export const remarkService = {
    getByLead: (leadId) => api.get(`/remarks/${leadId}`),
    add: (leadId, data) => api.post(`/remarks/${leadId}`, data),
};

export const historyService = {
    getByLead: (leadId) => api.get(`/history/${leadId}`),
};

export const dashboardService = {
    get: () => api.get('/dashboard'),
};
