<?php

namespace App\Livewire\Pos;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Showtime;
use App\Services\TmdbService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.pos')]
#[Title('Ticket POS - Cinema XXL')]
class TicketPos extends Component
{
    // Search
    public string $search = '';

    /**
     * Check if current user can access Ticket POS.
     * Allowed: Admin, Manager (read), Cashier
     */
    public function mount(): void
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
        ])) {
            abort(403, 'You do not have permission to access Ticket POS.');
        }
    }

    // Modal States
    public bool $showShowtimeModal = false;
    public bool $showSeatModal = false;
    public bool $showCheckoutModal = false;
    public bool $showSuccessModal = false;

    // Selected Data
    public ?int $selectedMovieId = null;
    public ?string $selectedMovieTitle = null;
    public ?string $selectedMoviePoster = null;
    public ?int $selectedShowtimeId = null;
    public ?Showtime $selectedShowtime = null;

    // Seat Selection
    public array $selectedSeats = [];
    public array $occupiedSeats = [];
    public int $maxSeatsPerBooking = 10; // Higher limit for POS

    // Payment
    public string $paymentMethod = 'cash';

    // Success Data
    public ?string $lastGroupCode = null;
    public array $lastBookingCodes = [];
    public float $lastTotal = 0;

    /**
     * Get now playing movies from TMDb (cached).
     */
    #[Computed]
    public function movies(): array
    {
        return Cache::remember('tmdb_now_playing_pos', 3600, function () {
            $tmdb = app(TmdbService::class);
            $result = $tmdb->getNowPlaying(1);
            return $result['results'] ?? [];
        });
    }

    /**
     * Get filtered movies based on search.
     */
    #[Computed]
    public function filteredMovies(): array
    {
        $movies = $this->movies;
        
        if (empty($this->search)) {
            return $movies;
        }

        return array_filter($movies, function ($movie) {
            return stripos($movie['title'] ?? '', $this->search) !== false;
        });
    }

    /**
     * Get available showtimes for selected movie (today only).
     */
    #[Computed]
    public function availableShowtimes(): Collection
    {
        if (!$this->selectedMovieId) {
            return collect();
        }

        return Showtime::with('studio')
            ->where('tmdb_movie_id', $this->selectedMovieId)
            ->whereDate('start_time', today())
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get row letters for seat grid.
     */
    #[Computed]
    public function rowLetters(): array
    {
        return $this->selectedShowtime?->studio->getRowLetters() ?? [];
    }

    /**
     * Get column numbers for seat grid.
     */
    #[Computed]
    public function colNumbers(): array
    {
        return $this->selectedShowtime?->studio->getColNumbers() ?? [];
    }

    /**
     * Calculate ticket price.
     */
    #[Computed]
    public function ticketPrice(): float
    {
        if (!$this->selectedShowtime) {
            return 0;
        }
        return (float) $this->selectedShowtime->price * $this->selectedShowtime->studio->getPriceMultiplier();
    }

    /**
     * Calculate total price.
     */
    #[Computed]
    public function totalPrice(): float
    {
        return $this->ticketPrice * count($this->selectedSeats);
    }

    /**
     * Open showtime modal for a movie.
     */
    public function selectMovie(int $movieId, string $title, ?string $poster): void
    {
        $this->selectedMovieId = $movieId;
        $this->selectedMovieTitle = $title;
        $this->selectedMoviePoster = $poster;
        $this->showShowtimeModal = true;
    }

    /**
     * Close showtime modal.
     */
    public function closeShowtimeModal(): void
    {
        $this->showShowtimeModal = false;
        $this->selectedMovieId = null;
        $this->selectedMovieTitle = null;
        $this->selectedMoviePoster = null;
    }

    /**
     * Select a showtime and open seat selection.
     */
    public function selectShowtime(int $showtimeId): void
    {
        $this->selectedShowtimeId = $showtimeId;
        $this->selectedShowtime = Showtime::with('studio')->find($showtimeId);
        
        // Load occupied seats
        $this->refreshOccupiedSeats();
        
        // Clear previous selection
        $this->selectedSeats = [];
        
        // Close showtime modal, open seat modal
        $this->showShowtimeModal = false;
        $this->showSeatModal = true;
    }

    /**
     * Refresh occupied seats from database.
     */
    private function refreshOccupiedSeats(): void
    {
        if (!$this->selectedShowtime) {
            $this->occupiedSeats = [];
            return;
        }

        $this->occupiedSeats = $this->selectedShowtime->bookings()
            ->whereIn('status', [
                BookingStatus::Booked->value,
                BookingStatus::Paid->value,
                BookingStatus::Redeemed->value,
            ])
            ->pluck('seat_number')
            ->toArray();
    }

    /**
     * Get seat status.
     */
    public function getSeatStatus(string $seat): string
    {
        if (in_array($seat, $this->occupiedSeats)) {
            return 'occupied';
        }
        
        if (in_array($seat, $this->selectedSeats)) {
            return 'selected';
        }
        
        return 'available';
    }

    /**
     * Toggle seat selection.
     */
    public function toggleSeat(string $seatNumber): void
    {
        if (in_array($seatNumber, $this->occupiedSeats)) {
            $this->dispatch('show-toast', type: 'error', message: 'Seat already booked');
            return;
        }

        if (in_array($seatNumber, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_filter(
                $this->selectedSeats,
                fn($s) => $s !== $seatNumber
            ));
            return;
        }

        if (count($this->selectedSeats) >= $this->maxSeatsPerBooking) {
            $this->dispatch('show-toast', type: 'error', message: "Maximum {$this->maxSeatsPerBooking} seats allowed");
            return;
        }

        $this->selectedSeats[] = $seatNumber;
        sort($this->selectedSeats);
    }

    /**
     * Clear seat selection.
     */
    public function clearSelection(): void
    {
        $this->selectedSeats = [];
    }

    /**
     * Close seat modal.
     */
    public function closeSeatModal(): void
    {
        $this->showSeatModal = false;
        $this->selectedSeats = [];
        $this->selectedShowtimeId = null;
        $this->selectedShowtime = null;
    }

    /**
     * Proceed to checkout.
     */
    public function proceedToCheckout(): void
    {
        if (empty($this->selectedSeats)) {
            $this->dispatch('show-toast', type: 'error', message: 'Please select at least one seat');
            return;
        }

        $this->showSeatModal = false;
        $this->showCheckoutModal = true;
    }

    /**
     * Go back to seat selection.
     */
    public function backToSeats(): void
    {
        $this->showCheckoutModal = false;
        $this->showSeatModal = true;
    }

    /**
     * Close checkout modal.
     */
    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
        $this->selectedSeats = [];
        $this->selectedShowtimeId = null;
        $this->selectedShowtime = null;
        $this->paymentMethod = 'cash';
    }

    /**
     * Process payment and create bookings.
     */
    public function processPayment(): void
    {
        if (empty($this->selectedSeats) || !$this->selectedShowtime) {
            $this->dispatch('show-toast', type: 'error', message: 'Invalid booking data');
            return;
        }

        try {
            DB::transaction(function () {
                // Check for race condition
                $takenSeats = Booking::where('showtime_id', $this->selectedShowtime->id)
                    ->whereIn('seat_number', $this->selectedSeats)
                    ->whereIn('status', [
                        BookingStatus::Booked->value,
                        BookingStatus::Paid->value,
                        BookingStatus::Redeemed->value,
                    ])
                    ->lockForUpdate()
                    ->pluck('seat_number')
                    ->toArray();

                if (!empty($takenSeats)) {
                    $seatList = implode(', ', $takenSeats);
                    throw new \Exception("Seat(s) {$seatList} already booked!");
                }

                // Generate group code
                $groupCode = 'POS-' . strtoupper(bin2hex(random_bytes(4))) . '-' . now()->format('dmy');
                $bookingCodes = [];

                // Create bookings for each seat
                foreach ($this->selectedSeats as $index => $seat) {
                    $bookingCode = $groupCode . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    
                    Booking::create([
                        'booking_code' => $bookingCode,
                        'user_id' => null, // Walk-in customer
                        'cashier_id' => Auth::id(),
                        'showtime_id' => $this->selectedShowtime->id,
                        'seat_number' => $seat,
                        'status' => BookingStatus::Paid->value, // Already paid
                        'payment_method' => $this->paymentMethod,
                        'booking_time' => now(),
                        'total_price' => $this->ticketPrice,
                    ]);

                    $bookingCodes[] = $bookingCode;
                }

                // Store success data
                $this->lastGroupCode = $groupCode;
                $this->lastBookingCodes = $bookingCodes;
                $this->lastTotal = $this->totalPrice;
            });

            // Show success modal
            $this->showCheckoutModal = false;
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            $this->refreshOccupiedSeats();
            $this->selectedSeats = array_values(array_filter(
                $this->selectedSeats,
                fn($s) => !in_array($s, $this->occupiedSeats)
            ));
            
            $this->dispatch('show-toast', type: 'error', message: $e->getMessage());
        }
    }

    /**
     * Print tickets and close.
     */
    public function printTickets(): void
    {
        // Open print window with all booking codes
        $this->dispatch('print-tickets', codes: $this->lastBookingCodes);
    }

    /**
     * Close success modal and reset.
     */
    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->reset([
            'selectedMovieId',
            'selectedMovieTitle',
            'selectedMoviePoster',
            'selectedShowtimeId',
            'selectedShowtime',
            'selectedSeats',
            'occupiedSeats',
            'paymentMethod',
            'lastGroupCode',
            'lastBookingCodes',
            'lastTotal',
        ]);
    }

    /**
     * New transaction after success.
     */
    public function newTransaction(): void
    {
        $this->closeSuccessModal();
    }

    public function render()
    {
        return view('livewire.pos.ticket-pos');
    }
}
