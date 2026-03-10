const asyncHandler = require('express-async-handler');
const Lead = require('../models/Lead');
const LeadHistory = require('../models/LeadHistory');

const normalizeMobile = (value = '') => value.replace(/\D/g, '');
const normalizeEmail = (value = '') => value.trim().toLowerCase();

const assertNoDuplicateLead = async ({ mobile, email, excludeLeadId }) => {
    const duplicateFilters = [];

    const normalizedMobile = normalizeMobile(mobile);
    if (normalizedMobile) {
        duplicateFilters.push({ mobile: normalizedMobile });
    }

    const normalizedEmail = normalizeEmail(email);
    if (normalizedEmail) {
        duplicateFilters.push({ email: normalizedEmail });
    }

    if (!duplicateFilters.length) return;

    const query = {
        $or: duplicateFilters,
    };

    if (excludeLeadId) {
        query._id = { $ne: excludeLeadId };
    }

    const duplicateLead = await Lead.findOne(query).select('studentName mobile email');
    if (!duplicateLead) return;

    const duplicateBy = [];
    if (normalizedMobile && normalizeMobile(duplicateLead.mobile || '') === normalizedMobile) {
        duplicateBy.push('mobile number');
    }
    if (normalizedEmail && normalizeEmail(duplicateLead.email || '') === normalizedEmail) {
        duplicateBy.push('email');
    }

    const duplicateFields = duplicateBy.length ? duplicateBy.join(' and ') : 'contact details';
    const err = new Error(
        `Duplicate lead found for the same ${duplicateFields}. Existing lead: ${duplicateLead.studentName || duplicateLead._id}`
    );
    err.statusCode = 409;
    throw err;
};

// @desc    Get all leads (admin) or assigned leads (counsellor)
// @route   GET /api/leads
const getLeads = asyncHandler(async (req, res) => {
    const { status, counsellor, course, search, page = 1, limit = 10 } = req.query;
    const query = {};

    if (req.user.role === 'counsellor') {
        query.assignedTo = req.user._id;
    }

    if (status) query.status = status;
    if (counsellor && req.user.role === 'admin') query.assignedTo = counsellor;
    if (course) query.interestedCourse = { $regex: course, $options: 'i' };

    if (search) {
        query.$or = [
            { studentName: { $regex: search, $options: 'i' } },
            { mobile: { $regex: search, $options: 'i' } },
            { email: { $regex: search, $options: 'i' } },
            { fatherName: { $regex: search, $options: 'i' } },
        ];
    }

    const total = await Lead.countDocuments(query);
    const leads = await Lead.find(query)
        .populate('assignedTo', 'name email')
        .populate('createdBy', 'name')
        .sort({ createdAt: -1 })
        .skip((page - 1) * limit)
        .limit(Number(limit));

    res.json({
        leads,
        total,
        page: Number(page),
        pages: Math.ceil(total / limit),
    });
});

// @desc    Get single lead
// @route   GET /api/leads/:id
const getLeadById = asyncHandler(async (req, res) => {
    const lead = await Lead.findById(req.params.id)
        .populate('assignedTo', 'name email phone')
        .populate('createdBy', 'name email');

    if (!lead) {
        res.status(404);
        throw new Error('Lead not found');
    }

    // Counsellors can only see their own leads
    if (
        req.user.role === 'counsellor' &&
        lead.assignedTo?._id.toString() !== req.user._id.toString()
    ) {
        res.status(403);
        throw new Error('Not authorized to view this lead');
    }

    res.json(lead);
});

// @desc    Create lead
// @route   POST /api/leads
const createLead = asyncHandler(async (req, res) => {
    await assertNoDuplicateLead({
        mobile: req.body.mobile,
        email: req.body.email,
    });

    const leadData = { ...req.body, createdBy: req.user._id };
    if (leadData.mobile) leadData.mobile = normalizeMobile(leadData.mobile);
    if (leadData.alternateMobile) leadData.alternateMobile = normalizeMobile(leadData.alternateMobile);
    if (leadData.whatsapp) leadData.whatsapp = normalizeMobile(leadData.whatsapp);
    if (leadData.email) leadData.email = normalizeEmail(leadData.email);

    const lead = await Lead.create(leadData);

    // Create history entry
    await LeadHistory.create({
        lead: lead._id,
        action: 'Created',
        performedBy: req.user._id,
        note: `Lead created by ${req.user.name}`,
    });

    if (lead.assignedTo) {
        await LeadHistory.create({
            lead: lead._id,
            action: 'Assigned',
            performedBy: req.user._id,
            newCounsellor: lead.assignedTo,
            note: 'Initial assignment on creation',
        });
    }

    const populated = await Lead.findById(lead._id)
        .populate('assignedTo', 'name email')
        .populate('createdBy', 'name');

    res.status(201).json(populated);
});

