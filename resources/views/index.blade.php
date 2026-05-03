@extends('layouts.master')

@section('title', 'Dashboard - Sistem Apriori')

@section('content')
<div class="row ">
  <div class="col-12">
    <div class="mb-6">
      <h1 class="fs-3 mb-1">Dashboard</h1>
      <p>Selamat datang di Sistem Analisis Pola Pembelian Produk (Apriori).</p>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-4 col-12">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4">
          <div class="icon-shape bg-primary text-white rounded-circle shadow-sm" style="width: 60px; height: 60px; min-width: 60px;">
            <i class="ti ti-database fs-2"></i>
          </div>
          <div>
            <p class="text-secondary mb-1 fw-medium">Total Transaksi</p>
            <h2 class="fw-bold mb-0">{{ number_format($totalTransaksi) }}</h2>
            <small class="text-primary fw-semibold">Invoice Terdaftar</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-12">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4">
          <div class="icon-shape bg-success text-white rounded-circle shadow-sm" style="width: 60px; height: 60px; min-width: 60px;">
            <i class="ti ti-package fs-2"></i>
          </div>
          <div>
            <p class="text-secondary mb-1 fw-medium">Total Produk</p>
            <h2 class="fw-bold mb-0">{{ number_format($totalProduk) }}</h2>
            <small class="text-success fw-semibold">Item Unik</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-12">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4">
          <div class="icon-shape bg-info text-white rounded-circle shadow-sm" style="width: 60px; height: 60px; min-width: 60px;">
            <i class="ti ti-history fs-2"></i>
          </div>
          <div>
            <p class="text-secondary mb-1 fw-medium">Total Analisis</p>
            <h2 class="fw-bold mb-0">{{ number_format($totalAnalisis) }}</h2>
            <small class="text-info fw-semibold">Riwayat Perhitungan</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-transparent px-4 py-3 d-flex justify-content-between align-items-center">
        <h3 class="h5 mb-0">Riwayat Analisis Terakhir</h3>
        <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-centered mb-0 text-nowrap">
            <thead class="bg-light">
              <tr>
                <th class="px-4">Tanggal</th>
                <th>Min. Support</th>
                <th>Min. Confidence</th>
                <th>Hasil Aturan</th>
                <th class="px-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAnalyses as $analysis)
              <tr>
                <td class="px-4">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-calendar me-2 text-primary"></i>
                    {{ $analysis->created_at->format('d M Y H:i') }}
                  </div>
                </td>
                <td>{{ $analysis->min_support }}%</td>
                <td>{{ $analysis->min_confidence }}%</td>
                <td>
                  <span class="badge bg-success-subtle text-success">
                    {{ $analysis->results_count }} Aturan
                  </span>
                </td>
                <td class="px-4">
                  <a href="#" class="btn btn-sm btn-primary">
                    <i class="ti ti-eye me-1"></i> Detail
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="ti ti-info-circle fs-2 d-block mb-2"></i>
                  Belum ada riwayat analisis.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
