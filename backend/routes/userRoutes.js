const express = require('express');
const router = express.Router();
const {
    getUsers,
    getCounsellors,
    createUser,
    updateUser,
    deleteUser,
    getUserById,
} = require('../controllers/userController');
const { protect, adminOnly } = require('../middleware/authMiddleware');

router.get('/counsellors', protect, getCounsellors);
router
    .route('/')
    .get(protect, adminOnly, getUsers)
    .post(protect, adminOnly, createUser);
router
    .route('/:id')
    .get(protect, getUserById)
    .put(protect, adminOnly, updateUser)
    .delete(protect, adminOnly, deleteUser);

module.exports = router;
