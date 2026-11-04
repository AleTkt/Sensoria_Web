@extends('layouts.public')

@section('title', 'Sensoría IoT - Plataforma de Monitoreo Inteligente para tu Empresa')

@section('content')
    <div style="max-width: 1200px; margin: 0 auto;">

        <!-- Hero Section -->
        <section style="text-align: center; padding: 60px 20px 80px 20px;">
        <span style="color: var(--color-primary); font-weight: 700; font-size: 0.875rem; letter-spacing: 1.5px; text-transform: uppercase;">
            Plataforma IoT & B2B
        </span>
            <h1 style="font-size: 3.25rem; font-weight: 800; color: var(--color-tertiary); margin: 16px 0 24px 0; line-height: 1.2;">
                Monitoreo inteligente para tu empresa
            </h1>
            <p style="max-width: 650px; margin: 0 auto 36px auto; color: #718096; font-size: 1.15rem; line-height: 1.6;">
                Diseñamos e implementamos kits modulares de sensores para garantizar el control en tiempo real de tus operaciones, instalaciones y activos corporativos.
            </p>

            <div style="display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('register') }}" style="background-color: var(--color-primary); color: white; padding: 14px 32px; text-decoration: none; border-radius: var(--radius-button); font-weight: 600; box-shadow: var(--shadow-soft); transition: transform 0.2s ease;">
                    Comenzar Ahora
                </a>
                <a href="#caracteristicas" style="background-color: transparent; color: var(--color-tertiary); padding: 14px 32px; text-decoration: none; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); font-weight: 600;">
                    Conocer Más
                </a>
            </div>
        </section>

        <!-- Sección de Valor Destacado / Métricas -->
        <section id="caracteristicas" style="margin-bottom: 80px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

                <div class="glass-surface" style="padding: 32px; text-align: left;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(88, 148, 69, 0.1); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem; margin-bottom: 20px;">
                        01
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 12px;">
                        Sensores Modulares
                    </h3>
                    <p style="color: #718096; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Kits de Hardware listos para instalar y adaptar según las necesidades de monitoreo ambiental, energía o seguridad de tu PYME.
                    </p>
                </div>

                <div class="glass-surface" style="padding: 32px; text-align: left;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(88, 148, 69, 0.1); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem; margin-bottom: 20px;">
                        02
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 12px;">
                        Plataforma Multi-tenant
                    </h3>
                    <p style="color: #718096; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Gestiona múltiples empresas, sucursales y permisos de equipo centralizadamente bajo una arquitectura robusta y aislada.
                    </p>
                </div>

                <div class="glass-surface" style="padding: 32px; text-align: left;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(88, 148, 69, 0.1); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem; margin-bottom: 20px;">
                        03
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 12px;">
                        Reportes & Control
                    </h3>
                    <p style="color: #718096; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Obtén métricas claras en tiempo real, alertas instantáneas y análisis de datos enfocados en la productividad de tu negocio.
                    </p>
                </div>

            </div>
        </section>

        <!-- Banner Call-to-Action Inferior -->
        <section class="glass-surface" style="padding: 48px; text-align: center; margin-bottom: 60px; background: linear-gradient(135deg, rgba(88, 148, 69, 0.08), rgba(27, 61, 26, 0.04));">
            <h2 style="font-size: 2rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 16px;">
                ¿Listo para digitalizar la infraestructura de tu empresa?
            </h2>
            <p style="color: #718096; max-width: 550px; margin: 0 auto 28px auto; font-size: 1rem; line-height: 1.5;">
                Crea tu cuenta de organización en pocos pasos y comienza a explorar las soluciones de Sensoría.
            </p>
            <a href="{{ route('register') }}" style="background-color: var(--color-primary); color: white; padding: 12px 28px; text-decoration: none; border-radius: var(--radius-button); font-weight: 600; display: inline-block;">
                Registrar mi Empresa
            </a>
        </section>

    </div>
@endsection
