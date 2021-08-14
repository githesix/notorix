<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PopulationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('welcome', function () {
    return view('welcome');
});

Route::get('test', function () {
    $groupFilters = [];
    $groupes = App\Models\Groupe::orderBy('nom', 'asc')->withCount('users')->get(['id', 'nom', 'description']);
    foreach ($groupes as $groupe) {
        $groupFilters[$groupe->id] = false;
    }
    return view('test', ['groupes' => $groupes, "groupFilters" => $groupFilters]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('old_dashboard');

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('bo/users', [HomeController::class, 'BOUsers'])->middleware('role:128')->name('BOUsers');
Route::get('bo/eleves', [HomeController::class, 'BOEleves'])->middleware('role:128')->name('BOEleves');
Route::get('/dwnxls/{action}', [HomeController::class, 'downloadExcel'])->middleware('role:128')->name('downloadExcel');
Route::get('/eleve/{eluid?}', [HomeController::class, 'eleve'])->name('eleve');
Route::get('/prof/{code?}', [HomeController::class, 'prof'])->name('prof');
Route::post('/hestheone', [HomeController::class, 'imtheone'])->name('hestheone');
Route::post('/imyourfather', [HomeController::class, 'imyourfather'])->name('imyourfather');

// Population
Route::prefix('population')->middleware('role:128')->group(function () {
    Route::get('/', [PopulationController::class, 'index'])->name('population'); // BackOffice Population
    Route::post('postcsv', [PopulationController::class, 'postcsv'])->name('population.postcsv'); // Receives csv uploaded
    Route::post('postclasses', [PopulationController::class, 'postclasses'])->name('population.postclasses'); // POST classes
    Route::post('posteleves', [PopulationController::class, 'posteleves'])->name('population.posteleves'); // POST élèves
    Route::get('listes', [PopulationController::class, 'listes'])->name('population.listes'); // BackOffice Population - Lists
    Route::post('listeidentifiants', [PopulationController::class, 'liste_identifiants'])->name('population.listes.identifiants'); // POST lists of identifiers
    Route::get('listedeseleves', [PopulationController::class, 'listedeseleves'])->name('population.listedeseleves'); // BackOffice Population - Lists
});

// Preinscriptions
Route::prefix('preauth')->group(function () {
    Route::get('/{token}/newpw', [IndexController::class, 'get_confirm_preregistration'])->name('get_confirm_preregistration');
    Route::post('/confirmpw', [IndexController::class, 'post_confirm_preregistration'])->name('post_confirm_preregistration');
});
