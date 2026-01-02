<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema XXL Ticket - {{ $booking->booking_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #1a1a2e;
            color: #ffffff;
            padding: 20px;
        }

        .ticket-container {
            max-width: 380px;
            margin: 0 auto;
            background: linear-gradient(135deg, #16213e 0%, #0f3460 100%);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        /* Header Section */
        .ticket-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 20'%3E%3Cpath fill='%2316213e' d='M0 20 Q25 0 50 20 Q75 0 100 20 L100 20 L0 20Z'/%3E%3C/svg%3E") repeat-x;
            background-size: 50px 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 2px;
        }

        .logo span {
            color: #f59e0b;
        }

        .admit-text {
            font-size: 12px;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 5px;
            font-weight: 600;
        }

        /* Movie Info Section */
        .movie-section {
            padding: 30px 25px 20px;
            text-align: center;
            border-bottom: 2px dashed #374151;
        }

        .movie-title {
            font-size: 22px;
            font-weight: 700;
            color: #f59e0b;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .movie-subtitle {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Details Grid */
        .details-section {
            padding: 20px 25px;
            display: table;
            width: 100%;
        }

        .details-row {
            display: table-row;
        }

        .detail-box {
            display: table-cell;
            padding: 10px 5px;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .detail-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }

        .detail-value.large {
            font-size: 28px;
            color: #f59e0b;
        }

        /* Seat Highlight */
        .seat-section {
            background: rgba(245, 158, 11, 0.1);
            padding: 20px 25px;
            text-align: center;
            border-top: 2px dashed #374151;
            border-bottom: 2px dashed #374151;
        }

        .seat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }

        .seat-number {
            font-size: 48px;
            font-weight: 800;
            color: #f59e0b;
            letter-spacing: 5px;
        }

        /* QR Section */
        .qr-section {
            padding: 20px;
            text-align: center;
            background: #ffffff;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 10px;
            padding: 3px;
            background: #ffffff;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
        }

        .booking-code {
            font-size: 10px;
            color: #1a1a2e;
            font-weight: 700;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
        }

        .scan-text {
            font-size: 8px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Footer */
        .ticket-footer {
            background: #0f0f23;
            padding: 15px 25px;
            text-align: center;
        }

        .footer-text {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer-text strong {
            color: #f59e0b;
        }

        /* Decorative circles (punch holes) */
        .punch-hole-left,
        .punch-hole-right {
            position: absolute;
            width: 30px;
            height: 30px;
            background: #1a1a2e;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .punch-hole-left {
            left: -15px;
        }

        .punch-hole-right {
            right: -15px;
        }

        .divider-section {
            position: relative;
            height: 0;
        }

        /* Studio Badge */
        .studio-badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Header -->
        <div class="ticket-header">
            <div class="logo">CINEMA<span>XXL</span></div>
            <div class="admit-text">★ Admit One ★</div>
        </div>

        <!-- Movie Title -->
        <div class="movie-section">
            <div class="movie-subtitle">Now Showing</div>
            <div class="movie-title">{{ $movieTitle }}</div>
            <div class="studio-badge">{{ $studio->name }} • {{ $studio->type->value ?? 'Regular' }}</div>
        </div>

        <!-- Details Grid -->
        <div class="details-section">
            <div class="details-row">
                <div class="detail-box">
                    <div class="detail-label">Date</div>
                    <div class="detail-value">{{ $showtime->start_time->format('d M Y') }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Time</div>
                    <div class="detail-value">{{ $showtime->start_time->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Divider with punch holes -->
        <div class="divider-section">
            <div class="punch-hole-left"></div>
            <div class="punch-hole-right"></div>
        </div>

        <!-- Seat Number -->
        <div class="seat-section">
            <div class="seat-label">Your Seat</div>
            <div class="seat-number">{{ $booking->seat_number }}</div>
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            <div class="qr-code">
                <img src="{{ $qrCodeUrl }}" alt="QR Code">
            </div>
            <div class="booking-code">{{ $booking->booking_code }}</div>
            <div class="scan-text">Scan this QR code at the entrance</div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            <div class="footer-text">
                <strong>Cinema XXL</strong> - Experience Movies Like Never Before<br>
                Please arrive 15 minutes before showtime. This ticket is non-refundable.<br>
                © {{ date('Y') }} Cinema XXL. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
