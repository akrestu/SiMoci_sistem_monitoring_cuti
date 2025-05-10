@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Deadline Pemesanan Tiket</span>
                    <a href="{{ route('transportasi_detail.dashboard') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Tiket yang Perlu Segera Dipesan</h5>
                        <p class="text-muted">Daftar tiket dengan deadline pemesanan dalam 10 hari ke depan</p>
                    </div>

                    @if($upcomingDeadlines->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Departemen</th>
                                        <th>Jenis</th>
                                        <th>Rute</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Urgency</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingDeadlines as $ticket)
                                        @php
                                            $deadlineDate = $ticket->jenis_perjalanan == 'pergi' 
                                                ? Carbon\Carbon::parse($ticket->cuti->tanggal_mulai)
                                                : Carbon\Carbon::parse($ticket->cuti->tanggal_selesai);
                                            
                                            $daysUntilDeadline = (int)now()->diffInDays($deadlineDate);
                                            $urgencyClass = $daysUntilDeadline <= 3 ? 'danger' : ($daysUntilDeadline <= 7 ? 'warning' : 'info');
                                        @endphp
                                        <tr>
                                            <td>{{ $ticket->cuti->karyawan->nama }}</td>
                                            <td>{{ $ticket->cuti->karyawan->departemen }}</td>
                                            <td>
                                                {{ ucfirst($ticket->jenis_perjalanan) }} - 
                                                {{ $ticket->transportasi->jenis ?? 'Belum ditetapkan' }}
                                            </td>
                                            <td>{{ $ticket->rute_asal }} - {{ $ticket->rute_tujuan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($deadlineDate)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($deadlineDate)->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $urgencyClass }}">
                                                    {{ $daysUntilDeadline }} hari lagi
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('transportasi_detail.edit', $ticket->id) }}" 
                                                    class="btn btn-sm btn-primary">
                                                    Proses
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Tidak ada tiket yang perlu segera dipesan dalam 10 hari ke depan.
                        </div>
                    @endif

                    <div class="mt-5">
                        <h5>Biaya Transportasi Tahun {{ date('Y') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Bulan</th>
                                                <th>Total Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalBiaya = 0;
                                                $bulanNames = [
                                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
                                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 
                                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                                ];
                                            @endphp
                                            
                                            @foreach($biayaTransportasi as $biaya)
                                                @php $totalBiaya += $biaya->total; @endphp
                                                <tr>
                                                    <td>{{ $bulanNames[$biaya->bulan] }}</td>
                                                    <td>Rp {{ number_format($biaya->total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                            
                                            <tr class="fw-bold">
                                                <td>Total</td>
                                                <td>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Placeholder for future chart -->
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <p class="text-muted">Graf biaya transportasi akan ditambahkan di sini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 