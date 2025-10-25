@extends('layouts.base')

@push('styles')
    <style>
        body.app-body {
            background: #ffffff;
        }
        .print-wrapper {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            color: #101923;
        }
        .print-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .print-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .print-header p {
            margin: 4px 0 0;
            color: #5a6a90;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #d6deff;
            padding: 8px 10px;
        }
        th {
            background: #eef3ff;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: .04em;
            color: #23345d;
        }
        .status {
            font-weight: 600;
        }
        .status.used {
            color: #2e7d32;
        }
        .status.unused {
            color: #b71c1c;
        }
        @media print {
            body.app-body {
                padding: 0;
                background: #fff;
            }
            .app-background {
                display: none;
            }
        }
    </style>
@endpush

@section('body')
    <div class="print-wrapper">
        <div class="print-header">
            <h1>Daftar Token Pemilos</h1>
            <p>Status: {{ $status === 'used' ? 'Sudah digunakan' : ($status === 'unused' ? 'Belum digunakan' : 'Semua token') }}</p>
            <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>
        </div>
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>Kode Token</th>
                <th>Status</th>
                <th>Paslon Dipilih</th>
                <th>Catatan</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tokens as $token)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $token->code }}</td>
                    <td>
                        @if ($token->isUsed())
                            <span class="status used">Digunakan ({{ optional($token->used_at)->format('d M Y H:i') }})</span>
                        @else
                            <span class="status unused">Belum</span>
                        @endif
                    </td>
                    <td>{{ optional($token->paslon)->display_name ?? optional($token->paslon)->name ?? '-' }}</td>
                    <td>{{ $token->note ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
@endpush
