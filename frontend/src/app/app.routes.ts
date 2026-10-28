import { Routes } from '@angular/router';

// Importamos los layouts (asegúrate de que las rutas coincidan con tu estructura)
import { PublicLayout } from './shared/layouts/public-layout/public-layout';
import { AdminLayout } from './shared/layouts/admin-layout/admin-layout';

export const routes: Routes = [
  {
    // ZONA PÚBLICA: Usa el Public Layout
    path: '',
    component: PublicLayout,
    children: [
      {
        path: '',
        loadChildren: () => import('./public/public-module').then(m => m.PublicModule)
      }
    ]
  },
  {
    // ZONA PRIVADA (CRM): Usa el Admin Layout
    path: 'admin',
    component: AdminLayout,
    children: [
      {
        path: '',
        loadChildren: () => import('./admin/admin-module').then(m => m.AdminModule)
      }
    ]
  },
  {
    // Ruta comodín
    path: '**',
    redirectTo: ''
  }
];