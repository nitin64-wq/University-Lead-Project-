const asyncHandler = require('express-async-handler');
const LeadHistory = require('../models/LeadHistory');

// @desc    Get history for a lead
// @route   GET /api/history/:leadId
const getLeadHistory = asyncHandler(async (req, res) => {
    const history = await LeadHistory.find({ lead: req.params.leadId })
        .populate('performedBy', 'name role')
        .populate('previousCounsellor', 'name')
        .populate('newCounsellor', 'name')
        .sort({ createdAt: 1 });
    res.json(history);
});

module.exports = { getLeadHistory };
