@extends('layouts.master')

@section('title', 'Data Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Data Transaksi</h2>
        <p class="text-secondary">Daftar seluruh transaksi yang telah diupload</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="ti ti-upload"></i> Upload Excel
        </button>
        <form action="{{ route('transactions.truncate') }}" method="POST" class="truncate-form">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2 btn-truncate">
                <i class="ti ti-trash"></i> Kosongkan Data
            </button>
        </form>
    </div>
</div>


<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-secondary small text-uppercase">No</th>
                    <th class="py-3 text-secondary small text-uppercase">Tanggal</th>
                    <th class="py-3 text-secondary small text-uppercase">No. Invoice</th>
                    <th class="py-3 text-secondary small text-uppercase">Kode Cust.</th>
                    <th class="py-3 text-secondary small text-uppercase">Nama Customer</th>
                    <th class="py-3 text-secondary small text-uppercase">Kode Barang</th>
                    <th class="py-3 text-secondary small text-uppercase">Nama Barang</th>
                    <th class="py-3 text-secondary small text-uppercase">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $transaction)
                    <tr class="text-nowrap">
                        <td class="px-4">{{ $transactions->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-light text-dark fw-normal">{{ $transaction->invoice_no }}</span></td>
                        <td><small>{{ $transaction->customer_code ?? '-' }}</small></td>
                        <td>{{ $transaction->customer_name ?? '-' }}</td>
                        <td><code>{{ $transaction->item_code }}</code></td>
                        <td>{{ $transaction->item_name }}</td>
                        <td>{{ $transaction->quantity ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-secondary">
                                <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                Belum ada data transaksi. Silakan upload file Excel.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title" id="uploadModalLabel">Upload Data Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    <div class="form-text mt-2 text-muted small">
                        Pastikan file Excel memiliki kolom: <strong>Tanggal, No. Invoice, Kode Customer, Nama Customer, Kode Barang, Nama Barang, Qty</strong>.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Upload Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-truncate').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Kosongkan Seluruh Data?',
                text: "Semua data transaksi yang sudah diupload akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
