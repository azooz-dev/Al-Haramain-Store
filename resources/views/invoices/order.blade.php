<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ __('app.invoice.title', ['number' => $order->order_number]) }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @if(app()->getLocale() === 'ar')
  <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  @else
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @endif
  <style>
    /* ===== CSS Variables ===== */
    :root {
      --primary: #f59e0b;
      --primary-dark: #d97706;
      --secondary: #ea580c;
      --gradient-start: #f59e0b;
      --gradient-end: #ea580c;
      --success: #059669;
      --success-light: #d1fae5;
      --danger: #dc2626;
      --danger-light: #fee2e2;
      --warning: #d97706;
      --warning-light: #fef3c7;
      --background: #f9fafb;
      --card: #ffffff;
      --text-primary: #1f2937;
      --text-secondary: #374151;
      --text-muted: #6b7280;
      --border: #e5e7eb;
      --radius: 12px;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* ===== Reset & Base ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: {
          {
          app()->getLocale()==='ar'? "'Noto Kufi Arabic', Tahoma, Arial, sans-serif": "'Inter', system-ui, -apple-system, sans-serif"
        }
      }

      ;
      line-height: 1.6;
      color: var(--text-primary);
      background: var(--background);
      padding: 24px;

      direction: {
          {
          app()->getLocale()==='ar'? 'rtl': 'ltr'
        }
      }

      ;
    }

    /* ===== Invoice Container ===== */
    .invoice-container {
      max-width: 850px;
      margin: 0 auto;
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }

    /* ===== Header ===== */
    .header {
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      color: white;
      padding: 32px;
      position: relative;
      overflow: hidden;
    }

    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M30 30l15-15v30l-15-15z'/%3E%3Cpath d='M30 30l-15-15v30l15-15z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
    }

    .header-content {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-icon {
      width: 56px;
      height: 56px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: 700;
    }

    .logo-text {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .invoice-meta {
      text-align: {
          {
          app()->getLocale()==='ar'? 'left': 'right'
        }
      }

      ;
    }

    .invoice-title {
      font-size: 14px;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }

    .invoice-number {
      font-size: 20px;
      font-weight: 700;
    }

    .status-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 9999px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-top: 8px;
    }

    /* ===== Content ===== */
    .content {
      padding: 32px;
    }

    /* ===== Info Grid ===== */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }

    .info-card {
      background: var(--background);
      border-radius: var(--radius);
      padding: 20px;
      border: 1px solid var(--border);
    }

    .info-card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 16px;
    }

    .info-card-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
    }

    .info-card-title {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .info-card p {
      color: var(--text-muted);
      font-size: 14px;
      margin-bottom: 6px;
    }

    .info-card p strong {
      color: var(--text-secondary);
      font-weight: 500;
    }

    /* ===== Table ===== */
    .table-container {
      background: var(--background);
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid var(--border);
      margin-top: 24px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%);
    }

    th {
      padding: 14px 16px;

      text-align: {
          {
          app()->getLocale()==='ar'? 'right': 'left'
        }
      }

      ;
      font-weight: 600;
      color: var(--text-secondary);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid var(--primary);
    }

    td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      color: var(--text-primary);
      font-size: 14px;
    }

    tbody tr:hover {
      background: rgba(245, 158, 11, 0.03);
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    .text-right {
      text-align: {
          {
          app()->getLocale()==='ar'? 'left': 'right'
        }
      }

      ;
    }

    .text-left {
      text-align: {
          {
          app()->getLocale()==='ar'? 'right': 'left'
        }
      }

      ;
    }

    .text-center {
      text-align: center;
    }

    .font-semibold {
      font-weight: 600;
    }

    .font-bold {
      font-weight: 700;
    }

    .text-sm {
      font-size: 13px;
    }

    .text-muted {
      color: var(--text-muted);
    }

    .text-success {
      color: var(--success);
    }

    .text-danger {
      color: var(--danger);
    }

    .text-primary {
      color: var(--primary-dark);
    }

    /* ===== Product Cell ===== */
    .product-name {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 2px;
    }

    .product-sku {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* ===== Totals ===== */
    tfoot {
      background: var(--card);
    }

    tfoot tr {
      border-top: 2px solid var(--border);
    }

    tfoot td {
      padding: 12px 16px;
      border-bottom: 1px solid var(--border);
    }

    .total-row {
      background: linear-gradient(135deg, rgba(5, 150, 105, 0.08) 0%, rgba(5, 150, 105, 0.12) 100%) !important;
    }

    .total-row td {
      padding: 16px;
      font-size: 18px;
      font-weight: 700;
      color: var(--success);
      border-bottom: none;
    }

    /* ===== Status Pills ===== */
    .status-pill {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      text-transform: capitalize;
    }

    .status-paid {
      background: var(--success-light);
      color: var(--success);
    }

    .status-pending {
      background: var(--warning-light);
      color: var(--warning);
    }

    .status-failed {
      background: var(--danger-light);
      color: var(--danger);
    }

    /* ===== Section Title ===== */
    .section-title {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--text-secondary);
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 16px;
      margin-top: 32px;
    }

    .section-title::before {
      content: '';
      width: 4px;
      height: 20px;
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      border-radius: 2px;
    }

    /* ===== Footer ===== */
    .footer {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.03) 0%, rgba(234, 88, 12, 0.06) 100%);
      padding: 24px 32px;
      text-align: center;
      border-top: 1px solid var(--border);
    }

    .footer-logo {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
    }

    .footer-logo-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 14px;
      font-weight: 700;
    }

    .footer-logo-text {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-secondary);
    }

    .footer p {
      color: var(--text-muted);
      font-size: 13px;
      margin: 4px 0;
    }

    .footer-note {
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid var(--border);
      font-size: 11px;
      color: var(--text-muted);
    }

    /* ===== Print Styles ===== */
    @media print {
      body {
        background: white;
        padding: 0;
      }

      .invoice-container {
        box-shadow: none;
        max-width: 100%;
      }

      .header {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      thead {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .status-pill,
      .status-badge {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }

    /* ===== Responsive ===== */
    @media (max-width: 640px) {
      body {
        padding: 12px;
      }

      .header {
        padding: 24px 20px;
      }

      .header-content {
        flex-direction: column;
        text-align: center;
      }

      .invoice-meta {
        text-align: center;
      }

      .content {
        padding: 20px;
      }

      .info-grid {
        grid-template-columns: 1fr;
      }

      table {
        font-size: 13px;
      }

      th,
      td {
        padding: 10px 12px;
      }
    }

  </style>
</head>
<body>
  <div class="invoice-container">
    <!-- Header -->
    <div class="header">
      <div class="header-content">
        <div class="logo-section">
          <div class="logo-icon">ح</div>
          <div class="logo-text">Al-Haramain</div>
        </div>
        <div class="invoice-meta">
          <div class="invoice-title">{{ __('app.invoice.label') }}</div>
          <div class="invoice-number">#{{ $order->order_number }}</div>
          <div class="status-badge">{{ __('app.status.' . $order->status) }}</div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">
      <!-- Info Grid -->
      <div class="info-grid">
        <!-- Invoice Details -->
        <div class="info-card">
          <div class="info-card-header">
            <div class="info-card-icon">📋</div>
            <div class="info-card-title">{{ __('app.invoice.details') }}</div>
          </div>
          <p><strong>{{ __('app.invoice.date') }}:</strong> {{ $order->created_at->format('M j, Y') }}</p>
          <p><strong>{{ __('app.invoice.time') }}:</strong> {{ $order->created_at->format('g:i A') }}</p>
          <p><strong>{{ __('app.invoice.payment_method') }}:</strong> {{ __('app.payment.' . $order->payment_method) }}</p>
          @if($order->coupon)
          <p class="text-primary"><strong>{{ __('app.invoice.coupon') }}:</strong> {{ $order->coupon->code }} (-${{ number_format((float)$order->coupon->discount_amount, 2) }})</p>
          @endif
        </div>

        <!-- Customer Information -->
        <div class="info-card">
          <div class="info-card-header">
            <div class="info-card-icon">👤</div>
            <div class="info-card-title">{{ __('app.invoice.customer_information') }}</div>
          </div>
          <p><strong>{{ __('app.invoice.customer_name') }}:</strong> {{ $order->user?->name ?? __('app.invoice.not_available') }}</p>
          <p><strong>{{ __('app.invoice.customer_email') }}:</strong> {{ $order->user?->email ?? __('app.invoice.not_available') }}</p>
          <p><strong>{{ __('app.invoice.customer_phone') }}:</strong> {{ $order->user?->phone ?? __('app.invoice.not_available') }}</p>
        </div>

        <!-- Shipping Address -->
        <div class="info-card">
          <div class="info-card-header">
            <div class="info-card-icon">📍</div>
            <div class="info-card-title">{{ __('app.invoice.shipping_address') }}</div>
          </div>
          <p>{{ $order->address?->full_address ?? __('app.invoice.no_address_provided') }}</p>
        </div>
      </div>

      <!-- Order Items Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>{{ __('app.invoice.product') }}</th>
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
                <div class="product-name">{{ $item->product?->translations->where('locale', app()->getLocale())->first()?->name ?? __('app.invoice.product_not_found') }}</div>
                <div class="product-sku">SKU: {{ $item->product?->sku ?? __('app.invoice.not_available') }}</div>
              </td>
              <td class="text-center font-semibold">{{ $item->quantity }}</td>
              <td class="text-right">${{ number_format((float)$item->total_price, 2) }}</td>
              <td class="text-right text-danger">
                @if($item->amount_discount_price && $item->amount_discount_price > 0)
                -${{ number_format((float)$item->amount_discount_price, 2) }}
                @else
                —
                @endif
              </td>
              <td class="text-right font-bold text-success">${{ number_format((float)$item->line_total, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" class="text-right font-semibold">{{ __('app.invoice.subtotal') }}</td>
              <td class="text-right font-semibold">${{ number_format((float)$order->subtotal, 2) }}</td>
            </tr>
            @if($order->total_discount > 0)
            <tr>
              <td colspan="4" class="text-right font-semibold text-danger">{{ __('app.invoice.total_discount') }}</td>
              <td class="text-right font-semibold text-danger">-${{ number_format((float)$order->total_discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
              <td colspan="4" class="text-right font-bold">{{ __('app.invoice.order_total') }}</td>
              <td class="text-right font-bold">${{ number_format((float)$order->total_amount, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Payment History -->
      @if($order->payments->isNotEmpty())
      <h3 class="section-title">{{ __('app.invoice.payment_history') }}</h3>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>{{ __('app.invoice.transaction_id') }}</th>
              <th>{{ __('app.invoice.method') }}</th>
              <th class="text-right">{{ __('app.invoice.amount') }}</th>
              <th class="text-center">{{ __('app.invoice.status') }}</th>
              <th class="text-right">{{ __('app.invoice.date') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->payments as $payment)
            <tr>
              <td class="text-sm text-muted">{{ $payment->transaction_id }}</td>
              <td>{{ __('app.payment.' . $payment->payment_method) }}</td>
              <td class="text-right font-semibold">${{ number_format((float)$payment->amount, 2) }}</td>
              <td class="text-center">
                <span class="status-pill status-{{ $payment->status }}">
                  {{ __('app.payment_status.' . $payment->status) }}
                </span>
              </td>
              <td class="text-right text-sm">{{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>

    <!-- Footer -->
    <div class="footer">
      <div class="footer-logo">
        <div class="footer-logo-icon">ح</div>
        <div class="footer-logo-text">Al-Haramain</div>
      </div>
      <p>{{ __('app.invoice.thank_you') }}</p>
      <p>{{ __('app.invoice.questions') }}</p>
      <div class="footer-note">
        {{ __('app.invoice.computer_generated') }}
      </div>
    </div>
  </div>
</body>
</html>
