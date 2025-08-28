<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.invoice.title', ['number' => $order->order_number]) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: {{ app()->getLocale() === 'ar' ? 'Tahoma, Arial, sans-serif' : 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif' }};
            line-height: 1.6; 
            color: #1f2937; 
            background: #f9fafb;
            padding: 20px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.2);
            font-size: 0.875rem;
            font-weight: 500;
        }
        .content {
            padding: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-section h3 {
            color: #374151;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .info-section p {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        th {
            background: #f3f4f6;
            padding: 12px 16px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }
        .text-right { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; }
        .text-left { text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 600; }
        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-gray-600 { color: #6b7280; }
        .text-green-600 { color: #059669; }
        .text-red-600 { color: #dc2626; }
        tfoot td {
            background: #f9fafb;
            font-weight: 600;
            border-bottom: none;
        }
        .total-row td {
            background: #f0fdf4;
            color: #059669;
            font-size: 1.125rem;
            font-weight: 700;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }
        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>{{ __('app.invoice.title', ['number' => $order->order_number]) }}</h1>
            <div class="status">{{ __('app.status.' . $order->status) }}</div>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-section">
                    <h3>{{ __('app.invoice.details') }}</h3>
                    <p><strong>{{ __('app.invoice.date') }}:</strong> {{ $order->created_at->format('M j, Y g:i A') }}</p>
                    <p><strong>{{ __('app.invoice.payment_method') }}:</strong> {{ __('app.payment.' . $order->payment_method) }}</p>
                    @if($order->coupon)
                        <p><strong>{{ __('app.invoice.coupon') }}:</strong> {{ $order->coupon->code }} (-${{ number_format((float)$order->coupon->discount_amount, 2) }})</p>
                    @endif
                </div>

                <div class="info-section">
                    <h3>{{ __('app.invoice.customer_information') }}</h3>
                    <p><strong>{{ __('app.invoice.customer_name') }}:</strong> {{ $order->user?->name ?? __('app.invoice.not_available') }}</p>
                    <p><strong>{{ __('app.invoice.customer_email') }}:</strong> {{ $order->user?->email ?? __('app.invoice.not_available') }}</p>
                    <p><strong>{{ __('app.invoice.customer_phone') }}:</strong> {{ $order->user?->phone ?? __('app.invoice.not_available') }}</p>
                </div>

                <div class="info-section">
                    <h3>{{ __('app.invoice.shipping_address') }}</h3>
                    <p>{{ $order->address?->full_address ?? __('app.invoice.no_address_provided') }}</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>{{ __('app.invoice.product') }}</th>
                        <th class="text-center">{{ __('app.invoice.sku') }}</th>
                        <th class="text-center">{{ __('app.invoice.quantity') }}</th>
                        <th class="text-right">{{ __('app.invoice.unit_price') }}</th>
                        <th class="text-right">{{ __('app.invoice.discount') }}</th>
                        <th class="text-right">{{ __('app.invoice.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="font-bold">{{ $item->product?->translations->where('locale', app()->getLocale())->first()?->name ?? __('app.invoice.product_not_found') }}</div>
                            </td>
                            <td class="text-center text-sm text-gray-600">{{ $item->product?->sku ?? __('app.invoice.not_available') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">${{ number_format((float)$item->total_price, 2) }}</td>
                            <td class="text-right text-red-600">
                                @if($item->amount_discount_price && $item->amount_discount_price > 0)
                                    -${{ number_format((float)$item->amount_discount_price, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right font-bold text-green-600">${{ number_format((float)$item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right font-bold">{{ __('app.invoice.subtotal') }}</td>
                        <td class="text-right">${{ number_format((float)$order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->total_discount > 0)
                        <tr>
                            <td colspan="5" class="text-right font-bold text-red-600">{{ __('app.invoice.total_discount') }}</td>
                            <td class="text-right text-red-600">-${{ number_format((float)$order->total_discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="5" class="text-right font-bold">{{ __('app.invoice.order_total') }}</td>
                        <td class="text-right font-bold">${{ number_format((float)$order->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            @if($order->payments->isNotEmpty())
                <div style="margin-top: 30px;">
                    <h3 style="color: #374151; font-size: 1rem; font-weight: 600; margin-bottom: 12px;">{{ __('app.invoice.payment_history') }}</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('app.invoice.transaction_id') }}</th>
                                <th>{{ __('app.invoice.method') }}</th>
                                <th>{{ __('app.invoice.amount') }}</th>
                                <th>{{ __('app.invoice.status') }}</th>
                                <th>{{ __('app.invoice.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                                <tr>
                                    <td class="text-sm">{{ $payment->transaction_id }}</td>
                                    <td>{{ __('app.payment.' . $payment->payment_method) }}</td>
                                    <td class="text-right">${{ number_format((float)$payment->amount, 2) }}</td>
                                    <td>
                                        <span style="
                                            display: inline-block;
                                            padding: 2px 8px;
                                            border-radius: 9999px;
                                            font-size: 0.75rem;
                                            font-weight: 500;
                                            background: {{ $payment->status === 'paid' ? '#dcfce7' : ($payment->status === 'pending' ? '#fef3c7' : '#fee2e2') }};
                                            color: {{ $payment->status === 'paid' ? '#059669' : ($payment->status === 'pending' ? '#d97706' : '#dc2626') }};
                                        ">
                                            {{ __('app.payment_status.' . $payment->status) }}
                                        </span>
                                    </td>
                                    <td class="text-sm">{{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>{{ __('app.invoice.thank_you') }}</p>
            <p>{{ __('app.invoice.computer_generated') }}</p>
        </div>
    </div>
</body>
</html>
