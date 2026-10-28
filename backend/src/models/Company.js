const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Company = sequelize.define('Company', {
  id: {
    type: DataTypes.UUID,
    defaultValue: DataTypes.UUIDV4,
    primaryKey: true
  },
  name: { type: DataTypes.STRING, allowNull: false },
  industry: { type: DataTypes.STRING },
  contact_email: { type: DataTypes.STRING }
}, {
  tableName: 'companies'
});

module.exports = Company;