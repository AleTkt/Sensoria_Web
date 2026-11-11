@extends('layouts.backoffice')

@section('title', 'Dashboard | Staff Sensoría')
@section('header', 'Panel Principal')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <!-- Tarjetas de Estadísticas (Plataforma & Usuarios) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 24px;">

        <!-- Total Empresas -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Empresas</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #0f172a;">{{ $stats['total_companies'] ?? 0 }}</p>
        </div>

        <!-- Empresas Activas -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Empresas Activas</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #16a34a;">{{ $stats['active_companies'] ?? 0 }}</p>
        </div>

        <!-- Usuarios Globales -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Usuarios Globales</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #0f172a;">{{ $stats['total_users'] ?? 0 }}</p>
        </div>

        <!-- Staff -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Miembros Staff</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #dc2626;">{{ $stats['staff_members'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas (Productos & Inventario) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">

        <!-- Total Productos -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Productos</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: var(--color-primary, #589445);">{{ $stats['total_products'] ?? 0 }}</p>
        </div>

        <!-- Productos Activos -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Productos Activos</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #0284c7;">{{ $stats['active_products'] ?? 0 }}</p>
        </div>

        <!-- Stock Global -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Stock Global</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #d97706;">{{ number_format($stats['total_stock'] ?? 0) }}</p>
        </div>

    </div>

    <!-- Sección de Gráfica con Chart.js -->
    <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 32px;">
        <h3 style="margin-top: 0; margin-bottom: 1.25rem; font-size: 1.1rem; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-chart-column" style="color: var(--color-primary, #589445);"></i> Distribución de Productos por Categoría
        </h3>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="productsCategoryChart"></canvas>
        </div>
    </div>

    <!-- Banner de Bienvenida -->
    <div style="background: var(--admin-bg-sidebar); color: white; padding: 32px; border-radius: 8px;">
        <h3 style="margin: 0 0 10px 0; font-size: 1.4rem;">Bienvenido al Backoffice, {{ Auth::user()->name }}</h3>
        <p style="margin: 0; color: #94a3b8; font-size: 1rem; line-height: 1.6;">
            Desde este panel puedes gestionar todas las instancias B2B, auditar usuarios y administrar el catálogo de hardware. Usa el menú lateral para navegar.
        </p>
    </div>
@endsection

@push('scripts')
    <!-- CDN de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('productsCategoryChart').getContext('2d');

            // Datos inyectados de manera segura desde Laravel
            const categories = {!! json_encode($chartCategories ?? []) !!};
            const totals = {!! json_encode($chartTotals ?? []) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categories.length > 0 ? categories : ['Sin Categorías'],
                    datasets: [{
                        label: 'Cantidad de Productos',
                        data: totals.length > 0 ? totals : [0],
                        backgroundColor: 'rgba(88, 148, 69, 0.75)',
                        borderColor: '#589445',
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
