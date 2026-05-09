@extends('layouts.master')

@section('title', 'Riwayat Analisis')

@section('content')
    <div class="mb-4">
        <h2>Riwayat Analisis Apriori</h2>
        <p class="text-secondary">Daftar hasil analisis yang telah disimpan sebelumnya.</p>
    </div>


    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Tanggal Analisis</th>
                            <th class="py-3">Rentang Data</th>
                            <th class="py-3 text-center">Min. Support</th>
                            <th class="py-3 text-center">Min. Confidence</th>
                            <th class="py-3 text-center">Jumlah Aturan</th>
                            <th class="py-3">Dijalankan Oleh</th>
                            <th class="py-3 text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold">{{ $session->created_at->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $session->min_support }}%</td>
                                <td class="text-center">{{ $session->min_confidence }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">{{ $session->results_count }}
                                        Aturan</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-1 me-2">
                                            <i class="ti ti-user fs-5 text-secondary"></i>
                                        </div>
                                        <span class="text-secondary small">{{ $session->user->name ?? 'Sistem' }}</span>
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('history.show', $session->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>
                                        <form action="{{ route('history.destroy', $session->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">
                                    <i class="ti ti-history fs-1 d-block mb-2"></i>
                                    Belum ada riwayat analisis yang disimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-delete').on('click', function (e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data riwayat ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
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