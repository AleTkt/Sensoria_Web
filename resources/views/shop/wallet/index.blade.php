@extends('layouts.public')

@section('title', 'Billetera y Pagos | Sensoría')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/shop/product-detail.css') }}">
    <style>
        /* Estilos del Modal de Tarjetas */
        .wallet-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .wallet-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .wallet-modal {
            background-color: var(--color-neutral, #FAFAF7);
            border: 1px solid rgba(88, 148, 69, 0.3);
            border-radius: 16px;
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(27, 61, 26, 0.15);
            z-index: 9999;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .wallet-modal-overlay.active .wallet-modal {
            transform: translateY(0);
        }
        .wallet-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            background: #ffffff;
            color: #1f2937;
            box-sizing: border-box;
            margin-top: 0.35rem;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
        }
        .wallet-input:focus {
            outline: none;
            border-color: var(--color-primary, #589445);
            box-shadow: 0 0 0 3px rgba(88, 148, 69, 0.15);
        }
        /* Estilos para campos con error */
        .wallet-input.is-invalid {
            border-color: #e53e3e;
            background-color: #fff5f5;
        }
    </style>
@endpush

@section('content')
    <div class="shop-page-wrapper">
        <div class="shop-container" style="max-width: 1100px; margin: 0 auto;">

            <a href="{{ route('shop.products.index') }}" class="back-link">
                ← Volver a la Tienda
            </a>

            <div class="shop-header" style="margin-bottom: 2rem;">
                <h1 class="page-title">Mi Billetera & Historial de Pagos</h1>
                <p class="page-subtitle">Gestione sus métodos de pago y supervise las transacciones de sus kits IoT en Sensoría.</p>
            </div>

            @if(session('success'))
                <div style="background-color: rgba(88, 148, 69, 0.15); color: var(--color-tertiary); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- SECCIÓN: MÉTODOS DE PAGO -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; color: var(--color-tertiary); font-weight: 700; margin: 0;">
                    <i class="fa-solid fa-credit-card"></i> Métodos de Pago Registrados
                </h2>
                <!-- Botón conectado al Modal -->
                <button type="button" class="btn-sensoria" onclick="togglePaymentModal()" style="background-color: var(--color-tertiary);">
                    <i class="fa-solid fa-plus"></i> Agregar Método de Pago
                </button>
            </div>

            @if(isset($paymentMethods) && $paymentMethods->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
                    @foreach($paymentMethods as $method)
                        <div class="product-card glass-surface" style="position: relative; border: 1px solid rgba(88, 148, 69, 0.2);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                                <div>
                                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #6b7280; letter-spacing: 0.05em;">{{ $method->brand ?? 'Tarjeta' }}</span>
                                    <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0.2rem 0 0 0;">
                                        •••• •••• •••• {{ $method->last_4_digits }}
                                    </h4>
                                </div>
                                <i class="fa-brands fa-cc-{{ strtolower($method->brand ?? 'visa') }}" style="font-size: 2rem; color: var(--color-primary);"></i>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #4b5563; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1rem;">
                                <div>
                                    <span>Titular: <strong>{{ $method->cardholder_name }}</strong></span>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.2rem;">Exp: {{ str_pad($method->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $method->exp_year }}</div>
                                </div>
                                <div>
                                    @if($method->is_default)
                                        <span style="background-color: var(--color-primary); color: white; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">Predeterminada</span>
                                    @else
                                        <!-- Formulario con soporte para loading-buttons.js -->
                                        <form action="{{ route('wallet.payment.default', $method->id) }}" method="POST" class="form-with-loading" style="margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-submit-loading" style="background: none; border: none; color: var(--color-secondary); font-size: 0.75rem; font-weight: 600; cursor: pointer; text-decoration: underline;" title="Establecer como predeterminada">
                                                Hacer principal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Estado Vacío -->
                <div class="product-card glass-surface" style="padding: 2.5rem 2rem; text-align: center; margin-bottom: 3.5rem;">
                    <i class="fa-regular fa-credit-card" style="font-size: 2.5rem; color: var(--color-tertiary); opacity: 0.3; margin-bottom: 0.75rem; display: block;"></i>
                    <h3 class="card-title" style="font-size: 1.1rem;">No tiene métodos de pago registrados</h3>
                    <p class="card-desc" style="max-width: 350px; margin: 0 auto 1rem auto; font-size: 0.875rem;">
                        Agregue una tarjeta simulada para agilizar sus compras de kits IoT y servicios en Sensoría.
                    </p>
                    <button type="button" class="btn-sensoria" onclick="togglePaymentModal()" style="display: inline-flex;">
                        <i class="fa-solid fa-plus"></i> Registrar Tarjeta Ahora
                    </button>
                </div>
            @endif

            <!-- SECCIÓN: HISTORIAL DE TRANSACCIONES (REDISEÑADO EN FORMATO TABLA PROFESIONAL) -->
            <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(27, 61, 26, 0.06); border: 1px solid rgba(88, 148, 69, 0.2); padding: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--color-primary); padding-bottom: 0.75rem;">
                    <i class="fa-solid fa-receipt" style="font-size: 1.4rem; color: var(--color-primary);"></i>
                    <h2 style="font-size: 1.4rem; color: var(--color-tertiary); font-weight: 700; margin: 0;">
                        Historial de Transacciones
                    </h2>
                </div>

                @if(isset($transactions) && $transactions->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: 750px;">
                            <thead>
                            <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb; color: var(--color-primary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th style="padding: 1rem; font-weight: 700;"># Orden</th>
                                <th style="padding: 1rem; font-weight: 700;">Fecha y Hora</th>
                                <th style="padding: 1rem; font-weight: 700;">Método de Pago Usado</th>
                                <th style="padding: 1rem; font-weight: 700; text-align: right;">Total</th>
                                <th style="padding: 1rem; font-weight: 700; text-align: center;">Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $tx)
                                <tr style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#fafaf9'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 1.1rem 1rem; font-weight: 700; color: var(--color-tertiary);">
                                        {{ $tx->order_number }}
                                    </td>
                                    <td style="padding: 1.1rem 1rem; color: #4b5563; font-size: 0.9rem;">
                                        {{ \Carbon\Carbon::parse($tx->paid_at ?? $tx->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 1.1rem 1rem; color: #374151; font-size: 0.9rem;">
                                        @if($tx->paymentMethod)
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fa-solid fa-credit-card" style="color: var(--color-primary);"></i>
                                                <span><strong>{{ ucfirst($tx->paymentMethod->brand) }}</strong> •••• {{ $tx->paymentMethod->last_4_digits }}</span>
                                            </div>
                                        @else
                                            <span style="color: #9ca3af; font-style: italic;">No especificado</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1.1rem 1rem; font-weight: 800; color: var(--color-tertiary); text-align: right; font-size: 1.05rem;">
                                        ${{ number_format($tx->total_amount, 2) }}
                                    </td>
                                    <td style="padding: 1.1rem 1rem; text-align: center;">
                                            <span style="background-color: #d1fae5; color: #065f46; padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                                {{ $tx->status }}
                                            </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Estado Vacío de Transacciones -->
                    <div style="text-align: center; padding: 3rem 1rem; color: #6b7280;">
                        <i class="fa-solid fa-wallet" style="font-size: 3rem; color: var(--color-tertiary); opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 0.5rem;">No hay transacciones registradas</h3>
                        <p style="font-size: 0.9rem; max-width: 380px; margin: 0 auto 1.5rem auto;">
                            Cuando adquiera kits modulares o servicios de soporte en Sensoría, el detalle de sus pagos figurará en este espacio.
                        </p>
                        <a href="{{ route('shop.products.index') }}" class="btn-sensoria" style="display: inline-flex;">
                            Explorar Catálogo IoT
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL FLOTANTE PARA AGREGAR TARJETA -->

@endsection

@push('modals')
    <div class="wallet-modal-overlay" id="paymentModalOverlay">
        <div class="wallet-modal">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-tertiary); font-weight: 800;">
                    <i class="fa-solid fa-credit-card"></i> Agregar Método de Pago
                </h3>
                <button type="button" onclick="togglePaymentModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; line-height: 1;">&times;</button>
            </div>

            <!-- Formulario conectado a la ruta POST, novalidate y con el efecto Loading -->
            <form action="{{ route('wallet.payment.store') }}" method="POST" class="form-with-loading" novalidate>
                @csrf
                <div>
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--color-tertiary);">Nombre del Titular</label>
                    <input type="text" name="cardholder_name" class="wallet-input @error('cardholder_name') is-invalid @enderror" placeholder="Ej. Rodrigo Maldonado" value="{{ old('cardholder_name') }}" required>
                    @error('cardholder_name')
                    <span style="color: #e53e3e; font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--color-tertiary);">Número de Tarjeta (Ficticia)</label>
                    <input type="text" name="card_number" class="wallet-input @error('card_number') is-invalid @enderror" placeholder="4532 •••• •••• ••••" maxlength="19" value="{{ old('card_number') }}" required>
                    @error('card_number')
                    <span style="color: #e53e3e; font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--color-tertiary);">Mes Expiración</label>
                        <input type="text" name="exp_month" class="wallet-input @error('exp_month') is-invalid @enderror" placeholder="MM (ej. 12)" maxlength="2" value="{{ old('exp_month') }}" required>
                        @error('exp_month')
                        <span style="color: #e53e3e; font-size: 0.75rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--color-tertiary);">Año Expiración</label>
                        <input type="text" name="exp_year" class="wallet-input @error('exp_year') is-invalid @enderror" placeholder="AAAA (ej. 2028)" maxlength="4" value="{{ old('exp_year') }}" required>
                        @error('exp_year')
                        <span style="color: #e53e3e; font-size: 0.75rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                    <button type="button" class="btn-sensoria" onclick="togglePaymentModal()" style="background: transparent; color: #4b5563; border: 1px solid #d1d5db;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-sensoria btn-submit-loading">
                        <i class="fa-solid fa-check"></i> Guardar Tarjeta
                    </button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // Lógica para abrir y cerrar el Modal
        function togglePaymentModal() {
            const overlay = document.getElementById('paymentModalOverlay');
            overlay.classList.toggle('active');
            if (overlay.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Reabrir el modal automáticamente si hay errores de validación del FormRequest
        @if ($errors->any())
        document.addEventListener("DOMContentLoaded", function() {
            togglePaymentModal();
        });
        @endif
    </script>
@endpush
