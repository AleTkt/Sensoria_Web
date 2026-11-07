@extends('layouts.backoffice')

@section('title', 'Dashboard | Staff Sensoría')
@section('header', 'Panel Principal')

@section('content')
    <!-- Tarjetas de Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">

        <!-- Total Empresas -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Empresas</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #0f172a;">{{ $stats['total_companies'] ?? 0 }}</p>
        </div>

        <!-- Empresas Activas -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Empresas Activas</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #16a34a;">{{ $stats['active_companies'] ?? 0 }}</p>
        </div>

        <!-- Usuarios Globales -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Usuarios Globales</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #0f172a;">{{ $stats['total_users'] ?? 0 }}</p>
        </div>

        <!-- Staff -->
        <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <h4 style="margin: 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Miembros Staff</h4>
            <p style="margin: 12px 0 0 0; font-size: 2.2rem; font-weight: 700; color: #dc2626;">{{ $stats['staff_members'] ?? 0 }}</p>
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
