<div class="w-full max-w-md">
    {{-- Card --}}
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Forgot Password?</h1>
            <p class="text-gray-400">No worries! Enter your email and we'll send you a reset link.</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-400 text-sm">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <input 
                    wire:model="email" 
                    id="email" 
                    type="email" 
                    required 
                    autofocus
                    class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit"
                class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-amber-500/25 flex items-center justify-center gap-2"
            >
                <span wire:loading.remove wire:target="sendPasswordResetLink">Send Reset Link</span>
                <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        </form>

        {{-- Back to Login --}}
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Sign In
            </a>
        </div>
    </div>
</div>
