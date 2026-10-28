import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-public-layout',
  standalone: true, // Si estamos usando Angular v15+ Standalone
  imports: [RouterModule],
  templateUrl: './public-layout.html',
  styleUrls: ['./public-layout.css']
})
export class PublicLayout {
  // Lógica de estado para menús móviles o control de scroll iría aquí
}