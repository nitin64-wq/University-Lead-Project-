const mongoose = require('mongoose');

const leadHistorySchema = new mongoose.Schema(
    {
        lead: { type: mongoose.Schema.Types.ObjectId, ref: 'Lead', required: true },
        action: {
            type: String,
            enum: [
                'Created',
                'Assigned',
                'Reassigned',
                'Status Changed',
                'Remark Added',
                'Updated',
            ],
            required: true,
        },
        performedBy: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
        previousCounsellor: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
        newCounsellor: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
        previousStatus: { type: String },
        newStatus: { type: String },
        note: { type: String },
    },
    { timestamps: true }
);

module.exports = mongoose.model('LeadHistory', leadHistorySchema);
