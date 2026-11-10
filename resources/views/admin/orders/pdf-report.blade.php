<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Global de Pedidos - Sensoría IoT</title>
    <style>
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #374151;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #1B3D1A;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #1B3D1A;
            margin: 0;
        }
        .brand-title span {
            color: #589445;
        }
        .subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #6b7280;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .meta-info {
            float: right;
            text-align: right;
            font-size: 9px;
            color: #4b5563;
            line-height: 1.4;
        }
        .filters-box {
            background-color: #f9faf7;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 9px;
            color: #4b5563;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #1B3D1A;
            color: #FAFAF7;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            font-size: 9px;
            text-transform: uppercase;
        }
        .table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pagado { background-color: #d1fae5; color: #065f46; }
        .badge-pendiente { background-color: #fef3c7; color: #92400e; }
        .badge-cancelado { background-color: #fee2e2; color: #991b1b; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        .clear { clear: both; }
    </style>
</head>
<body>

<div class="header">
    <div class="meta-info">
        <strong>FECHA DE EMISIÓN:</strong> {{ date('d/m/Y H:i') }}<br>
        <strong>GENERADO POR:</strong> {{ Auth::user()->name ?? 'Staff' }}
    </div>
    <h1 class="brand-title">Sensoría <span>IoT</span></h1>
    <div class="subtitle">Reporte Global de Pedidos y Transacciones B2B</div>
    <div class="clear"></div>
</div>

@if(!empty($filtersText))
    <div class="filters-box">
        <strong>Filtros aplicados:</strong> {{ $filtersText }}
    </div>
@endif

<table class="table">
    <thead>
    <tr>
        <th style="width: 18%;"># Orden</th>
        <th style="width: 25%;">Empresa (PYME)</th>
        <th style="width: 17%;">Fecha</th>
        <th style="width: 15%; text-align: right;">Total ($)</th>
        <th style="width: 12%; text-align: center;">Estado</th>
        <th style="width: 13%; text-align: center;">Código</th>
    </tr>
    </thead>
    <tbody>
    @php $sumTotal = 0; @endphp
    @foreach($orders as $order)
        @php
            if(strtoupper($order->status) === 'PAGADO') {
                $sumTotal += $order->total_amount;
            }
        @endphp
        <tr>
            <td><strong>{{ $order->order_number }}</strong></td>
            <td>{{ $order->company->trade_name ?? 'N/A' }}</td>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
            <td class="text-right">${{ number_format($order->total_amount, 2) }}</td>
            <td class="text-center">
                <span class="badge badge-{{ strtolower($order->status) }}">{{ $order->status }}</span>
            </td>
            <td class="text-center" style="font-family: monospace; font-size: 9px;">
                {{ $order->activationCode->code ?? 'N/A' }}
            </td>
        </tr>
    @endforeach
    <tr style="background-color: #f3f4f6; font-weight: bold;">
        <td colspan="3" class="text-right" style="padding: 10px 6px; color: #1B3D1A;">TOTAL INGRESOS (PAGADOS EN REPORTE):</td>
        <td class="text-right" style="padding: 10px 6px; color: #1B3D1A; font-size: 11px;">${{ number_format($sumTotal, 2) }}</td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>

<div class="footer">
    Sensoría IoT Backoffice &copy; {{ date('Y') }} — Reporte confidencial de auditoría y control de operaciones.
</div>

</body>
</html>
