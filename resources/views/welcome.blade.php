@extends('layouts.public')

@section('title', 'Sensoría IoT - Llevando la tecnología a tu PYME')

@section('content')
    <!-- Hero Section (Sección Principal) -->
    <div style="text-align: center; padding: 80px 20px;">
        <h1 style="font-size: 3.5rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 24px; line-height: 1.2;">
            Inteligencia <span style="color: var(--color-primary);">IoT</span><br>para tu negocio
        </h1>
        <p style="font-size: 1.25rem; color: #718096; max-width: 600px; margin: 0 auto 40px auto; line-height: 1.6;">
            Automatiza, asegura y monitorea tus operaciones con dispositivos modulares de instalación sencilla (Plug & Play). Sin complicaciones técnicas.
        </p>

        <div style="display: flex; gap: 16px; justify-content: center; align-items: center;">
            <a href="{{ url('/tienda') }}" style="background-color: var(--color-primary); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.1rem; box-shadow: 0 4px 14px rgba(88, 148, 69, 0.3);">
                Explorar Kits
            </a>
            <a href="{{ route('register') }}" style="background-color: transparent; color: var(--color-secondary); border: 2px solid var(--color-secondary); padding: 12px 30px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.1rem;">
                Crear Cuenta
            </a>
        </div>
    </div>

    <!-- Bloque de relleno para probar el Scroll y el efecto del Navbar -->
    <div style="height: 600px; background-color: rgba(250, 250, 247, 0.5); border: 1px solid rgba(27, 61, 26, 0.05); border-radius: 24px; margin-top: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <svg style="width: 64px; height: 64px; color: var(--color-primary); opacity: 0.5; margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        <p style="color: var(--color-tertiary); font-weight: 500; font-size: 1.2rem;">[ Espacio para mostrar los beneficios y catálogo ]</p>
        <p style="color: #718096; margin-top: 8px;">(Haz scroll para ver el efecto cristalino en el menú superior)</p>
    </div>
@endsection
