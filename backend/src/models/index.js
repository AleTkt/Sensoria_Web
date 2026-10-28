const Category = require('./Category');
const Product = require('./Product');
const Company = require('./Company');
const User = require('./User');
const Device = require('./Device');
const MetricsReading = require('./MetricsReading');
const Alert = require('./Alert');

// Relaciones E-commerce
Category.hasMany(Product, { foreignKey: 'category_id', as: 'products' });
Product.belongsTo(Category, { foreignKey: 'category_id', as: 'category' });

// Relaciones CRM
Company.hasMany(User, { foreignKey: 'company_id', as: 'users' });
User.belongsTo(Company, { foreignKey: 'company_id', as: 'company' });

Company.hasMany(Device, { foreignKey: 'company_id', as: 'devices' });
Device.belongsTo(Company, { foreignKey: 'company_id', as: 'company' });

// Hardware físico al producto del catálogo
Product.hasMany(Device, { foreignKey: 'product_id', as: 'sold_devices' });
Device.belongsTo(Product, { foreignKey: 'product_id', as: 'product_model' });

// Relaciones IoT / Métricas
Device.hasMany(MetricsReading, { foreignKey: 'device_id', as: 'metrics' });
MetricsReading.belongsTo(Device, { foreignKey: 'device_id', as: 'device' });

Device.hasMany(Alert, { foreignKey: 'device_id', as: 'alerts' });
Alert.belongsTo(Device, { foreignKey: 'device_id', as: 'device' });

Company.hasMany(Alert, { foreignKey: 'company_id', as: 'company_alerts' });
Alert.belongsTo(Company, { foreignKey: 'company_id', as: 'company' });

module.exports = {
  Category, Product, Company, User, Device, MetricsReading, Alert
};