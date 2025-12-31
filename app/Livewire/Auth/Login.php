<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Check if user has 'user' role - staff/admin must use /admin
        if (Auth::user()->role !== 'user') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Staff dan Admin harus login melalui halaman Admin Panel.',
            ]);
        }

        Session::regenerate();

        $this->redirect(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
