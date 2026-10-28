const express = require('express');
const router = express.Router();
const deviceController = require('../controllers/deviceController');

router.get('/company/:companyId', deviceController.getCompanyDevices);
router.post('/metric', deviceController.receiveMetric);

module.exports = router;