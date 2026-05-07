@extends('layouts.master')

@section('title', 'Detail Riwayat Analisis')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('history.index') }}" class="btn btn-light btn-icon">
        <i class="ti ti-arrow-left"></i>
    </a>
    <div>
        <h2 class="mb-0">Detail Riwayat Analisis</h2>
        <p class="text-secondary mb-0 no-print">Disimpan pada {{ $session->created_at->format('d F Y, H:i') }} WIB</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-secondary small mb-1">Rentang Data</div>
            <div class="fw-bold">{{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-secondary small mb-1">Min. Support</div>
            <div class="fw-bold text-primary">{{ $session->min_support }}%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-secondary small mb-1">Min. Confidence</div>
            <div class="fw-bold text-primary">{{ $session->min_confidence }}%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-secondary small mb-1">Total Transaksi</div>
            <div class="fw-bold">{{ $session->total_transactions }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
        <ul class="nav nav-pills gap-2" id="historyTab" role="tablist">
            @if($session->transformations->isNotEmpty())
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="trans-tab" data-bs-toggle="tab" data-bs-target="#trans" type="button" role="tab">1. Transformation & Tabulasi</button>
                </li>
            @endif
            @if($session->steps->isNotEmpty())
                @foreach($session->steps as $step)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $session->transformations->isEmpty() && $step->k == 1 ? 'active' : '' }}" id="step{{ $step->k }}-tab" data-bs-toggle="tab" data-bs-target="#step{{ $step->k }}" type="button" role="tab">{{ $session->transformations->isNotEmpty() ? $step->k + 1 : $step->k }}. {{ $step->k }}-Itemset</button>
                    </li>
                @endforeach
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $session->transformations->isEmpty() && $session->steps->isEmpty() ? 'active' : '' }}" id="rules-tab" data-bs-toggle="tab" data-bs-target="#rules" type="button" role="tab">{{ $session->transformations->isNotEmpty() ? $session->steps->count() + 2 : 'Hasil Akhir' }}. Hasil Akhir (Rules)</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4">
        <div class="tab-content" id="historyTabContent">
            <!-- TAB: TRANSFORMATION -->
            @if($session->transformations->isNotEmpty())
                <div class="tab-pane fade show active" id="trans" role="tabpanel">
                    <div class="mb-3">
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                            <i class="ti ti-database"></i> Total data yang dianalisis: <strong>{{ $session->total_transactions }}</strong> transaksi
                        </span>
                    </div>
                    <div class="row">
                        <!-- Tabel Transformasi -->
                        <div class="col-lg-6">
                            <div class="card border mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 small fw-bold">Tabel Transformasi Data (Seluruh Transaksi)</h6>
                                </div>
                                <div class="table-responsive" style="max-height: 450px;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="px-3">No</th>
                                                <th>No. Invoice</th>
                                                <th>Itemset</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i = 1; @endphp
                                            @foreach($session->transformations as $trans)
                                                <tr>
                                                    <td class="px-3 text-secondary small">{{ $i++ }}</td>
                                                    <td class="small fw-bold">{{ $trans->invoice_no }}</td>
                                                    <td class="small"><code>{{ $trans->items }}</code></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Tabulasi (Ambil dari Step 1 Candidates) -->
                        <div class="col-lg-6">
                            <div class="card border mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 small fw-bold">Tabel Frekuensi Kemunculan Barang (Tabulasi)</h6>
                                </div>
                                <div class="table-responsive" style="max-height: 450px;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="px-3">No</th>
                                                <th>Kode Barang</th>
                                                <th>Jumlah</th>
                                                <th>Support (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $step1 = $session->steps->where('k', 1)->first();
                                                $candidates1 = $step1 ? $step1->candidates : collect();
                                            @endphp
                                            @if($candidates1->isNotEmpty())
                                                @php $j = 1; @endphp
                                                @foreach($candidates1 as $candidate)
                                                    <tr>
                                                        <td class="px-3 text-secondary small">{{ $j++ }}</td>
                                                        <td><code>{{ $candidate->items }}</code></td>
                                                        <td class="fw-bold">{{ $candidate->count }}</td>
                                                        <td class="small">{{ number_format($candidate->support, 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">Data tabulasi tidak tersedia.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TABS: ITEMSETS -->
            @if($session->steps->isNotEmpty())
                @foreach($session->steps as $step)
                    <div class="tab-pane fade {{ $session->transformations->isEmpty() && $step->k == 1 ? 'show active' : '' }}" id="step{{ $step->k }}" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-primary">Langkah {{ $step->k }}: Menghitung Pola Frequent {{ $step->k }}-itemset</h4>
                            <span class="badge bg-primary px-3 py-2">Proses {{ $step->k }}</span>
                        </div>
                        <div class="row">
                            <!-- Tabel Calon (Left) -->
                            <div class="col-lg-7">
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 small fw-bold">Tabel Calon {{ $step->k }}-itemset (C{{ $step->k }})</h6>
                                        <span class="badge bg-secondary small">{{ $step->candidates->count() }} Baris</span>
                                    </div>
                                    <div class="table-responsive" style="max-height: 450px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="px-3">Kode Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Support (%)</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($step->candidates as $candidate)
                                                    <tr class="{{ $candidate->is_frequent ? 'table-success-light' : '' }}">
                                                        <td class="px-3"><code>{{ $candidate->items }}</code></td>
                                                        <td>{{ $candidate->count }}</td>
                                                        <td>{{ number_format($candidate->support, 2) }}%</td>
                                                        <td>
                                                            @if($candidate->is_frequent)
                                                                <span class="badge bg-success small" style="font-size: 0.7rem;">Lolos</span>
                                                            @else
                                                                <span class="badge bg-light text-secondary small" style="font-size: 0.7rem;">Gagal</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4">Data calon tidak tersedia.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Hasil Seleksi (Right) -->
                            <div class="col-lg-5">
                                <div class="card border border-primary mb-3 h-100">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0 small fw-bold">Hasil Seleksi {{ $step->k }}-itemset (L{{ $step->k }})</h6>
                                    </div>
                                    <div class="table-responsive" style="max-height: 450px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="px-3">Itemset</th>
                                                    <th class="text-center">Jumlah</th>
                                                    <th class="text-center">Support (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($step->frequent as $frequent)
                                                    <tr>
                                                        <td class="px-3"><code>{{ $frequent->items }}</code></td>
                                                        <td class="text-center">{{ $frequent->count }}</td>
                                                        <td class="text-center">{{ number_format($frequent->support, 2) }}%</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-secondary">
                                                            <i class="ti ti-alert-circle d-block fs-1 mb-2"></i>
                                                            Tidak ada data.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body bg-light py-3 mt-auto">
                                        <p class="mb-1 small">Minimum Support:</p>
                                        <h5 class="mb-0 text-primary">{{ number_format($session->min_support, 2) }}%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- TAB: RULES -->
            <div class="tab-pane fade {{ $session->steps->isEmpty() ? 'show active' : '' }}" id="rules" role="tabpanel">
                <h5 class="mb-4">Association Rules yang Ditemukan</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="py-3">Aturan (Rules)</th>
                                <th class="py-3 text-center">Support</th>
                                <th class="py-3 text-center">Confidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($session->results as $index => $rule)
                                <tr>
                                    <td class="px-4 text-secondary">{{ $index + 1 }}</td>
                                    <td>
                                        Jika membeli <span class="fw-bold text-dark">{{ $rule->antecedent }}</span>, <br>
                                        maka akan membeli <span class="fw-bold text-primary">{{ $rule->consequent }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($rule->support, 2) }}%</td>
                                    <td class="text-center">
                                        <div class="fw-bold">{{ number_format($rule->confidence, 2) }}%</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">Tidak ada aturan yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 py-3 text-end px-4">
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="ti ti-printer"></i> Cetak Laporan
        </button>
    </div>
</div>

<style>
    @media print {
        @page {
            margin: 0;
        }
        body {
            padding: 2cm !important;
        }
        .sidebar, .topbar, .btn-icon, .card-footer, .nav-pills, #sidebar, #topbar, .no-print, footer, .footer, .print-hide {
            display: none !important;
        }
        .content {
            margin-left: 0 !important;
            padding: 0 !important;
            padding-top: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table-responsive {
            overflow: visible !important;
            max-height: none !important;
        }
    }
    .table-success-light {
        background-color: rgba(25, 135, 84, 0.05);
    }
    .sticky-top {
        top: -1px;
        z-index: 10;
    }
    code {
        color: #d63384;
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 4px;
    }
</style>
@endsection
