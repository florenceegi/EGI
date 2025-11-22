<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>{{ __('invoices.invoice') }} {{ $invoice->invoice_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.4;
        }
        
        .page {
            padding: 40px;
            background: white;
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #8b5cf6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .header-logo {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-logo h1 {
            font-size: 24pt;
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header-logo .tagline {
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
        
        .header-invoice {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .header-invoice h2 {
            font-size: 18pt;
            color: #1a1a1a;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header-invoice .invoice-number {
            font-size: 12pt;
            color: #666;
            margin-bottom: 10px;
        }
        
        .header-invoice .invoice-date {
            font-size: 9pt;
            color: #666;
        }
        
        /* Parties */
        .parties {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .party {
            display: table-cell;
            width: 48%;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        
        .party:first-child {
            margin-right: 4%;
        }
        
        .party h3 {
            font-size: 11pt;
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #d0d0d0;
            padding-bottom: 5px;
        }
        
        .party p {
            font-size: 9pt;
            margin-bottom: 3px;
            color: #333;
        }
        
        .party strong {
            color: #1a1a1a;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background: #8b5cf6;
            color: white;
        }
        
        .items-table thead th {
            padding: 12px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #7c3aed;
        }
        
        .items-table thead th.text-center {
            text-align: center;
        }
        
        .items-table thead th.text-right {
            text-align: right;
        }
        
        .items-table tbody td {
            padding: 10px 8px;
            border: 1px solid #e0e0e0;
            font-size: 9pt;
            color: #333;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .items-table tbody tr:hover {
            background: #f0f0f0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        /* Totals */
        .totals {
            width: 50%;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .totals-row.total {
            border-bottom: 3px solid #8b5cf6;
            font-weight: bold;
            font-size: 12pt;
            padding: 12px 0;
        }
        
        .totals-label {
            display: table-cell;
            width: 60%;
            text-align: right;
            padding-right: 15px;
            font-size: 10pt;
            color: #666;
        }
        
        .totals-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .totals-row.total .totals-label,
        .totals-row.total .totals-value {
            color: #8b5cf6;
        }
        
        /* Notes */
        .notes {
            background: #fffbea;
            border: 1px solid #f59e0b;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 3px;
        }
        
        .notes h4 {
            font-size: 10pt;
            color: #92400e;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .notes p {
            font-size: 9pt;
            color: #78350f;
            line-height: 1.5;
        }
        
        /* Footer */
        .footer {
            border-top: 2px solid #e0e0e0;
            padding-top: 20px;
            text-align: center;
        }
        
        .footer p {
            font-size: 8pt;
            color: #999;
            margin-bottom: 5px;
        }
        
        .footer .platform-info {
            font-size: 9pt;
            color: #666;
            margin-top: 10px;
        }
        
        .footer .platform-info strong {
            color: #8b5cf6;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-pending {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-sent {
            background: #e0e7ff;
            color: #4338ca;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="page">
        
        {{-- Header --}}
        <div class="header">
            <div class="header-top">
                <div class="header-logo">
                    <h1>FlorenceEGI</h1>
                    <p class="tagline">{{ __('invoices.tagline') }}</p>
                </div>
                <div class="header-invoice">
                    <h2>{{ __('invoices.invoice') }}</h2>
                    <p class="invoice-number">{{ $invoice->invoice_code }}</p>
                    <p class="invoice-date">
                        {{ __('invoices.fields.issue_date') }}: {{ $invoice->issue_date->format('d/m/Y') }}
                    </p>
                    <p style="margin-top: 5px;">
                        <span class="status-badge status-{{ $invoice->invoice_status }}">
                            {{ __('invoices.status.' . $invoice->invoice_status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Parties --}}
        <div class="parties">
            <div class="party">
                <h3>{{ __('invoices.fields.seller') }}</h3>
                <p><strong>{{ $invoice->seller->name }}</strong></p>
                @if($invoice->seller->invoicePreferences)
                    @if($invoice->seller->invoicePreferences->company_name)
                        <p>{{ $invoice->seller->invoicePreferences->company_name }}</p>
                    @endif
                    @if($invoice->seller->invoicePreferences->vat_number)
                        <p>P.IVA: {{ $invoice->seller->invoicePreferences->vat_number }}</p>
                    @endif
                    @if($invoice->seller->invoicePreferences->fiscal_code)
                        <p>C.F.: {{ $invoice->seller->invoicePreferences->fiscal_code }}</p>
                    @endif
                    @if($invoice->seller->invoicePreferences->address)
                        <p>{{ $invoice->seller->invoicePreferences->address }}</p>
                    @endif
                    @if($invoice->seller->invoicePreferences->city)
                        <p>
                            {{ $invoice->seller->invoicePreferences->postal_code }} 
                            {{ $invoice->seller->invoicePreferences->city }} 
                            ({{ $invoice->seller->invoicePreferences->province }})
                        </p>
                    @endif
                    @if($invoice->seller->invoicePreferences->country)
                        <p>{{ $invoice->seller->invoicePreferences->country }}</p>
                    @endif
                @endif
                <p style="margin-top: 5px;">{{ $invoice->seller->email }}</p>
            </div>
            
            <div class="party">
                <h3>{{ __('invoices.fields.buyer') }}</h3>
                @if($invoice->buyer)
                    <p><strong>{{ $invoice->buyer->name }}</strong></p>
                    @if($invoice->buyer->invoicePreferences)
                        @if($invoice->buyer->invoicePreferences->company_name)
                            <p>{{ $invoice->buyer->invoicePreferences->company_name }}</p>
                        @endif
                        @if($invoice->buyer->invoicePreferences->vat_number)
                            <p>P.IVA: {{ $invoice->buyer->invoicePreferences->vat_number }}</p>
                        @endif
                        @if($invoice->buyer->invoicePreferences->fiscal_code)
                            <p>C.F.: {{ $invoice->buyer->invoicePreferences->fiscal_code }}</p>
                        @endif
                        @if($invoice->buyer->invoicePreferences->address)
                            <p>{{ $invoice->buyer->invoicePreferences->address }}</p>
                        @endif
                        @if($invoice->buyer->invoicePreferences->city)
                            <p>
                                {{ $invoice->buyer->invoicePreferences->postal_code }} 
                                {{ $invoice->buyer->invoicePreferences->city }} 
                                ({{ $invoice->buyer->invoicePreferences->province }})
                            </p>
                        @endif
                        @if($invoice->buyer->invoicePreferences->country)
                            <p>{{ $invoice->buyer->invoicePreferences->country }}</p>
                        @endif
                    @endif
                    <p style="margin-top: 5px;">{{ $invoice->buyer->email }}</p>
                @else
                    <p><strong>{{ __('invoices.aggregations.multiple_buyers') }}</strong></p>
                    <p style="color: #666; font-size: 8pt; margin-top: 5px;">
                        {{ __('invoices.info.aggregation_buyers_info') }}
                    </p>
                @endif
            </div>
        </div>
        
        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('invoices.fields.code') }}</th>
                    <th class="text-left">{{ __('invoices.fields.description') }}</th>
                    <th class="text-center">{{ __('invoices.fields.quantity') }}</th>
                    <th class="text-right">{{ __('invoices.fields.unit_price') }}</th>
                    <th class="text-center">{{ __('invoices.fields.tax_rate') }}</th>
                    <th class="text-right">{{ __('invoices.fields.tax_amount') }}</th>
                    <th class="text-right">{{ __('invoices.fields.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="text-left">{{ $item->code ?? '-' }}</td>
                        <td class="text-left">{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">€ {{ number_format($item->unit_price_eur, 2, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="text-right">€ {{ number_format($item->tax_amount_eur, 2, ',', '.') }}</td>
                        <td class="text-right"><strong>€ {{ number_format($item->total_eur, 2, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- Totals --}}
        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">{{ __('invoices.fields.subtotal') }}:</div>
                <div class="totals-value">€ {{ number_format($invoice->subtotal_eur, 2, ',', '.') }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">{{ __('invoices.fields.tax_amount') }}:</div>
                <div class="totals-value">€ {{ number_format($invoice->tax_amount_eur, 2, ',', '.') }}</div>
            </div>
            <div class="totals-row total">
                <div class="totals-label">{{ __('invoices.fields.total') }}:</div>
                <div class="totals-value">€ {{ number_format($invoice->total_eur, 2, ',', '.') }}</div>
            </div>
        </div>
        
        {{-- Notes --}}
        @if($invoice->notes)
            <div class="notes">
                <h4>{{ __('invoices.fields.notes') }}:</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif
        
        {{-- Management Info --}}
        @if($invoice->managed_by === 'platform')
            <div class="notes" style="background: #ede9fe; border-color: #8b5cf6;">
                <h4 style="color: #5b21b6;">{{ __('invoices.info.platform_managed_title') }}</h4>
                <p style="color: #6b21a8;">
                    {{ __('invoices.info.platform_managed_invoice_info') }}
                </p>
            </div>
        @endif
        
        {{-- Footer --}}
        <div class="footer">
            <p>{{ __('invoices.pdf.footer_line_1') }}</p>
            <p>{{ __('invoices.pdf.footer_line_2') }}</p>
            <p class="platform-info">
                <strong>FlorenceEGI</strong> - {{ __('invoices.pdf.platform_description') }}
            </p>
            <p style="margin-top: 10px; font-size: 7pt;">
                {{ __('invoices.pdf.generated_at') }}: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
        
    </div>
</body>
</html>

