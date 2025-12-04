<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('statements.pdf.document_title') }} - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1f2937;
        }

        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%);
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }

        .header-logo {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-period {
            font-size: 10pt;
            opacity: 0.9;
        }

        .account-info {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .account-info table {
            width: 100%;
        }

        .account-info td {
            padding: 5px 0;
        }

        .account-info .label {
            font-weight: bold;
            color: #6b7280;
            width: 40%;
        }

        .account-info .value {
            color: #1f2937;
        }

        .summary {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            color: #92400e;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            width: 20%;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #f59e0b;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #92400e;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
        }

        .summary-unit {
            font-size: 8pt;
            color: #6b7280;
        }

        .transactions-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1f2937;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 10px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .transactions-table thead {
            background: #f3f4f6;
            border-top: 2px solid #d1d5db;
            border-bottom: 2px solid #d1d5db;
        }

        .transactions-table th {
            padding: 10px;
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
        }

        .transactions-table th.text-right {
            text-align: right;
        }

        .transactions-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .transactions-table tbody tr:last-child {
            border-bottom: 2px solid #d1d5db;
        }

        .transactions-table td {
            padding: 10px;
            font-size: 9pt;
        }

        .transactions-table td.text-right {
            text-align: right;
        }

        .transaction-date {
            font-weight: bold;
            color: #1f2937;
        }

        .transaction-time {
            font-size: 8pt;
            color: #9ca3af;
        }

        .transaction-description {
            font-weight: 600;
            color: #1f2937;
        }

        .transaction-type {
            font-size: 8pt;
            color: #6b7280;
        }

        .amount-positive {
            font-weight: bold;
            color: #059669;
        }

        .amount-negative {
            font-weight: bold;
            color: #dc2626;
        }

        .balance {
            font-weight: bold;
            color: #7c3aed;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f9fafb;
            border-top: 1px solid #d1d5db;
            padding: 15px 30px;
            font-size: 8pt;
            color: #6b7280;
        }

        .footer-disclaimer {
            margin-bottom: 5px;
        }

        .footer-generated {
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }

        .no-transactions {
            text-align: center;
            padding: 50px;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-logo">💎 FlorenceEGI</div>
        <div class="header-title">{{ __('statements.pdf.title') }}</div>
        <div class="header-period">
            {{ __('statements.egili.period', [
                'from' => $startDate->format('d/m/Y'),
                'to' => $endDate->format('d/m/Y'),
            ]) }}
        </div>
    </div>

    {{-- Account Information --}}
    <div class="account-info">
        <table>
            <tr>
                <td class="label">{{ __('statements.pdf.account_holder') }}:</td>
                <td class="value">{{ $user->name }}</td>
                <td class="label">{{ __('statements.pdf.generated_on') }}:</td>
                <td class="value">{{ $generatedAt->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('statements.pdf.account_id') }}:</td>
                <td class="value">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td class="label">{{ __('statements.pdf.wallet_id') }}:</td>
                <td class="value">#{{ str_pad($user->primaryWallet->id ?? 0, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
        </table>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-title">{{ __('statements.egili.summary.title') }}</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">{{ __('statements.egili.summary.starting_balance') }}</div>
                <div class="summary-value">{{ number_format($egiliSummary['starting_balance']) }}</div>
                <div class="summary-unit">Egili</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('statements.egili.summary.total_income') }}</div>
                <div class="summary-value" style="color: #059669;">+{{ number_format($egiliSummary['total_income']) }}
                </div>
                <div class="summary-unit">Egili</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('statements.egili.summary.total_expenses') }}</div>
                <div class="summary-value" style="color: #dc2626;">
                    -{{ number_format($egiliSummary['total_expenses']) }}</div>
                <div class="summary-unit">Egili</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('statements.egili.summary.ending_balance') }}</div>
                <div class="summary-value" style="color: #7c3aed;">{{ number_format($egiliSummary['ending_balance']) }}
                </div>
                <div class="summary-unit">Egili</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('statements.egili.summary.transaction_count') }}</div>
                <div class="summary-value">{{ $egiliSummary['transaction_count'] }}</div>
                <div class="summary-unit">Movimenti</div>
            </div>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="transactions-title">{{ __('statements.egili.table.description') }}</div>

    @if ($egiliTransactions->count() > 0)
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>{{ __('statements.egili.table.date') }}</th>
                    <th>{{ __('statements.egili.table.description') }}</th>
                    <th class="text-right">{{ __('statements.egili.table.income') }}</th>
                    <th class="text-right">{{ __('statements.egili.table.expenses') }}</th>
                    <th class="text-right">{{ __('statements.egili.table.balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($egiliTransactions as $transaction)
                    <tr>
                        <td>
                            <div class="transaction-date">{{ $transaction->created_at->format('d/m/Y') }}</div>
                            <div class="transaction-time">{{ $transaction->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="transaction-description">
                                {{ $transaction->description ?? __('statements.egili.types.' . $transaction->transaction_type) }}
                            </div>
                            <div class="transaction-type">
                                {{ __('statements.egili.types.' . $transaction->transaction_type) }}
                            </div>
                        </td>
                        <td class="text-right">
                            @if ($transaction->operation === 'add')
                                <span class="amount-positive">+{{ number_format($transaction->amount) }}</span>
                            @else
                                <span style="color: #d1d5db;">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($transaction->operation === 'subtract')
                                <span class="amount-negative">-{{ number_format($transaction->amount) }}</span>
                            @else
                                <span style="color: #d1d5db;">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <span class="balance">{{ number_format($transaction->balance_after) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-transactions">
            {{ __('statements.egili.no_transactions') }}
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-disclaimer">{{ __('statements.pdf.footer_disclaimer') }}</div>
        <div class="footer-generated">{{ __('statements.pdf.generated_on') }}:
            {{ $generatedAt->format('d/m/Y H:i:s') }}</div>
    </div>

</body>

</html>
