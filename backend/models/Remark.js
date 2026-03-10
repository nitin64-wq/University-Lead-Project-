const mongoose = require('mongoose');

const remarkSchema = new mongoose.Schema(
    {
        lead: { type: mongoose.Schema.Types.ObjectId, ref: 'Lead', required: true },
        counsellor: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'User',
            required: true,
        },
        remark: { type: String, required: true },
        followUpDate: { type: Date },
        statusAtTime: { type: String },
    },
    { timestamps: true }
);

module.exports = mongoose.model('Remark', remarkSchema);
