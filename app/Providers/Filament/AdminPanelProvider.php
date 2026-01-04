<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Inter')
            ->brandName('Cinema XXL Admin')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Cinema Operations',
                'Food & Beverage',
                'POS System',
                'Administration',
            ])
            ->navigationItems([
                NavigationItem::make('Ticket POS')
                    ->url('/pos/ticket', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-ticket')
                    ->group('POS System')
                    ->sort(1)
                    ->visible(fn (): bool => $this->canAccessTicketPos()),
                NavigationItem::make('FnB POS')
                    ->url('/pos/fnb', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-shopping-cart')
                    ->group('POS System')
                    ->sort(2)
                    ->visible(fn (): bool => $this->canAccessFnbPos()),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Check if current user can access Ticket POS.
     */
    protected function canAccessTicketPos(): bool
    {
        $user = Auth::user();
        
        return $user && in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
        ]);
    }

    /**
     * Check if current user can access FnB POS.
     */
    protected function canAccessFnbPos(): bool
    {
        $user = Auth::user();
        
        return $user && in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::FnbStaff,
        ]);
    }
}
