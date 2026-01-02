<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('My Profile - Cinema XXL')]
class UserProfile extends Component
{
    public string $activeTab = 'tickets';
    
    // Profile form fields
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public ?string $date_of_birth = null;
    
    // Password change fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    
    // QR Modal state
    public bool $showQrModal = false;
    public ?string $qrBookingCode = null;
    public ?string $qrSeatNumber = null;
    public ?string $qrMovieTitle = null;
    public ?string $qrShowtime = null;
    public ?string $qrStudio = null;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            $this->redirect(route('login'));
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number ?? '';
        $this->date_of_birth = $user->date_of_birth instanceof \DateTimeInterface 
            ? $user->date_of_birth->format('Y-m-d') 
            : null;

        // Handle tab from query string
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['tickets', 'watchlist', 'settings'])) {
            $this->activeTab = $tab;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getActiveBookingsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->bookings()
            ->with(['showtime.studio'])
            ->whereHas('showtime', function ($query) {
                $query->where('start_time', '>=', now());
            })
            ->whereIn('status', ['booked', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPastBookingsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->bookings()
            ->with(['showtime.studio'])
            ->whereHas('showtime', function ($query) {
                $query->where('start_time', '<', now());
            })
            ->orWhere('status', 'redeemed')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    public function getWatchlistProperty(): \Illuminate\Database\Eloquent\Collection
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->watchlists()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateProfile(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ]);

        $user->update($validated);

        $this->dispatch('notify', type: 'success', message: 'Profile updated successfully!');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        
        $this->dispatch('notify', type: 'success', message: 'Password changed successfully!');
    }

    public function removeFromWatchlist(int $id): void
    {
        $watchlist = Watchlist::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($watchlist) {
            $watchlist->delete();
            $this->dispatch('notify', type: 'success', message: 'Removed from watchlist.');
        }
    }

    /**
     * Open QR code modal for a booking.
     */
    public function openQrModal(string $bookingCode): void
    {
        $booking = Booking::with(['showtime.studio'])
            ->where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->first();

        if ($booking) {
            $this->qrBookingCode = $booking->booking_code;
            $this->qrSeatNumber = $booking->seat_number;
            $this->qrShowtime = $booking->showtime->start_time->format('d M Y, H:i');
            $this->qrMovieTitle = $this->getMovieTitle($booking->showtime->tmdb_movie_id);
            $this->qrStudio = $booking->showtime->studio->name ?? '-';
            $this->showQrModal = true;
        }
    }

    /**
     * Close QR modal.
     */
    public function closeQrModal(): void
    {
        $this->showQrModal = false;
        $this->qrBookingCode = null;
        $this->qrSeatNumber = null;
        $this->qrMovieTitle = null;
        $this->qrShowtime = null;
        $this->qrStudio = null;
    }

    /**
     * Get movie title from TMDb.
     */
    private function getMovieTitle(int $tmdbId): string
    {
        $apiKey = config('services.tmdb.api_key');
        
        if (!$apiKey) {
            return 'Movie';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get(
                "https://api.themoviedb.org/3/movie/{$tmdbId}",
                ['api_key' => $apiKey]
            );

            if ($response->successful()) {
                return $response->json()['title'] ?? 'Movie';
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return 'Movie';
    }

    public function render()
    {
        return view('livewire.user-profile', [
            'user' => Auth::user(),
            'activeBookings' => $this->activeBookings,
            'pastBookings' => $this->pastBookings,
            'watchlist' => $this->watchlist,
        ]);
    }
}
