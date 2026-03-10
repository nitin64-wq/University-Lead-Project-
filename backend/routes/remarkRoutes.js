const express = require('express');
const router = express.Router();
const { getRemarks, addRemark } = require('../controllers/remarkController');
const { protect } = require('../middleware/authMiddleware');

router.route('/:leadId').get(protect, getRemarks).post(protect, addRemark);

module.exports = router;
