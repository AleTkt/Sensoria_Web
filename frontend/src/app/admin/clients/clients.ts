import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CompanyService, Company } from '../../core/services/company';

@Component({
  selector: 'app-clients',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './clients.html',
  styleUrls: ['./clients.css']
})
export class Clients implements OnInit {
  companies: Company[] = [];
  isLoading = true;

  constructor(private companyService: CompanyService) {}

  ngOnInit(): void {
    // Simulamos un pequeño retraso para apreciar el estado de carga del diseño
    setTimeout(() => {
      this.companyService.getCompanies().subscribe({
        next: (data) => {
          this.companies = data;
          this.isLoading = false;
        },
        error: (err) => {
          console.error('Error al cargar clientes', err);
          this.isLoading = false;
        }
      });
    }, 800);
  }
}