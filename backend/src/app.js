const express = require('express');
const cors = require('cors');
const sequelize = require('./config/database');
require('./models');

const companyRoutes = require('./routes/companyRoutes');
const productRoutes = require('./routes/productRoutes');
const deviceRoutes = require('./routes/deviceRoutes');

const app = express();
app.use(cors());
app.use(express.json());

// Registro de endpoints
app.use('/api/companies', companyRoutes);
app.use('/api/products', productRoutes);
app.use('/api/devices', deviceRoutes);

const PORT = process.env.PORT || 3000;

sequelize.sync({ force: false }).then(() => {
  console.log('Base de datos sincronizada correctamente.');
  app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
  });
}).catch(err => console.error('Error de base de datos:', err));