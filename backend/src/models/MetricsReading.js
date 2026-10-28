const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const MetricsReading = sequelize.define('MetricsReading', {
  id: { type: DataTypes.BIGINT, autoIncrement: true, primaryKey: true },
  metric_type: { type: DataTypes.STRING, allowNull: false },
  value: { type: DataTypes.FLOAT, allowNull: false },
  unit: { type: DataTypes.STRING },
  recorded_at: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { tableName: 'metrics_readings', timestamps: false });

module.exports = MetricsReading;