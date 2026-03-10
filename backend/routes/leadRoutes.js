const express = require('express');
const router = express.Router();
const {
    getLeads,
    getLeadById,
    createLead,
    updateLead,
    deleteLead,
    getFollowUps,
    exportLeads,
} = require('../controllers/leadController');
const { protect, adminOnly } = require('../middleware/authMiddleware');

router.get('/followups', protect, getFollowUps);
router.get('/export', protect, exportLeads);
router.route('/').get(protect, getLeads).post(protect, adminOnly, createLead);
router
    .route('/:id')
    .get(protect, getLeadById)
    .put(protect, updateLead)
    .delete(protect, adminOnly, deleteLead);

module.exports = router;
