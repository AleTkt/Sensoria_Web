import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { Home } from './home/home';
import { Catalog } from './catalog/catalog';

const routes: Routes = [
  { path: '', component: Home }, // tusitio.com/
  { path: 'tienda', component: Catalog } // tusitio.com/tienda
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PublicRoutingModule { }