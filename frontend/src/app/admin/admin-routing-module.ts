import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { Dashboard } from './dashboard/dashboard';
import { Clients } from './clients/clients';

const routes: Routes = [
  { path: '', component: Dashboard }, // tusitio.com/admin/
  { path: 'clientes', component: Clients } // tusitio.com/admin/clientes
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }