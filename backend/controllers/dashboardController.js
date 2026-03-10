const asyncHandler = require('express-async-handler');
const Lead = require('../models/Lead');
const User = require('../models/User');

// @desc    Get dashboard analytics
// @route   GET /api/dashboard
const getDashboard = asyncHandler(async (req, res) => {
    const query = {};
    if (req.user.role === 'counsellor') query.assignedTo = req.user._id;

    const [
        totalLeads,
        newLeads,
        contacted,
        interested,
        admissionDone,
        notInterested,
        pendingFollowUps,
        totalCounsellors,
    ] = await Promise.all([
        Lead.countDocuments(query),
        Lead.countDocuments({ ...query, status: 'New Lead' }),
        Lead.countDocuments({ ...query, status: 'Contacted' }),
        Lead.countDocuments({ ...query, status: 'Interested' }),
        Lead.countDocuments({ ...query, status: 'Admission Done' }),
        Lead.countDocuments({ ...query, status: 'Not Interested' }),
        Lead.countDocuments({
            ...query,
            followUpDate: { $lte: new Date() },
            status: { $nin: ['Admission Done', 'Not Interested'] },
        }),
        req.user.role === 'admin'
            ? User.countDocuments({ role: 'counsellor', isActive: true })
            : Promise.resolve(0),
    ]);

    // Source breakdown
    const sourceBreakdown = await Lead.aggregate([
        { $match: query },
        { $group: { _id: '$leadSource', count: { $sum: 1 } } },
        { $sort: { count: -1 } },
    ]);

    // Status breakdown
    const statusBreakdown = await Lead.aggregate([
        { $match: query },
        { $group: { _id: '$status', count: { $sum: 1 } } },
    ]);

    // Course breakdown
    const courseBreakdown = await Lead.aggregate([
        { $match: { ...query, interestedCourse: { $ne: null, $ne: '' } } },
        { $group: { _id: '$interestedCourse', count: { $sum: 1 } } },
        { $sort: { count: -1 } },
        { $limit: 8 },
    ]);

    // Monthly trend (last 6 months)
    const sixMonthsAgo = new Date();
    sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
    const monthlyTrend = await Lead.aggregate([
        { $match: { ...query, createdAt: { $gte: sixMonthsAgo } } },
        {
            $group: {
                _id: {
                    year: { $year: '$createdAt' },
                    month: { $month: '$createdAt' },
                },
                count: { $sum: 1 },
            },
        },
        { $sort: { '_id.year': 1, '_id.month': 1 } },
    ]);

    // Recent leads
    const recentLeads = await Lead.find(query)
        .populate('assignedTo', 'name')
        .sort({ createdAt: -1 })
        .limit(5);

    res.json({
        stats: {
            totalLeads,
            newLeads,
            contacted,
            interested,
            admissionDone,
            notInterested,
            pendingFollowUps,
            totalCounsellors,
        },
        sourceBreakdown,
        statusBreakdown,
        courseBreakdown,
        monthlyTrend,
        recentLeads,
    });
});

module.exports = { getDashboard };
