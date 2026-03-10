const asyncHandler = require('express-async-handler');
const User = require('../models/User');

// @desc    Get all users (counsellors)
// @route   GET /api/users
const getUsers = asyncHandler(async (req, res) => {
    const users = await User.find({}).select('-password').sort({ createdAt: -1 });
    res.json(users);
});

// @desc    Get counsellors only
// @route   GET /api/users/counsellors
const getCounsellors = asyncHandler(async (req, res) => {
    const counsellors = await User.find({ role: 'counsellor' })
        .select('-password')
        .sort({ name: 1 });
    res.json(counsellors);
});

// @desc    Create user
// @route   POST /api/users
const createUser = asyncHandler(async (req, res) => {
    const { name, email, password, role, phone } = req.body;

    const exists = await User.findOne({ email });
    if (exists) {
        res.status(400);
        throw new Error('User already exists with this email');
    }

    const user = await User.create({ name, email, password, role, phone });
    res.status(201).json({
        _id: user._id,
        name: user.name,
        email: user.email,
        role: user.role,
        phone: user.phone,
    });
});

// @desc    Update user
// @route   PUT /api/users/:id
const updateUser = asyncHandler(async (req, res) => {
    const user = await User.findById(req.params.id);
    if (!user) {
        res.status(404);
        throw new Error('User not found');
    }

    user.name = req.body.name || user.name;
    user.email = req.body.email || user.email;
    user.role = req.body.role || user.role;
    user.phone = req.body.phone || user.phone;
    user.isActive =
        req.body.isActive !== undefined ? req.body.isActive : user.isActive;

    if (req.body.password) {
        user.password = req.body.password;
    }

    const updated = await user.save();
    res.json({
        _id: updated._id,
        name: updated.name,
        email: updated.email,
        role: updated.role,
        phone: updated.phone,
        isActive: updated.isActive,
    });
});

// @desc    Delete user
// @route   DELETE /api/users/:id
const deleteUser = asyncHandler(async (req, res) => {
    const user = await User.findById(req.params.id);
    if (!user) {
        res.status(404);
        throw new Error('User not found');
    }
    await user.deleteOne();
    res.json({ message: 'User removed' });
});

// @desc    Get single user
// @route   GET /api/users/:id
const getUserById = asyncHandler(async (req, res) => {
    const user = await User.findById(req.params.id).select('-password');
    if (!user) {
        res.status(404);
        throw new Error('User not found');
    }
    res.json(user);
});

module.exports = {
    getUsers,
    getCounsellors,
    createUser,
    updateUser,
    deleteUser,
    getUserById,
};
