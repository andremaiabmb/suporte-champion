<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Dashboard (roteador por papel)
use App\Http\Controllers\Dashboard\RouterController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AlunoController;
use App\Http\Controllers\Dashboard\ModeradorController;
use App\Http\Controllers\Dashboard\ProfessorController;
use App\Http\Controllers\Dashboard\FinanceiroController;
use App\Http\Controllers\Dashboard\DsoController;

// Admin (dashboard) - CRUDs
use App\Http\Controllers\Dashboard\FaqController           as AdminFaqController;
use App\Http\Controllers\Dashboard\EventController         as AdminEventController;
use App\Http\Controllers\Dashboard\PostController          as AdminPostController;
use App\Http\Controllers\Dashboard\Support\SectorAdminController;

// Suporte (staff/aluno)
use App\Http\Controllers\Dashboard\SupportModerationController;   // staff (moderação)
use App\Http\Controllers\Support\MyTicketsController;             // aluno

// Perfil / QR
use App\Http\Controllers\ProfileController;

// Presenças (check-in/out)
use App\Http\Controllers\Dashboard\EventAttendanceController;

// Site público
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\FaqController           as PublicFaqController;
use App\Http\Controllers\PublicSite\PostController          as PublicPostController;
use App\Http\Controllers\PublicSite\EventController         as PublicEventController;
use App\Http\Controllers\PublicSite\StoreController;

// Idioma (Controller)
use App\Http\Controllers\LocaleController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Página pública de dúvidas
Route::get('/duvidas',        [PublicFaqController::class, 'index'])->name('faqs.index');
Route::get('/d/{faq:slug}',   [PublicFaqController::class, 'show'])->name('faqs.show'); // <-- ADICIONADA

/*
|--------------------------------------------------------------------------
| Notícias (Posts) públicas
|--------------------------------------------------------------------------
*/
Route::get('/noticias',       [PublicPostController::class, 'index'])->name('news.index');
Route::get('/noticias/{key}', [PublicPostController::class, 'show'])->name('news.show');

/*
|--------------------------------------------------------------------------
| Loja pública & carrinho
|--------------------------------------------------------------------------
*/
Route::get('/loja',                [StoreController::class, 'index'])->name('store.index');
Route::get('/loja/{slug}',         [StoreController::class, 'show'])->name('store.show');
Route::get('/carrinho',            [StoreController::class, 'cart'])->name('store.cart');
Route::post('/carrinho/adicionar', [StoreController::class, 'addToCart'])->name('store.cart.add');
Route::post('/carrinho/remover',   [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
Route::post('/carrinho/limpar',    [StoreController::class, 'clearCart'])->name('store.cart.clear');

/*
|--------------------------------------------------------------------------
| Idioma (padronizado p/ locale.store)
|--------------------------------------------------------------------------
*/
Route::post('/locale', [LocaleController::class, 'store'])
    ->name('locale.store')
    ->middleware('web');

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('status', 'Email verificado com sucesso.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Link de verificação enviado.');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Eventos (inscrição autenticada no site público)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/eventos/{event}/inscricao',  [PublicEventController::class, 'subscribe'])->name('events.subscribe');
    Route::post('/eventos/{event}/inscricao', [PublicEventController::class, 'storeRegistration'])->name('events.register');
});

