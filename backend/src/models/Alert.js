const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Alert = sequelize.define('Alert', {
  id: { type: DataTypes.UUID, defaultValue: DataTypes.UUIDV4, primaryKey: true },
  message: { type: DataTypes.TEXT, allowNull: false },
  is_resolved: { type: DataTypes.BOOLEAN, defaultValue: false }
}, { tableName: 'alerts', timestamps: true });

module.exports = Alert;