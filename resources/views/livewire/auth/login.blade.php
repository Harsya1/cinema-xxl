<div class="w-full max-w-md">
    {{-- Card --}}
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-gray-400">Sign in to continue to Cinema XXL</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                <p class="text-green-400 text-sm">{{ session('status') }}</p>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit="login" class="space-y-6">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                <input 
                    wire:model="email" 
                    id="email" 
                    type="email" 
                    required 
                    autofocus 
                    autocomplete="username"
                    class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <a href="{{ route('password.request') }}" class="text-sm text-amber-400 hover:text-amber-300 transition-colors">
                        Forgot password?
                    </a>
                </div>
                <input 
                    wire:model="password" 
                    id="password" 
                    type="password" 
                    required 
                    autocomplete="current-password"
                    class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center">
                <input 
                    wire:model="remember" 
                    id="remember" 
                    type="checkbox"
                    class="w-4 h-4 bg-gray-900 border-gray-600 rounded text-amber-500 focus:ring-amber-500 focus:ring-offset-gray-800"
                >
                <label for="remember" class="ml-2 text-sm text-gray-400">Remember me</label>
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit"
                class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-amber-500/25 flex items-center justify-center gap-2"
            >
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Signing in...
                </span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-gray-800/50 text-gray-500">New to Cinema XXL?</span>
            </div>
        </div>

        {{-- Register Link --}}
        <a 
            href="{{ route('register') }}"
            class="block w-full py-3 px-4 bg-transparent hover:bg-white/5 text-white font-semibold rounded-lg border border-gray-600 hover:border-gray-500 transition-all text-center"
        >
            Create an Account
        </a>
    </div>
</div>