/*
|--------------------------------------------------------------------------
| Suporte do Aluno (autenticado + verificado)
| - Mantivemos suas rotas e nomes support.my.* para lista/criação/ações
| - O "show" ficou canônico com nome support.show e binding pelo code
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->prefix('suporte')->group(function () {
    Route::get('/',                      [MyTicketsController::class, 'index'])->name('support.my.index');
    Route::get('/novo',                  [MyTicketsController::class, 'create'])->name('support.my.create');
    Route::post('/novo',                 [MyTicketsController::class, 'store'])->name('support.my.store');

    // Detalhe do ticket (NOME esperado pelo Blade): /suporte/{code}
    Route::get('/{ticket:code}',         [MyTicketsController::class, 'show'])->name('support.show');

    // Ações sobre o próprio ticket (continuam com prefixo support.my.*)
    Route::post('/{ticket:code}/reply',  [MyTicketsController::class, 'reply'])->name('support.my.reply');
    Route::post('/{ticket:code}/close',  [MyTicketsController::class, 'close'])->name('support.my.close');
});

/*
|--------------------------------------------------------------------------
| Perfil / QR (autenticado + verificado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/perfil',  [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/meu-qr',  [ProfileController::class, 'qrPage'])->name('profile.qrpage');
});

/*
|--------------------------------------------------------------------------
| Área Autenticada + Verificada (Dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Roteador do /dashboard (redireciona conforme o papel)
    Route::get('/dashboard', RouterController::class)->name('dashboard');

    Route::prefix('dashboard')->group(function () {

        Route::middleware('role:admin')
            ->get('/admin', [AdminController::class, 'index'])
            ->name('dashboard.admin.index');

        Route::middleware('role:professor')
            ->get('/professor', [ProfessorController::class, 'index'])
            ->name('dashboard.professor.index');

        Route::middleware('role:moderador')
            ->get('/moderador', [ModeradorController::class, 'index'])
            ->name('dashboard.moderador.index');

        Route::middleware('role:financeiro')
            ->get('/financeiro', [FinanceiroController::class, 'index'])
            ->name('dashboard.financeiro.index');

        Route::middleware('role:dso')
            ->get('/dso', [DsoController::class, 'index'])
            ->name('dashboard.dso.index');

        /*
        |--------------------------------------------------------------------------
        | Admin • CRUD de FAQs (dashboard)
        |--------------------------------------------------------------------------
        */
        if (class_exists(AdminFaqController::class)) {
            Route::middleware('role:admin')->prefix('faqs')->name('admin.faqs.')->group(function () {
                Route::get('/',             [AdminFaqController::class, 'index'])->name('index');
                Route::get('/create',       [AdminFaqController::class, 'create'])->name('create');
                Route::post('/',            [AdminFaqController::class, 'store'])->name('store');
                Route::get('/{faq}',        [AdminFaqController::class, 'show'])->name('show');
                Route::get('/{faq}/edit',   [AdminFaqController::class, 'edit'])->name('edit');
                Route::put('/{faq}',        [AdminFaqController::class, 'update'])->name('update');
                Route::delete('/{faq}',     [AdminFaqController::class, 'destroy'])->name('destroy');
            });
        } else {
            Route::middleware('role:admin')->get('faqs', fn () => view('admin.placeholders.module', [
                'titulo'   => 'FAQs',
                'mensagem' => 'Módulo de FAQs ainda não instalado.',
            ]))->name('admin.faqs.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Admin • CRUD de Eventos (dashboard)
        |--------------------------------------------------------------------------
        */
        if (class_exists(AdminEventController::class)) {
            Route::middleware('role:admin')->prefix('events')->name('admin.events.')->group(function () {
                Route::get('/',             [AdminEventController::class, 'index'])->name('index');
                Route::get('/create',       [AdminEventController::class, 'create'])->name('create');
                Route::post('/',            [AdminEventController::class, 'store'])->name('store');
                Route::get('/{event}',      [AdminEventController::class, 'show'])->name('show');
                Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit');
                Route::put('/{event}',      [AdminEventController::class, 'update'])->name('update');
                Route::delete('/{event}',   [AdminEventController::class, 'destroy'])->name('destroy');

                // Presenças (por dia via ?date=YYYY-MM-DD)
                Route::get('/{event}/attendance',       [EventAttendanceController::class, 'attendance'])->name('attendance');
                Route::post('/{event}/attendance/scan', [EventAttendanceController::class, 'scan'])->name('attendance.scan');
            });
        } else {
            Route::middleware('role:admin')->get('events', fn () => view('admin.placeholders.module', [
                'titulo'   => 'Eventos',
                'mensagem' => 'Módulo de Eventos ainda não instalado.',
            ]))->name('admin.events.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Admin • CRUD de Posts (dashboard)
        |--------------------------------------------------------------------------
        */
        if (class_exists(AdminPostController::class)) {
            Route::middleware('role:admin')->prefix('posts')->name('admin.posts.')->group(function () {
                Route::get('/',             [AdminPostController::class, 'index'])->name('index');
                Route::get('/create',       [AdminPostController::class, 'create'])->name('create');
                Route::post('/',            [AdminPostController::class, 'store'])->name('store');
                Route::get('/{post}',       [AdminPostController::class, 'show'])->name('show');
                Route::get('/{post}/edit',  [AdminPostController::class, 'edit'])->name('edit');
                Route::put('/{post}',       [AdminPostController::class, 'update'])->name('update');
                Route::delete('/{post}',    [AdminPostController::class, 'destroy'])->name('destroy');
            });
        } else {
            Route::middleware('role:admin')->get('posts', fn () => view('admin.placeholders.module', [
                'titulo'   => 'Posts',
                'mensagem' => 'Módulo de Posts ainda não instalado.',
            ]))->name('admin.posts.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Admin • Suporte (Tickets para staff)
        | -> usa policy can:viewAny,App\Models\Ticket
        |--------------------------------------------------------------------------
        */
        if (class_exists(SupportModerationController::class)) {
            Route::middleware('can:viewAny,App\Models\Ticket')
                ->prefix('support')->name('admin.supports.')
                ->group(function () {
                    Route::get('/',                 [SupportModerationController::class, 'index'])->name('index');
                    Route::get('/{ticket}',         [SupportModerationController::class, 'show'])->name('show');
                    Route::post('/{ticket}/assign',   [SupportModerationController::class, 'assign'])->name('assign');
                    Route::post('/{ticket}/reassign', [SupportModerationController::class, 'reassign'])->name('reassign');
                    Route::post('/{ticket}/status',   [SupportModerationController::class, 'status'])->name('status');
                    Route::post('/{ticket}/note',     [SupportModerationController::class, 'note'])->name('note');
                });
        } else {
            Route::middleware('can:viewAny,App\Models\Ticket')
                ->get('support', fn () => view('admin.placeholders.module', [
                    'titulo'   => 'Chamados',
                    'mensagem' => 'Módulo de Suporte (staff) ainda não instalado.',
                ]))->name('admin.supports.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Admin • Setores (CRUD do suporte)
        |--------------------------------------------------------------------------
        */
        if (class_exists(SectorAdminController::class)) {
            Route::middleware('role:moderador|admin')
                ->prefix('sectors')->name('admin.sectors.')
                ->group(function () {
                    Route::get('/',               [SectorAdminController::class, 'index'])->name('index');
                    Route::get('/create',         [SectorAdminController::class, 'create'])->name('create');
                    Route::post('/',              [SectorAdminController::class, 'store'])->name('store');
                    Route::get('/{sector}',       [SectorAdminController::class, 'show'])->name('show');
                    Route::get('/{sector}/edit',  [SectorAdminController::class, 'edit'])->name('edit');
                    Route::put('/{sector}',       [SectorAdminController::class, 'update'])->name('update');
                    Route::delete('/{sector}',    [SectorAdminController::class, 'destroy'])->name('destroy');
                });
        } else {
            Route::middleware('role:moderador|admin')
                ->get('sectors', fn () => view('admin.placeholders.module', [
                    'titulo'   => 'Setores',
                    'mensagem' => 'CRUD de Setores ainda não instalado.',
                ]))->name('admin.sectors.index');
        }

    }); // /dashboard
}); // auth + verified

// Rotas de autenticação (Breeze/Jetstream/Fortify, etc.)
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Fallback 404 amigável (opcional)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (view()->exists('errors.simple-404')) {
        return response()->view('errors.simple-404', [], 404);
    }
    return response('Página não encontrada.', 404);
});
