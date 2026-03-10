const asyncHandler = require('express-async-handler');
const Remark = require('../models/Remark');
const Lead = require('../models/Lead');
const LeadHistory = require('../models/LeadHistory');

// @desc    Get remarks for a lead
// @route   GET /api/remarks/:leadId
const getRemarks = asyncHandler(async (req, res) => {
    const remarks = await Remark.find({ lead: req.params.leadId })
        .populate('counsellor', 'name email')
        .sort({ createdAt: -1 });
    res.json(remarks);
});

// @desc    Add remark to a lead
// @route   POST /api/remarks/:leadId
const addRemark = asyncHandler(async (req, res) => {
    const { remark, followUpDate, statusAtTime } = req.body;
    const lead = await Lead.findById(req.params.leadId);

    if (!lead) {
        res.status(404);
        throw new Error('Lead not found');
    }

    const newRemark = await Remark.create({
        lead: req.params.leadId,
        counsellor: req.user._id,
        remark,
        followUpDate,
        statusAtTime: statusAtTime || lead.status,
    });

    // Update follow up date if provided
    if (followUpDate) {
        lead.followUpDate = followUpDate;
        await lead.save();
    }

    // Log history
    await LeadHistory.create({
        lead: req.params.leadId,
        action: 'Remark Added',
        performedBy: req.user._id,
        note: remark.substring(0, 100),
    });

    const populated = await Remark.findById(newRemark._id).populate(
        'counsellor',
        'name email'
    );

    res.status(201).json(populated);
});

module.exports = { getRemarks, addRemark };
