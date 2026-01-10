
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;

// Route API untuk grafik RMS dari analysis_results
Route::get('/grafik-rms', [GrafikController::class, 'getRmsData']);
use App\Http\Controllers\GrafikController;

// Route API untuk grafik RMS dari analysis_results
Route::get('/grafik-rms', [GrafikController::class, 'getRmsData']);
