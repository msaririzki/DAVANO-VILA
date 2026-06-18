@php
    $formatCurrency = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
    $nights = max(1, $booking->check_in_date->diffInDays($booking->check_out_date));
    $categoryLabels = \App\Models\BookingAddon::categoryLabels();
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tagihan {{ $booking->booking_code }}</title>
    <style>
        @page { margin: 32px 36px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #1f1f1f;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }
        .topbar {
            border-bottom: 4px solid #b88943;
            padding-bottom: 18px;
        }
        .brand {
            display: table;
            width: 100%;
        }
        .brand-left,
        .brand-right {
            display: table-cell;
            vertical-align: top;
        }
        .brand-right {
            text-align: right;
        }
        .logo {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            vertical-align: middle;
        }
        .brand-title {
            display: inline-block;
            margin-left: 12px;
            vertical-align: middle;
        }
        h1 {
            margin: 0;
            color: #0b513d;
            font-size: 27px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .muted { color: #777; }
        .invoice-title {
            margin: 0 0 6px;
            color: #0b513d;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .box {
            margin-top: 18px;
            border: 1px solid #e6e0d7;
            border-radius: 12px;
            padding: 16px;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .col + .col {
            padding-left: 18px;
        }
        .label {
            color: #777;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .value {
            margin: 4px 0 12px;
            font-size: 13px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            border-bottom: 1px solid #d9d0c3;
            color: #73542a;
            font-size: 10px;
            letter-spacing: 1px;
            padding: 9px 6px;
            text-align: left;
            text-transform: uppercase;
        }
        td {
            border-bottom: 1px solid #eee8df;
            padding: 9px 6px;
            vertical-align: top;
        }
        .right { text-align: right; }
        .category {
            background: #f7f1e8;
            color: #7c5725;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .summary {
            margin-left: auto;
            margin-top: 18px;
            width: 48%;
        }
        .summary-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #eee8df;
            padding: 7px 0;
        }
        .summary-row span {
            display: table-cell;
        }
        .summary-row span:last-child {
            text-align: right;
            font-weight: 700;
        }
        .summary-total {
            margin-top: 8px;
            border-radius: 10px;
            background: #0b513d;
            color: white;
            padding: 12px;
        }
        .summary-total .summary-row {
            border: 0;
            padding: 2px 0;
        }
        .footer {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .note,
        .signature-block {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }
        .signature-block {
            text-align: center;
        }
        .signature {
            max-width: 160px;
            max-height: 90px;
        }
        .sign-line {
            border-top: 1px solid #333;
            display: inline-block;
            margin-top: 6px;
            padding-top: 6px;
            min-width: 180px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">
            <div class="brand-left">
                @if ($logoDataUri)
                    <img src="{{ $logoDataUri }}" class="logo" alt="Villa Dafano">
                @endif
                <div class="brand-title">
                    <h1>Villa Dafano</h1>
                    <div class="muted">Sembalun, Lombok Timur</div>
                </div>
            </div>
            <div class="brand-right">
                <p class="invoice-title">Tagihan</p>
                <div><strong>{{ $booking->booking_code }}</strong></div>
                <div class="muted">{{ now()->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="box grid">
        <div class="col">
            <div class="label">Nama Tamu</div>
            <div class="value">{{ $booking->guest_name }}</div>
            <div class="label">WhatsApp</div>
            <div class="value">{{ $booking->guest_phone }}</div>
            <div class="label">Penghuni</div>
            <div class="value">Dewasa {{ $booking->adult_count }} · Anak {{ $booking->child_count }}</div>
            <div class="label">Kamar</div>
            <div class="value">{{ $booking->room->name }}</div>
        </div>
        <div class="col">
            <div class="label">Tanggal Masuk</div>
            <div class="value">{{ $booking->check_in_date->format('d M Y') }} 14:00</div>
            <div class="label">Check-out</div>
            <div class="value">{{ $booking->check_out_date->format('d M Y') }} 12:00</div>
            <div class="label">Durasi</div>
            <div class="value">{{ $nights }} malam</div>
            <div class="label">Unit</div>
            <div class="value">{{ $booking->unit_count }} unit{{ $booking->units->isNotEmpty() ? ' · '.$booking->units->pluck('name')->implode(', ') : '' }}</div>
        </div>
    </div>

    <div class="box">
        <div class="label">Rincian Tagihan</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $booking->room->name }} ({{ $nights }} malam)</td>
                    <td class="right">{{ $booking->unit_count }} x {{ $nights }}</td>
                    <td class="right">{{ $formatCurrency($booking->room->price) }}</td>
                    <td class="right">{{ $formatCurrency($booking->total_room_price) }}</td>
                </tr>
                @if ((float) $booking->occupancy_adjustment_amount > 0)
                    <tr>
                        <td>Biaya tambahan penghuni{{ $booking->occupancy_adjustment_note ? ' - '.$booking->occupancy_adjustment_note : '' }}</td>
                        <td class="right">1</td>
                        <td class="right">{{ $formatCurrency($booking->occupancy_adjustment_amount) }}</td>
                        <td class="right">{{ $formatCurrency($booking->occupancy_adjustment_amount) }}</td>
                    </tr>
                @endif

                @foreach ($groupedAddons as $category => $addons)
                    <tr>
                        <td colspan="4" class="category">{{ $categoryLabels[$category] ?? ucfirst((string) $category) }}</td>
                    </tr>
                    @foreach ($addons as $addon)
                        <tr>
                            <td>{{ $addon->item_name }}</td>
                            <td class="right">{{ $addon->qty }}</td>
                            <td class="right">{{ $formatCurrency($addon->price) }}</td>
                            <td class="right">{{ $formatCurrency($addon->subtotal) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row"><span>Harga kamar</span><span>{{ $formatCurrency($booking->total_room_price) }}</span></div>
            <div class="summary-row"><span>Pesanan tambahan</span><span>{{ $formatCurrency($booking->total_addons_price) }}</span></div>
            <div class="summary-row"><span>Biaya penghuni</span><span>{{ $formatCurrency($booking->occupancy_adjustment_amount) }}</span></div>
            <div class="summary-row"><span>Denda keterlambatan</span><span>{{ $formatCurrency($booking->late_fee) }}</span></div>
            <div class="summary-row"><span>Diskon</span><span>- {{ $formatCurrency($booking->discount_amount) }}</span></div>
            <div class="summary-row"><span>Sudah dibayar / DP</span><span>{{ $formatCurrency($booking->paid_amount) }}</span></div>
            <div class="summary-total">
                <div class="summary-row"><span>Total Tagihan</span><span>{{ $formatCurrency($booking->grand_total) }}</span></div>
                <div class="summary-row"><span>Sisa Tagihan</span><span>{{ $formatCurrency($booking->balance_due) }}</span></div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="note">
            <div class="label">Catatan</div>
            <p class="muted">
                Tagihan ini diterbitkan oleh Villa Dafano berdasarkan data transaksi di sistem.
                Kamar dan pesanan dianggap selesai setelah pembayaran tervalidasi oleh Super Admin.
            </p>
        </div>
        <div class="signature-block">
            <div>Hormat kami,</div>
            @if ($signatureDataUri)
                <img src="{{ $signatureDataUri }}" class="signature" alt="Tanda tangan">
            @endif
            <div class="sign-line">Villa Dafano</div>
        </div>
    </div>
</body>
</html>