// @desc    Update lead
// @route   PUT /api/leads/:id
const updateLead = asyncHandler(async (req, res) => {
    const lead = await Lead.findById(req.params.id);
    if (!lead) {
        res.status(404);
        throw new Error('Lead not found');
    }

    const { assignedTo, status, ...rest } = req.body;

    const nextMobile = rest.mobile !== undefined ? rest.mobile : lead.mobile;
    const nextEmail = rest.email !== undefined ? rest.email : lead.email;

    await assertNoDuplicateLead({
        mobile: nextMobile,
        email: nextEmail,
        excludeLeadId: lead._id,
    });

    if (rest.mobile !== undefined) rest.mobile = normalizeMobile(rest.mobile);
    if (rest.alternateMobile !== undefined) rest.alternateMobile = normalizeMobile(rest.alternateMobile);
    if (rest.whatsapp !== undefined) rest.whatsapp = normalizeMobile(rest.whatsapp);
    if (rest.email !== undefined) rest.email = normalizeEmail(rest.email);

    // Track reassignment
    if (
        assignedTo &&
        lead.assignedTo &&
        assignedTo !== lead.assignedTo.toString()
    ) {
        await LeadHistory.create({
            lead: lead._id,
            action: 'Reassigned',
            performedBy: req.user._id,
            previousCounsellor: lead.assignedTo,
            newCounsellor: assignedTo,
            note: 'Lead reassigned to new counsellor',
        });
    } else if (assignedTo && !lead.assignedTo) {
        await LeadHistory.create({
            lead: lead._id,
            action: 'Assigned',
            performedBy: req.user._id,
            newCounsellor: assignedTo,
            note: 'Lead assigned to counsellor',
        });
    }

    // Track status change
    if (status && status !== lead.status) {
        await LeadHistory.create({
            lead: lead._id,
            action: 'Status Changed',
            performedBy: req.user._id,
            previousStatus: lead.status,
            newStatus: status,
            note: `Status changed from ${lead.status} to ${status}`,
        });
    }

    Object.assign(lead, rest);
    if (assignedTo !== undefined) lead.assignedTo = assignedTo || null;
    if (status) lead.status = status;

    const updated = await lead.save();
    const populated = await Lead.findById(updated._id)
        .populate('assignedTo', 'name email')
        .populate('createdBy', 'name');

    res.json(populated);
});

// @desc    Delete lead
// @route   DELETE /api/leads/:id
const deleteLead = asyncHandler(async (req, res) => {
    const lead = await Lead.findById(req.params.id);
    if (!lead) {
        res.status(404);
        throw new Error('Lead not found');
    }
    await lead.deleteOne();
    res.json({ message: 'Lead deleted' });
});

// @desc    Get follow-up leads
// @route   GET /api/leads/followups
const getFollowUps = asyncHandler(async (req, res) => {
    const query = { followUpDate: { $ne: null } };
    if (req.user.role === 'counsellor') {
        query.assignedTo = req.user._id;
    }

    const leads = await Lead.find(query)
        .populate('assignedTo', 'name email')
        .sort({ followUpDate: 1 });

    res.json(leads);
});

// @desc    Export leads (returns all)
// @route   GET /api/leads/export
const exportLeads = asyncHandler(async (req, res) => {
    const query = {};
    if (req.user.role === 'counsellor') query.assignedTo = req.user._id;

    const leads = await Lead.find(query)
        .populate('assignedTo', 'name')
        .sort({ createdAt: -1 });

    res.json(leads);
});

module.exports = {
    getLeads,
    getLeadById,
    createLead,
    updateLead,
    deleteLead,
    getFollowUps,
    exportLeads,
};
