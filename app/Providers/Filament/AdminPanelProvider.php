<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use App\Filament\Pages\Auth\Register;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            // 1. Tambahkan Link di Halaman LOGIN
            ->renderHook(
                'panels::auth.login.form.after', // Posisi: Di bawah tombol Login
                fn() => view('components.back-to-home') // Kita akan buat view kecil nanti
            )

            // 2. Tambahkan Link di Halaman REGISTER
            ->renderHook(
                'panels::auth.register.form.after', // Posisi: Di bawah tombol Register
                fn() => view('components.back-to-home')
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            // --- TAMBAHKAN BARIS INI ---
            ->brandName('LevelUp Blog')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->registration(Register::class)
            ->renderHook(
                // Lokasi: Tampilkan SEBELUM menu user (pojok kanan atas)
                'panels::user-menu.before',

                // Konten: Render HTML Badge Role
                fn(): string => Blade::render('
                    @php
                        $user = auth()->user();
                        $role = $user ? $user->roles->first()?->name : null;
                    @endphp
                    
                    @if($role)
                        <div class="flex items-center gap-2 mr-4">
                            <span class="px-3 py-1 text-xs font-bold text-primary-600 bg-primary-100 border border-primary-200 rounded-full">
                                {{ str($role)->headline() }}
                            </span>
                        </div>
                    @endif
                ')
            );
    }
}
