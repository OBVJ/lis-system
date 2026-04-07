@extends('layouts.app')

@section('title', __('Payment Receipt'))

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .receipt-container { box-shadow: none !important; border: 1px solid #000 !important; }
    }

    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 30px;
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .receipt-title {
        font-size: 28px;
        font-weight: bold;
        color: #0d6efd;
        margin-bottom: 10px;
    }

    .receipt-subtitle {
        font-size: 16px;
        color: #6c757d;
    }

    .receipt-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-section h5 {
        color: #0d6efd;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .info-label {
        font-weight: 500;
        color: #495057;
    }

    .info-value {
        color: #212529;
    }

    .tests-table {
        margin-bottom: 30px;
    }

    .tests-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .tests-table th,
    .tests-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    .tests-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }

    .payment-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .summary-total {
        font-size: 18px;
        font-weight: bold;
        color: #0d6efd;
        border-top: 2px solid #0d6efd;
        padding-top: 10px;
        margin-top: 10px;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
        color: #6c757d;
        font-size: 14px;
    }

    .print-btn {
        background: #0d6efd;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        margin-bottom: 20px;
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
            <h5>{{ __('Requested Tests') }}</h5>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Test Name') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labRequest->items as $item)
                    <tr>
                        <td>{{ $item->test->name }}</td>
                        <td>{{ $item->test->category->name ?? __('N/A') }}</td>
                        <td>{{ number_format($item->test->price, 2) }} {{ __('EGP') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="summary-row">
                <span>{{ __('Subtotal:') }}</span>
                <span>{{ number_format($labRequest->payment->amount + $labRequest->payment->discount_value, 2) }} {{ __('EGP') }}</span>
            </div>
            @if($labRequest->payment->discount_value > 0)
            <div class="summary-row">
                <span>{{ __('Discount:') }}</span>
                <span>-{{ number_format($labRequest->payment->discount_value, 2) }} {{ __('EGP') }}</span>
            </div>
            @endif
            <div class="summary-row summary-total">
                <span>{{ __('Total Amount:') }}</span>
                <span>{{ number_format($labRequest->payment->amount, 2) }} {{ __('EGP') }}</span>
            </div>
            <div class="summary-row">
                <span>{{ __('Paid Amount:') }}</span>
                <span>{{ number_format($labRequest->payment->paid_amount, 2) }} {{ __('EGP') }}</span>
            </div>
            <div class="summary-row">
                <span>{{ __('Payment Status:') }}</span>
                <span>{{ __($labRequest->payment->status) }}</span>
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