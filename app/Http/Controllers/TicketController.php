<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Download ticket as PDF with QR Code.
     */
    public function download(string $booking_code)
    {
        // Find booking with relationships
        $booking = Booking::with(['showtime.studio', 'user'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        // Authorization check - ensure user owns this ticket
        if (Auth::id() !== $booking->user_id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Generate QR Code URL using goqr.me API (reliable and free)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($booking->booking_code);

        // Get movie details from TMDb (cached in showtime)
        $movieData = $this->getMovieData($booking->showtime->tmdb_movie_id);

        // Prepare data for PDF
        $data = [
            'booking' => $booking,
            'showtime' => $booking->showtime,
            'studio' => $booking->showtime->studio,
            'qrCodeUrl' => $qrCodeUrl,
            'movieTitle' => $movieData['title'] ?? 'Movie',
            'moviePoster' => $movieData['poster'] ?? null,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.ticket', $data);
        
        // Set paper size (A6 for ticket-style, or custom)
        $pdf->setPaper([0, 0, 396, 612], 'portrait'); // ~5.5 x 8.5 inches (half letter)

        // Download filename
        $filename = 'ticket-' . $booking->booking_code . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream ticket PDF (view in browser).
     */
    public function view(string $booking_code)
    {
        // Find booking with relationships
        $booking = Booking::with(['showtime.studio', 'user'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        // Authorization check
        if (Auth::id() !== $booking->user_id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Generate QR Code URL using goqr.me API
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($booking->booking_code);

        // Get movie details
        $movieData = $this->getMovieData($booking->showtime->tmdb_movie_id);

        $data = [
            'booking' => $booking,
            'showtime' => $booking->showtime,
            'studio' => $booking->showtime->studio,
            'qrCodeUrl' => $qrCodeUrl,
            'movieTitle' => $movieData['title'] ?? 'Movie',
            'moviePoster' => $movieData['poster'] ?? null,
        ];

        $pdf = Pdf::loadView('pdf.ticket', $data);
        $pdf->setPaper([0, 0, 396, 612], 'portrait');

        return $pdf->stream('ticket-' . $booking->booking_code . '.pdf');
    }

    /**
     * Get movie data from TMDb API.
     */
    private function getMovieData(int $tmdbId): array
    {
        $apiKey = config('services.tmdb.api_key');
        
        if (!$apiKey) {
            return ['title' => 'Movie', 'poster' => null];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get(
                "https://api.themoviedb.org/3/movie/{$tmdbId}",
                ['api_key' => $apiKey]
            );

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'title' => $data['title'] ?? 'Movie',
                    'poster' => $data['poster_path'] 
                        ? 'https://image.tmdb.org/t/p/w200' . $data['poster_path'] 
                        : null,
                ];
            }
        } catch (\Exception $e) {
            // Fallback on error
        }

        return ['title' => 'Movie', 'poster' => null];
    }
}
