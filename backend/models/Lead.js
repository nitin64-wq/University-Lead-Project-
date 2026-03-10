const mongoose = require('mongoose');

const leadSchema = new mongoose.Schema(
    {
        // Basic Student Details
        studentName: { type: String, required: true, trim: true },
        fatherName: { type: String, trim: true },
        motherName: { type: String, trim: true },
        gender: { type: String, enum: ['Male', 'Female', 'Other'] },
        dateOfBirth: { type: Date },
        category: { type: String, enum: ['General', 'SC', 'ST', 'OBC', 'EWS'] },
        nationality: { type: String, default: 'Indian' },
        city: { type: String },
        state: { type: String },
        country: { type: String, default: 'India' },
        pinCode: { type: String },

        // Contact Details
        mobile: { type: String, required: true },
        alternateMobile: { type: String },
        email: { type: String, lowercase: true },
        whatsapp: { type: String },

        // Academic Details
        tenthMarks: { type: String },
        twelfthMarks: { type: String },
        graduationMarks: { type: String },
        stream: {
            type: String,
            enum: ['Science', 'Commerce', 'Arts', 'Computer', 'Other'],
        },
        schoolCollegeName: { type: String },
        passingYear: { type: String },

        // Course Interest
        interestedCourse: { type: String },
        specialization: { type: String },
        preferredCampus: { type: String },
        mode: { type: String, enum: ['Regular', 'Distance', 'Online'] },

        // Lead Source
        leadSource: {
            type: String,
            enum: [
                'Website',
                'Facebook',
                'Instagram',
                'Google Ads',
                'Walk-in',
                'Referral',
                'Education Fair',
                'Call Enquiry',
                'Other',
            ],
        },

        // Counselling
        assignedTo: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
        status: {
            type: String,
            enum: [
                'New Lead',
                'Contacted',
                'Interested',
                'Not Interested',
                'Admission Done',
            ],
            default: 'New Lead',
        },
        followUpDate: { type: Date },
        createdBy: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
    },
    { timestamps: true }
);

// Text index for search
leadSchema.index({
    studentName: 'text',
    mobile: 'text',
    email: 'text',
    interestedCourse: 'text',
});

module.exports = mongoose.model('Lead', leadSchema);
