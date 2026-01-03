<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cinema XXL - Tickets</title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
            color: #1a1a1a;
            font-size: 11px;
            line-height: 1.4;
        }

        /* Page Setup - Landscape A4 */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        /* Ticket Container */
        .ticket-container {
            width: 100%;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        /* Main Ticket Table */
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1a1a1a;
            background: #fff;
        }

        /* Main Part - Left 75% */
        .main-part {
            width: 75%;
            padding: 15px 20px;
            border-right: 3px dashed #666;
            vertical-align: top;
            position: relative;
        }

        /* Stub Part - Right 25% */
        .stub-part {
            width: 25%;
            padding: 15px;
            text-align: center;
            vertical-align: top;
            background: #f8f8f8;
        }

        /* Header Section */
        .ticket-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .cinema-logo {
            display: inline-block;
        }

        .cinema-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 1px;
        }

        .cinema-name .xxl {
            color: #d97706;
        }

        .ticket-type {
            float: right;
            background: #d97706;
            color: #fff;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Movie Info */
        .movie-section {
            margin-bottom: 12px;
        }

        .movie-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .movie-details {
            color: #666;
            font-size: 10px;
        }

        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 5px 15px 5px 0;
            width: 25%;
        }

        .info-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .info-value.large {
            font-size: 18px;
            color: #d97706;
        }

        /* QR Code Section */
        .qr-section {
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            text-align: center;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            background: #fff;
            padding: 5px;
            border: 1px solid #ddd;
        }

        .qr-code img {
            width: 70px;
            height: 70px;
        }

        /* Booking Code */
        .booking-code {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .booking-code-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
        }

        .booking-code-value {
            font-size: 11px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* Stub Section Styling */
        .stub-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .stub-cinema {
            font-size: 12px;
            font-weight: bold;
        }

        .stub-cinema .xxl {
            color: #d97706;
        }

        .stub-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        .stub-value {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .stub-seat {
            font-size: 24px;
            font-weight: bold;
            color: #d97706;
            margin: 10px 0;
        }

        .stub-movie {
            font-size: 10px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .stub-qr {
            margin: 10px auto;
            width: 60px;
            height: 60px;
        }

        .stub-qr img {
            width: 60px;
            height: 60px;
        }

        /* Footer */
        .ticket-footer {
            font-size: 8px;
            color: #999;
            text-align: center;
            margin-top: 8px;
        }

        /* Perforation Line Visual */
        .perforation {
            position: absolute;
            right: -2px;
            top: 0;
            bottom: 0;
            width: 3px;
            border-right: 3px dashed #666;
        }

        /* Print Info */
        .print-info {
            font-size: 9px;
            color: #999;
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        /* Scissors Icon */
        .scissors {
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    @foreach($bookings as $booking)
    <div class="ticket-container">
        <table class="ticket-table">
            <tr>
                {{-- MAIN PART (Left) --}}
                <td class="main-part">
                    {{-- Header --}}
                    <div class="ticket-header">
                        <span class="cinema-logo">
                            <span class="cinema-name">CINEMA <span class="xxl">XXL</span></span>
                        </span>
                        <span class="ticket-type">ADMISSION TICKET</span>
                    </div>

                    {{-- Movie Section --}}
                    <div class="movie-section">
                        <div class="movie-title">{{ $movieTitle }}</div>
                        <div class="movie-details">{{ $studio->type->value ?? 'Regular' }} Experience</div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-cell">
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ $showtime->start_time->format('d M Y') }}</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-label">Time</div>
                                <div class="info-value">{{ $showtime->start_time->format('H:i') }}</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-label">Studio</div>
                                <div class="info-value">{{ $studio->name }}</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-label">Seat</div>
                                <div class="info-value large">{{ $booking->seat_number }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Booking Code --}}
                    <div class="booking-code">
                        <span class="booking-code-label">Booking Code: </span>
                        <span class="booking-code-value">{{ $booking->booking_code }}</span>
                    </div>

                    {{-- QR Code --}}
                    <div class="qr-section">
                        <div class="qr-code">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($booking->booking_code) }}" alt="QR">
                        </div>
                    </div>

                    {{-- Scissors indicator --}}
                    <span class="scissors">✂</span>
                </td>

                {{-- STUB PART (Right - Tear Off) --}}
                <td class="stub-part">
                    <div class="stub-header">
                        <div class="stub-cinema">CINEMA <span class="xxl">XXL</span></div>
                    </div>

                    <div class="stub-movie">{{ Str::limit($movieTitle, 25) }}</div>

                    <div class="stub-label">Seat</div>
                    <div class="stub-seat">{{ $booking->seat_number }}</div>

                    <div class="stub-label">Date</div>
                    <div class="stub-value">{{ $showtime->start_time->format('d/m/Y') }}</div>

                    <div class="stub-label">Time</div>
                    <div class="stub-value">{{ $showtime->start_time->format('H:i') }}</div>

                    <div class="stub-label">Studio</div>
                    <div class="stub-value">{{ $studio->name }}</div>

                    <div class="stub-qr">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($booking->booking_code) }}" alt="QR">
                    </div>

                    <div class="ticket-footer">
                        Keep this stub
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach

    {{-- Print Information --}}
    <div class="print-info">
        <strong>Group Code:</strong> {{ $groupCode }} &nbsp;|&nbsp;
        <strong>Printed:</strong> {{ $printedAt->format('d M Y H:i') }} &nbsp;|&nbsp;
        <strong>Cashier:</strong> {{ $cashierName }} &nbsp;|&nbsp;
        <strong>Total Tickets:</strong> {{ count($bookings) }}
    </div>
</body>
</html>
