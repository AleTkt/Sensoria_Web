const { Company, Device } = require('../models');

// Crear una nueva empresa (PYME)
exports.createCompany = async (req, res) => {
  try {
    const { name, industry, contact_email } = req.body;
    const newCompany = await Company.create({ name, industry, contact_email });
    res.status(201).json({ message: 'Empresa creada con éxito', data: newCompany });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al crear la empresa', error });
  }
};

// Obtener todas las empresas con sus dispositivos IoT
exports.getCompanies = async (req, res) => {
  try {
    const companies = await Company.findAll({
      include: [{ model: Device, as: 'devices' }]
    });
    res.status(200).json(companies);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al obtener las empresas', error });
  }
};