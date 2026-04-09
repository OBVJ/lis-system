@extends('layouts.app')

@section('title', __('Payment Receipt'))

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .receipt-container { box-shadow: none !important; border: none !important; max-width: 360px !important; width: 100% !important; margin: 0 auto !important; padding: 12px !important; }
        .receipt-container * { box-shadow: none !important; }
        html, body { margin: 0 !important; padding: 0 !important; }
    }

    .receipt-container {
        max-width: 360px;
        width: 100%;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 18px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #212529;
    }

    body[dir='rtl'] .receipt-container {
        direction: rtl;
        text-align: right;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 18px;
    }

    .receipt-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .receipt-subtitle {
        font-size: 13px;
        color: #555;
        margin-bottom: 10px;
    }

    .receipt-meta {
        font-size: 12px;
        color: #555;
        margin-bottom: 4px;
    }

    .info-section {
        margin-bottom: 14px;
    }

    .info-heading {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #0d6efd;
        border-bottom: 1px dashed #dee2e6;
        padding-bottom: 6px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        padding: 4px 0;
    }

    body[dir='rtl'] .info-row {
        flex-direction: row-reverse;
    }

    .info-label {
        color: #555;
    }

    .info-value {
        color: #111;
        font-weight: 600;
    }

    .tests-table {
        margin-bottom: 16px;
    }

    .tests-table table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .tests-table th,
    .tests-table td {
        padding: 6px 4px;
        border-bottom: 1px dashed #dee2e6;
    }

    .tests-table th {
        text-align: left;
        font-weight: 700;
        color: #333;
    }

    body[dir='rtl'] .tests-table th,
    body[dir='rtl'] .tests-table td {
        text-align: right;
    }

    .payment-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px;
        font-size: 12px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 4px 0;
    }

    .summary-total {
        margin-top: 8px;
        font-weight: 700;
        border-top: 1px dashed #dee2e6;
        padding-top: 8px;
    }

    .receipt-footer {
        text-align: center;
        color: #777;
        font-size: 12px;
        margin-top: 16px;
    }

    .print-btn {
        width: 100%;
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 10px 0;
        border-radius: 8px;
        font-size: 14px;
    }

    .print-btn:hover {
        background: #0b5ed7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h1 class="h3 mb-0">{{ __('Payment Receipt') }}</h1>
        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print me-2"></i>{{ __('Print Receipt') }}
        </button>
    </div>

    <div class="receipt-container">
        @php
            $currency = App\Models\Setting::get('currency_symbol', 'SDG');
            $payment = $labRequest->payment;
            $subtotal = optional($payment)->amount + optional($payment)->discount_value;
        @endphp
        <!-- Receipt Header -->
        <div class="receipt-header">
            <h1 class="receipt-title">{{ __('Laboratory Information System') }}</h1>
            <p class="receipt-subtitle">{{ __('Payment Receipt') }}</p>
            <p class="mb-0">{{ __('Receipt No:') }} #{{ $labRequest->id }}</p>
            <p>{{ __('Date:') }} {{ $labRequest->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Patient & Request Information -->
        <div class="receipt-info">
            <div class="info-section">
                <h5>{{ __('Patient Information') }}</h5>
                <div class="info-row">
                    <span class="info-label">{{ __('Name:') }}</span>
                    <span class="info-value">{{ $labRequest->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Patient Code:') }}</span>
                    <span class="info-value">{{ $labRequest->patient->patient_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Age:') }}</span>
                    <span class="info-value">{{ $labRequest->patient->age }} {{ __('years') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Gender:') }}</span>
                    <span class="info-value">{{ __($labRequest->patient->gender) }}</span>
                </div>
                @if($labRequest->patient->phone)
                <div class="info-row">
                    <span class="info-label">{{ __('Phone:') }}</span>
                    <span class="info-value">{{ $labRequest->patient->phone }}</span>
                </div>
                @endif
            </div>

            <div class="info-section">
                <h5>{{ __('Request Information') }}</h5>
                <div class="info-row">
                    <span class="info-label">{{ __('Request ID:') }}</span>
                    <span class="info-value">#{{ $labRequest->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Status:') }}</span>
                    <span class="info-value">{{ __($labRequest->status) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Priority:') }}</span>
                    <span class="info-value">{{ __($labRequest->priority) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Created By:') }}</span>
                    <span class="info-value">{{ $labRequest->createdBy->name ?? __('System') }}</span>
                </div>
            </div>
        </div>

        <!-- Tests Table -->
        <div class="tests-table">
            <div class="info-heading">{{ __('Requested Tests') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Test') }}</th>
                        <th>{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labRequest->items as $item)
                    <tr>
                        <td>{{ optional($item->test)->name ?? __('N/A') }}</td>
                        <td>{{ number_format(optional($item->test)->price ?? 0, 2) }} {{ $currency }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="summary-row">
                <span>{{ __('Subtotal:') }}</span>
                <span>{{ number_format($subtotal, 2) }} {{ $currency }}</span>
            </div>
            @if(optional($payment)->discount_value > 0)
            <div class="summary-row">
                <span>{{ __('Discount:') }}</span>
                <span>-{{ number_format(optional($payment)->discount_value, 2) }} {{ $currency }}</span>
            </div>
            @endif
            <div class="summary-row summary-total">
                <span>{{ __('Total Amount:') }}</span>
                <span>{{ number_format(optional($payment)->amount ?? 0, 2) }} {{ $currency }}</span>
            </div>
            <div class="summary-row">
                <span>{{ __('Paid Amount:') }}</span>
                <span>{{ number_format(optional($payment)->paid_amount ?? 0, 2) }} {{ $currency }}</span>
            </div>
            <div class="summary-row">
                <span>{{ __('Payment Status:') }}</span>
                <span>{{ __($payment->status ?? 'unpaid') }}</span>
            </div>
        </div>

        <!-- Receipt Footer -->
        <div class="receipt-footer">
            <p>{{ __('Thank you for choosing our laboratory services.') }}</p>
            <p>{{ __('Please keep this receipt for your records.') }}</p>
            <p>{{ __('For results collection, please present this receipt.') }}</p>
        </div>
    </div>
</div>
@endsection