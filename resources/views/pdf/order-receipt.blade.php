<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Compra - Sensoría IoT</title>
    <style>
        @page {
            margin: 30px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #374151; /* Gris oscuro */
            background-color: #FAFAF7; /* Fondo Sensoría */
            margin: 0;
            padding: 0;
        }

        /* Encabezado */
        .header {
            width: 100%;
            border-bottom: 2px solid #1B3D1A;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #1B3D1A;
            margin: 0;
            letter-spacing: 1px;
        }
        .brand-title span {
            color: #589445;
        }
        .subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #6b7280;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .meta-info {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #4b5563;
            line-height: 1.5;
        }

        /* Detalles de la Orden */
        .order-details {
            width: 100%;
            margin-bottom: 25px;
        }
        .order-details td {
            vertical-align: top;
        }
        .info-box {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 6px;
        }

        /* Tabla de Productos */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background-color: #ffffff;
        }
        .table th {
            background-color: #1B3D1A;
            color: #FAFAF7;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            color: #1B3D1A;
            font-size: 14px;
            background-color: rgba(88, 148, 69, 0.1);
        }

        /* Código de Activación */
        .activation-box {
            text-align: center;
            background-color: #1B3D1A;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .activation-code {
            font-size: 22px;
            font-weight: bold;
            color: #589445;
            letter-spacing: 2px;
            margin-top: 10px;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .clear { clear: both; }
    </style>
</head>
<body>

<div class="header">
    <div class="meta-info">
        <strong>FECHA:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}<br>
        <strong>ORDEN:</strong> {{ $order->order_number }}
    </div>
    <h1 class="brand-title">Sensoría <span>IoT</span></h1>
    <div class="subtitle">Comprobante de Compra y Código de Activación</div>
    <div class="clear"></div>
</div>

<table class="order-details">
    <tr>
        <td style="width: 50%; padding-right: 10px;">
            <div class="info-box">
                <strong style="color: #1B3D1A;">Datos de la Empresa</strong><br><br>
                Nombre: {{ $company->trade_name }}<br>
                Razón Social: {{ $company->business_name ?? 'N/A' }}<br>
                NIT: {{ $company->nit ?? 'N/A' }}
            </div>
        </td>
        <td style="width: 50%; padding-left: 10px;">
            <div class="info-box">
                <strong style="color: #1B3D1A;">Resumen de Pago</strong><br><br>
                Estado: <span style="color: #589445; font-weight: bold;">{{ $order->status }}</span><br>
                Método: Tarjeta terminada en {{ $order->paymentMethod->last_four ?? '****' }}<br>
                Total Pagado: ${{ number_format($order->total_amount, 2) }}
            </div>
        </td>
    </tr>
</table>

<table class="table">
    <thead>
    <tr>
        <th style="width: 50%;">Descripción del Producto / Solución</th>
        <th style="width: 15%; text-align: center;">Cantidad</th>
        <th style="width: 15%; text-align: right;">Precio Unit.</th>
        <th style="width: 20%; text-align: right;">Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>
                <strong>{{ $item->product->name ?? $item->kit->name ?? 'Solución IoT' }}</strong>
            </td>
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
            <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
        </tr>
    @endforeach
    <tr class="total-row">
        <td colspan="3" class="text-right" style="padding-top: 15px; padding-bottom: 15px;">TOTAL PAGADO:</td>
        <td class="text-right" style="padding-top: 15px; padding-bottom: 15px;">${{ number_format($order->total_amount, 2) }}</td>
    </tr>
    </tbody>
</table>

<div class="activation-box">
    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Tu Código de Activación de Hardware</div>
    <div class="activation-code">{{ $activationCode->code }}</div>
    <div style="font-size: 11px; margin-top: 10px; color: #d1d5db;">Ingresa este código en tu panel de cliente para enlazar los dispositivos a tu Hub.</div>
</div>

<div class="footer">
    Sensoría IoT &copy; {{ date('Y') }} — Documento generado automáticamente. Este comprobante no es una factura fiscal de crédito.
</div>

</body>
</html>
