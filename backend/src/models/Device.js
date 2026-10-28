const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Device = sequelize.define('Device', {
  id: {
    type: DataTypes.UUID,
    defaultValue: DataTypes.UUIDV4,
    primaryKey: true
  },
  type: { type: DataTypes.STRING, allowNull: false },
  alias: { type: DataTypes.STRING },
  status: { type: DataTypes.STRING, defaultValue: 'OFFLINE' },
  last_ping: { type: DataTypes.DATE }
}, {
  tableName: 'devices'
});

module.exports = Device;