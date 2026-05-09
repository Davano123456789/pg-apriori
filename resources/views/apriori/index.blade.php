@extends('layouts.master')

@section('title', 'Proses Apriori')

@section('content')
<div class="mb-4">
    <h2>Proses Apriori</h2>
    <p class="text-secondary">Tentukan parameter dan rentang tanggal untuk melakukan analisis pola pembelian.</p>
</div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('apriori.calculate') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Tanggal Mulai <small class="text-secondary">(Tgl/Bln/Thn)</small></label>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link p-0 text-primary text-decoration-none dropdown-toggle small" type="button" data-bs-toggle="dropdown">
                                Pilih Cepat
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                <li><a class="dropdown-item set-date" href="#" data-start="{{ $min_db_date }}" data-end="{{ $max_db_date }}">Semua Data</a></li>
                                <li><a class="dropdown-item set-date" href="#" data-start="{{ date('Y-01-01') }}" data-end="{{ date('Y-12-31') }}">Tahun Ini ({{ date('Y') }})</a></li>
                                <li><a class="dropdown-item set-date" href="#" data-start="{{ date('Y-m-01') }}" data-end="{{ date('Y-m-t') }}">Bulan Ini</a></li>
                            </ul>
                        </div>
                    </div>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker" value="{{ $params['start_date'] ?? '' }}" placeholder="Pilih Tanggal" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai <small class="text-secondary">(Tgl/Bln/Thn)</small></label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker" value="{{ $params['end_date'] ?? '' }}" placeholder="Pilih Tanggal" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Min. Support (%)</label>
                    <input type="number" step="0.1" name="min_support" class="form-control" placeholder="Contoh: 10" value="{{ $params['min_support'] ?? '' }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Min. Confidence (%)</label>
                    <input type="number" step="0.1" name="min_confidence" class="form-control" placeholder="Contoh: 50" value="{{ $params['min_confidence'] ?? '' }}" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-calculator"></i> Hitung
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($results))
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <ul class="nav nav-pills gap-2" id="aprioriTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="trans-tab" data-bs-toggle="tab" data-bs-target="#trans" type="button" role="tab">1. Transformation dan Tabulasi</button>
                </li>
                @foreach($results['step_by_step'] as $k => $step)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="step{{ $k }}-tab" data-bs-toggle="tab" data-bs-target="#step{{ $k }}" type="button" role="tab">{{ $k+1 }}. {{ $k }}-Itemset</button>
                    </li>
                @endforeach
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rules-tab" data-bs-toggle="tab" data-bs-target="#rules" type="button" role="tab">{{ count($results['step_by_step']) + 2 }}. Hasil Akhir</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="aprioriTabContent">
                <!-- TAB: DATA TRANSFORMATION -->
                <div class="tab-pane fade show active" id="trans" role="tabpanel">
                    <div class="mb-4">
                        <h4 class="mb-3 text-primary">Transformation dan Tabulasi</h4>
                        <div class="mb-3">
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                <i class="ti ti-database"></i> Total data yang dianalisis: <strong>{{ $total_invoices }}</strong> transaksi
                            </span>
                        </div>
                        
                        <div class="row">
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
                                                    <th>Kode Barang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i = 1; @endphp
                                                @foreach($transformation as $invoice => $items)
                                                    <tr>
                                                        <td class="px-3 text-secondary small">{{ $i++ }}</td>
                                                        <td class="small fw-bold">{{ $invoice }}</td>
                                                        <td class="small"><code>{{ implode(', ', $items) }}</code></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer bg-white py-1 text-center">
                                        <small class="text-primary fw-bold">Menampilkan seluruh {{ count($transformation) }} transaksi.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="card border mb-4">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold">Tabel Frekuensi Kemunculan Barang (Jumlah per Invoice)</h6>
                                    </div>
                                    <div class="table-responsive" style="max-height: 450px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="px-3">No</th>
                                                    <th>Kode Barang</th>
                                                    <th>Jumlah (Invoice)</th>
                                                    <th>Support (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $j = 1; @endphp
                                                @foreach($results['step_by_step'][1]['candidates'] as $candidate)
                                                    <tr>
                                                        <td class="px-3 text-secondary small">{{ $j++ }}</td>
                                                        <td><code>{{ $candidate['items'][0] }}</code></td>
                                                        <td class="fw-bold">{{ $candidate['count'] }}</td>
                                                        <td class="small">{{ number_format($candidate['support'], 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer bg-white py-1">
                                        <small class="text-secondary italic" style="font-size: 0.7rem;">* Jumlah dihitung berdasarkan banyaknya invoice unik yang mengandung barang tersebut.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABS: ITEMSETS (L1, L2, etc) -->
                @foreach($results['step_by_step'] as $k => $step)
                    <div class="tab-pane fade" id="step{{ $k }}" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-primary">Langkah {{ $k }}: Menghitung Pola Frequent {{ $k }}-itemset</h4>
                            <span class="badge bg-primary px-3 py-2">Proses {{ $k }}</span>
                        </div>
                        
                        <div class="row">
                            <!-- Tabel Calon (Candidates) -->
                            <div class="col-lg-7">
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 small fw-bold">Tabel Calon {{ $k }}-itemset (C{{ $k }})</h6>
                                        <span class="badge bg-secondary small">{{ count($step['candidates']) }} Baris</span>
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
                                                @foreach($step['candidates'] as $candidate)
                                                    <tr class="{{ $candidate['is_frequent'] ? 'table-success-light' : '' }}">
                                                        <td class="px-3"><code>{{ implode(', ', $candidate['items']) }}</code></td>
                                                        <td>{{ $candidate['count'] }}</td>
                                                        <td>{{ number_format($candidate['support'], 2) }}%</td>
                                                        <td>
                                                            @if($candidate['is_frequent'])
                                                                <span class="badge bg-success small" style="font-size: 0.7rem;">Lolos</span>
                                                            @else
                                                                <span class="badge bg-light text-secondary small" style="font-size: 0.7rem;">Gagal</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Hasil Seleksi -->
                            <div class="col-lg-5">
                                <div class="card border border-primary mb-3 h-100">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0 small fw-bold">Hasil Seleksi {{ $k }}-itemset (L{{ $k }})</h6>
                                    </div>
                                    <div class="table-responsive" style="max-height: 450px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="px-3">Kode Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Support (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($step['frequent'] as $frequent)
                                                    <tr>
                                                        <td class="px-3"><code>{{ implode(', ', $frequent['items']) }}</code></td>
                                                        <td>{{ $frequent['count'] }}</td>
                                                        <td>{{ number_format($frequent['support'], 2) }}%</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-5 text-secondary">
                                                            <i class="ti ti-alert-circle d-block fs-1 mb-2"></i>
                                                            Tidak ada itemset yang lolos seleksi.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body bg-light py-3 mt-auto">
                                        <p class="mb-1 small">Minimum Support:</p>
                                        <h5 class="mb-0 text-primary">{{ number_format($params['min_support'], 2) }}%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- TAB: RULES -->
                <!-- TAB: RULES -->
                <div class="tab-pane fade" id="rules" role="tabpanel">
                    <!-- RINGKASAN DEBUG (UNTUK ANALISIS) -->
                    <div class="alert alert-light border mb-4">
                        <h6 class="fw-bold mb-2"><i class="ti ti-info-circle"></i> Ringkasan Statistik Proses:</h6>
                        <ul class="mb-0 small">
                            <li>Total Transaksi: <strong>{{ count($transformation) }}</strong></li>
                            @foreach($results['step_by_step'] as $k => $step)
                                <li>Langkah {{ $k }} (L{{ $k }}): <strong>{{ count($step['frequent']) }}</strong> itemset yang memenuhi minimum support.</li>
                            @endforeach
                            <li>Total Calon Aturan: <strong>{{ count($results['rules']['candidates']) }}</strong></li>
                            <li>Aturan Lolos Seleksi: <strong>{{ count($results['rules']['final']) }}</strong></li>
                        </ul>
                    </div>

                    <!-- TABEL CALON ATURAN (C-RULES) -->
                    <div class="mb-5">
                        <h5 class="mb-3 d-flex align-items-center gap-2">
                            <i class="ti ti-list-check text-primary"></i> Tabel Calon Aturan (C-Rules)
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Calon Aturan</th>
                                        <th class="py-3">Support</th>
                                        <th class="py-3">Confidence</th>
                                        <th class="py-3">Min. Conf</th>
                                        <th class="py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['rules']['candidates'] as $rule)
                                        <tr class="{{ $rule['is_passed'] ? 'table-success-light' : '' }}">
                                            <td class="px-4 py-3">
                                                Jika membeli <strong>{{ implode(', ', $rule['antecedent_names']) }}</strong>, <br>
                                                maka akan membeli <strong>{{ implode(', ', $rule['consequent_names']) }}</strong>
                                            </td>
                                            <td>{{ number_format($rule['support'], 2) }}%</td>
                                            <td>
                                                <div class="small text-secondary mb-1">{{ $rule['confidence_ratio'] }}</div>
                                                <strong>{{ number_format($rule['confidence'], 2) }}%</strong>
                                            </td>
                                            <td class="text-secondary">{{ number_format($params['min_confidence'], 2) }}%</td>
                                            <td class="text-center">
                                                @if($rule['is_passed'])
                                                    <span class="badge bg-success px-3">Lolos</span>
                                                @else
                                                    <span class="badge bg-light text-secondary px-3">Gagal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABEL HASIL SELEKSI ATURAN (FINAL RULES) -->
                    <div>
                        <h5 class="mb-3 d-flex align-items-center gap-2 text-success">
                            <i class="ti ti-discount-check"></i> Hasil Seleksi Aturan Asosiasi (Final Rules)
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border shadow-sm">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th class="px-4 py-3">Pola Aturan (Rules)</th>
                                        <th class="py-3">Support</th>
                                        <th class="py-3">Confidence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($results['rules']['final'] as $rule)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <i class="ti ti-arrow-right text-success me-2"></i>
                                                Jika membeli <strong>{{ implode(', ', $rule['antecedent_names']) }}</strong>, 
                                                maka akan membeli <strong>{{ implode(', ', $rule['consequent_names']) }}</strong>
                                            </td>
                                            <td>{{ number_format($rule['support'], 2) }}%</td>
                                            <td><span class="badge bg-success-light text-success fs-6">{{ number_format($rule['confidence'], 2) }}%</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-secondary">
                                                <i class="ti ti-mood-empty fs-1 d-block mb-2"></i>
                                                Tidak ada aturan yang lolos kriteria seleksi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!empty($results['rules']['final']))
                        <div class="mt-4 text-end">
                            <form action="{{ route('apriori.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ $params['start_date'] }}">
                                <input type="hidden" name="end_date" value="{{ $params['end_date'] }}">
                                <input type="hidden" name="min_support" value="{{ $params['min_support'] }}">
                                <input type="hidden" name="min_confidence" value="{{ $params['min_confidence'] }}">
                                <input type="hidden" name="total_transactions" value="{{ $results['total_transactions'] }}">
                                <input type="hidden" name="results" value="{{ json_encode($results['rules']['final']) }}">
                                <input type="hidden" name="step_by_step_data" value="{{ json_encode($results['step_by_step']) }}">
                                <input type="hidden" name="transformation_data" value="{{ json_encode($transformation) }}">
                                <input type="hidden" name="transaction_ids" value="{{ json_encode($transaction_ids) }}">
                                <button type="submit" class="btn btn-success px-5 py-3 fw-bold">
                                    <i class="ti ti-device-floppy fs-5 me-2"></i> Simpan Hasil ke Riwayat Analisis
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Flatpickr
        const fpStart = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true
        });
        const fpEnd = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true
        });

        $('.set-date').on('click', function(e) {
            e.preventDefault();
            let start = $(this).data('start');
            let end = $(this).data('end');
            
            if (start && end) {
                fpStart.setDate(start);
                fpEnd.setDate(end);
            }
        });
    });
</script>
<style>
    .table-success-light {
        background-color: rgba(25, 135, 84, 0.05);
    }
    .sticky-top {
        top: -1px;
        z-index: 10;
    }
    .card-header h6 {
        letter-spacing: 0.5px;
    }
    code {
        color: #d63384;
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 4px;
    }
</style>
@endpush
