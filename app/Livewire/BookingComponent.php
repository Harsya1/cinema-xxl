<?php

namespace App\Livewire;

use App\Enums\BookingStatus;
use App\Enums\StudioType;
use App\Models\Booking;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Select Seats - Cinema XXL')]
class BookingComponent extends Component
{
    // Showtime data
    public Showtime $showtime;
    public array $occupiedSeats = [];
    
    // User selection
    public array $selectedSeats = [];
    
    // Limits
    public int $maxSeatsPerBooking = 6;
    
    // UI State
    public bool $showSuccessModal = false;
    public ?string $lastBookingCode = null;
    public float $lastBookingTotal = 0;
    public int $lastPointsEarned = 0;
    public bool $isProcessing = false;

    /**
     * Points earned per ticket based on studio type.
     */
    private const POINTS_PER_TICKET = [
        'Regular' => 10,
        '3D' => 13,
        'Premier' => 15,
    ];

    /**
     * Mount the component with showtime data.
     */
    public function mount(int $showtime_id): void
    {
        $this->showtime = Showtime::with(['studio'])->findOrFail($showtime_id);
        
        // Check if showtime is still available
        if ($this->showtime->start_time < now()) {
            session()->flash('error', 'This showtime has already started.');
            $this->redirect(route('movie.details', $this->showtime->tmdb_movie_id));
            return;
        }

        // Fetch occupied seats
        $this->refreshOccupiedSeats();
    }

    /**
     * Refresh occupied seats from database.
     */
    private function refreshOccupiedSeats(): void
    {
        $this->occupiedSeats = $this->showtime->bookings()
            ->whereIn('status', [
                BookingStatus::Booked->value,
                BookingStatus::Paid->value,
                BookingStatus::Redeemed->value,
            ])
            ->pluck('seat_number')
            ->toArray();
    }

    /**
     * Get studio details.
     */
    #[Computed]
    public function studio()
    {
        return $this->showtime->studio;
    }

    /**
     * Get row letters for the seat grid.
     */
    #[Computed]
    public function rowLetters(): array
    {
        return $this->studio->getRowLetters();
    }

    /**
     * Get column numbers for the seat grid.
     */
    #[Computed]
    public function colNumbers(): array
    {
        return $this->studio->getColNumbers();
    }

    /**
     * Check if studio is Premier type.
     */
    #[Computed]
    public function isPremier(): bool
    {
        return $this->studio->type === StudioType::Premier;
    }

    /**
     * Check if studio is 3D type.
     */
    #[Computed]
    public function is3D(): bool
    {
        return $this->studio->type === StudioType::ThreeD;
    }

    /**
     * Calculate base price (showtime price * studio multiplier).
     */
    #[Computed]
    public function ticketPrice(): float
    {
        return (float) $this->showtime->price * $this->studio->getPriceMultiplier();
    }

    /**
     * Calculate total price for selected seats.
     */
    #[Computed]
    public function totalPrice(): float
    {
        return $this->ticketPrice * count($this->selectedSeats);
    }

    /**
     * Calculate points to be earned for current selection.
     */
    #[Computed]
    public function pointsToEarn(): int
    {
        $studioType = $this->studio->type->value ?? 'Regular';
        $pointsPerTicket = self::POINTS_PER_TICKET[$studioType] ?? 10;
        return $pointsPerTicket * count($this->selectedSeats);
    }

    /**
     * Get seat status for display.
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
        // Check if seat is occupied (double validation)
        if (in_array($seatNumber, $this->occupiedSeats)) {
            $this->dispatch('show-toast', type: 'error', message: 'This seat is already taken');
            return;
        }

        // If seat is already selected, remove it
        if (in_array($seatNumber, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_filter(
                $this->selectedSeats, 
                fn($s) => $s !== $seatNumber
            ));
            return;
        }

        // Check max seats limit
        if (count($this->selectedSeats) >= $this->maxSeatsPerBooking) {
            $this->dispatch('show-toast', type: 'warning', message: "Maximum {$this->maxSeatsPerBooking} seats allowed per booking");
            return;
        }

        // Add seat to selection
        $this->selectedSeats[] = $seatNumber;
        
        // Sort seats for better display (A1, A2, B1, B2, etc.)
        sort($this->selectedSeats);
    }

    /**
     * Clear all selected seats.
     */
    public function clearSelection(): void
    {
        $this->selectedSeats = [];
    }

    /**
     * Process the booking with race condition check.
     */
    public function bookTickets(): void
    {
        // Validate user is logged in
        if (!Auth::check()) {
            session()->flash('error', 'Please login to book tickets.');
            $this->redirect(route('login'));
            return;
        }

        // Validate selection
        if (empty($this->selectedSeats)) {
            $this->dispatch('show-toast', type: 'error', message: 'Please select at least one seat');
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                // CRUCIAL: Final check for race condition
                // Lock the bookings table for this showtime during check
                $takenSeats = Booking::where('showtime_id', $this->showtime->id)
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
                    // Some seats were just taken!
                    $seatList = implode(', ', $takenSeats);
                    throw new \Exception("Seat(s) {$seatList} just got booked by someone else! Please select different seats.");
                }

                // Generate unique group code for this booking transaction
                $groupCode = 'TKT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . now()->format('dmy');

                // Create bookings for each seat with unique booking codes
                foreach ($this->selectedSeats as $index => $seat) {
                    // Each seat gets unique booking code: GROUP-SEATINDEX
                    $bookingCode = $groupCode . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    
                    Booking::create([
                        'booking_code' => $bookingCode,
                        'user_id' => Auth::id(),
                        'showtime_id' => $this->showtime->id,
                        'seat_number' => $seat,
                        'status' => BookingStatus::Booked->value,
                        'booking_time' => now(),
                        'total_price' => $this->ticketPrice,
                    ]);
                }

                // Calculate and add points to user
                $studioType = $this->studio->type->value ?? 'Regular';
                $pointsPerTicket = self::POINTS_PER_TICKET[$studioType] ?? 10;
                $totalPoints = $pointsPerTicket * count($this->selectedSeats);

                // Increment user points
                $user = User::find(Auth::id());
                $user->increment('points', $totalPoints);

                // Store for success modal (show the group code for user reference)
                $this->lastBookingCode = $groupCode;
                $this->lastBookingTotal = $this->totalPrice;
                $this->lastPointsEarned = $totalPoints;

                // Clear selection
                $this->selectedSeats = [];
                
                // Refresh occupied seats
                $this->refreshOccupiedSeats();

                // Show success modal
                $this->showSuccessModal = true;
            });

        } catch (\Exception $e) {
            // Refresh occupied seats in case of race condition
            $this->refreshOccupiedSeats();
            
            // Remove any seats that are now taken from selection
            $this->selectedSeats = array_values(array_filter(
                $this->selectedSeats,
                fn($s) => !in_array($s, $this->occupiedSeats)
            ));

            $this->dispatch('show-toast', type: 'error', message: $e->getMessage());
        }

        $this->isProcessing = false;
    }

    /**
     * Close success modal and optionally redirect.
     */
    public function closeSuccessModal(bool $redirect = false): void
    {
        $this->showSuccessModal = false;
        
        if ($redirect) {
            $this->redirect(route('profile') . '#tickets');
        }
    }

    /**
     * Go back to movie details.
     */
    public function goBack(): void
    {
        $this->redirect(route('movie.details', $this->showtime->tmdb_movie_id));
    }

    public function render()
    {
        return view('livewire.booking-component');
    }
}
