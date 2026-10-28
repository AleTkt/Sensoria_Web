const { Device, MetricsReading, Product } = require('../models');

exports.getCompanyDevices = async (req, res) => {
  try {
    const { companyId } = req.params;
    const devices = await Device.findAll({
      where: { company_id: companyId },
      include: [
        { model: Product, as: 'product_model', attributes: ['name', 'sku'] }
      ]
    });
    res.status(200).json(devices);
  } catch (error) {
    res.status(500).json({ message: 'Error al obtener dispositivos', error });
  }
};

// Endpoint simulado para que un sensor envíe datos
exports.receiveMetric = async (req, res) => {
  try {
    const { device_id, metric_type, value, unit } = req.body;
    const reading = await MetricsReading.create({ device_id, metric_type, value, unit });
    res.status(201).json(reading);
  } catch (error) {
    res.status(500).json({ message: 'Error al guardar métrica', error });
  }
};