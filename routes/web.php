<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| HALAMAN AWAL
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| STATUS REALTIME
|--------------------------------------------------------------------------
| TANPA AUTH → agar ESP32 bisa akses JSON
|--------------------------------------------------------------------------
*/
Route::get('/status', function () {

    $data = DB::table('status_realtime')
        ->where('id', 1)
        ->first();

    // FALLBACK
    if (!$data) {

        return response()->json([
            'status_palang' => 'buka',
            'arah'          => 'manual',
            'sirine'        => 'off',
            'sensor1'       => 0,
            'sensor2'       => 0,
            'sensor'        => 'on',
            'gangguan'      => null,
        ]);
    }

    return response()->json([
        'status_palang' => $data->status_palang,
        'arah'          => $data->arah,
        'sirine'        => $data->sirine,
        'sensor1'       => $data->sensor1 ?? 0,
        'sensor2'       => $data->sensor2 ?? 0,
        'sensor'        => $data->sensor ?? 'on',
        'gangguan'      => $data->gangguan,
    ]);

});

/*
|--------------------------------------------------------------------------
| HISTORY
|--------------------------------------------------------------------------
| TANPA AUTH → agar chart realtime normal
|--------------------------------------------------------------------------
*/
Route::get('/history', function () {

    $rows = DB::table('history')
        ->orderBy('id', 'desc')
        ->limit(20)
        ->get()
        ->reverse()
        ->values();

    return response()->json(
        $rows->map(fn($row) => [
            'waktu'         => $row->waktu,
            'status_palang' => $row->status_palang,
            'sensor1'       => (int) $row->sensor1,
            'sensor2'       => (int) $row->sensor2,
        ])
    );

});

/*
|--------------------------------------------------------------------------
| MANUAL CONTROL
|--------------------------------------------------------------------------
*/

// =========================
// BUKA PALANG
// =========================
Route::get('/open', function () {

    DB::table('status_realtime')->updateOrInsert(

        ['id' => 1],

        [
            'status_palang' => 'buka',
            'arah'          => 'manual',
            'sirine'        => 'off',
            'sensor1'       => 0,
            'sensor2'       => 0,
            'gangguan'      => null,
        ]
    );

    return response()->json([
        'status' => 'ok',
        'aksi'   => 'buka'
    ]);

})->middleware(['auth'])
  ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// =========================
// TUTUP PALANG
// =========================
Route::get('/close', function () {

    DB::table('status_realtime')->updateOrInsert(

        ['id' => 1],

        [
            'status_palang' => 'tutup',
            'arah'          => 'manual',
            'sirine'        => 'on',
            'sensor1'       => 0,
            'sensor2'       => 0,
            'gangguan'      => null,
        ]
    );

    return response()->json([
        'status' => 'ok',
        'aksi'   => 'tutup'
    ]);

})->middleware(['auth'])
  ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// =========================
// SENSOR ON / OFF
// =========================
Route::get('/sensor', function () {

    $status = request('status');

    // VALIDASI
    if (!in_array($status, ['on', 'off'])) {

        return response()->json([
            'error' => 'Nilai tidak valid'
        ], 422);
    }

    $updateData = [

        'sensor' => $status
    ];

    // RESET SENSOR SAAT OFF
    if ($status === 'off') {

        $updateData['sensor1'] = 0;
        $updateData['sensor2'] = 0;
        $updateData['sirine']  = 'off';
    }

    DB::table('status_realtime')->updateOrInsert(

        ['id' => 1],

        $updateData
    );

    return response()->json([
        'status' => 'ok',
        'sensor' => $status
    ]);

})->middleware(['auth'])
  ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| API UPDATE DARI ESP32
|--------------------------------------------------------------------------
*/
Route::post('/api/update', function (Request $req) {

    // =========================
    // VALIDASI API KEY
    // =========================
    $apiKey = $req->header('X-API-KEY');

    // VALIDASI API KEY DARI DATABASE
    $validKey = DB::table('api_keys')
        ->where('api_key', $apiKey)
        ->exists();

    if (!$validKey) {

        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized'
        ], 401);
    }

    // =========================
    // VALIDASI DATA
    // =========================
    $validated = $req->validate([

        'status_palang' => 'required|in:buka,tutup',
        'arah'          => 'nullable|string|max:20',
        'sirine'        => 'nullable|in:on,off',
        'sensor1'       => 'nullable|integer|in:0,1',
        'sensor2'       => 'nullable|integer|in:0,1',
        'gangguan'      => 'nullable|string|max:255',
    ]);

    // =========================
    // CEK STATUS SENSOR
    // =========================
    $current = DB::table('status_realtime')
        ->where('id', 1)
        ->first();

    // SENSOR OFF → abaikan data ESP32
    if ($current && ($current->sensor ?? 'on') === 'off') {

        return response()->json([
            'status'  => 'ignored',
            'message' => 'Sensor OFF'
        ]);
    }

    // =========================
    // DATA DARI ESP32
    // =========================
    $status   = $validated['status_palang'];
    $arah     = $validated['arah'] ?? 'sensor';
    $sirine   = $validated['sirine'] ?? 'off';
    $gangguan = $validated['gangguan'] ?? null;
    $sensor1  = $validated['sensor1'] ?? 0;
    $sensor2  = $validated['sensor2'] ?? 0;

    // =========================
    // FALLBACK SENSOR
    // =========================
    $currentSensor1 = $current->sensor1 ?? 0;
    $currentSensor2 = $current->sensor2 ?? 0;

    // =========================
    // DETEKSI PERUBAHAN SENSOR
    // =========================
    $sensorChanged =
        ((int)$currentSensor1 !== (int)$sensor1) ||
        ((int)$currentSensor2 !== (int)$sensor2);

    // =========================
    // UPDATE REALTIME
    // =========================
    DB::table('status_realtime')->updateOrInsert(

        ['id' => 1],

        [
            'status_palang' => $status,
            'arah'          => $arah,
            'sirine'        => $sirine,
            'sensor1'       => $sensor1,
            'sensor2'       => $sensor2,
            'gangguan'      => $gangguan,
        ]
    );

    // =========================
    // INSERT HISTORY
    // =========================
    if ($sensorChanged) {

        DB::table('history')->insert([

            'status_palang' => $status,
            'arah'          => $arah,
            'sensor1'       => $sensor1,
            'sensor2'       => $sensor2,
            'waktu'         => now(),
        ]);
    }

    return response()->json([
        'status'         => 'ok',
        'sensor_changed' => $sensorChanged
    ]);

})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| HALAMAN
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    return view('dashboard');

})->middleware(['auth'])->name('dashboard');

Route::get('/log', function () {

    return view('log');

})->middleware(['auth'])->name('log');

/*
|--------------------------------------------------------------------------
| EXPORT LOG
|--------------------------------------------------------------------------
*/
Route::get('/export-log', function () {

    $data = DB::table('history')
        ->orderBy('waktu', 'desc')
        ->get();

    $filename = "log_kereta_" . date('Ymd_His') . ".xls";

    $headers = [
        "Content-Type"        => "application/vnd.ms-excel",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $content  = "<table border='1'>";

    $content .= "
    <tr>
        <th>No</th>
        <th>Status Palang</th>
        <th>Arah</th>
        <th>Sensor 1</th>
        <th>Sensor 2</th>
        <th>Waktu</th>
    </tr>";

    foreach ($data as $i => $row) {

        $content .= "
        <tr>
            <td>" . ($i + 1) . "</td>
            <td>{$row->status_palang}</td>
            <td>{$row->arah}</td>
            <td>" . ($row->sensor1 ? 'AKTIF' : 'IDLE') . "</td>
            <td>" . ($row->sensor2 ? 'AKTIF' : 'IDLE') . "</td>
            <td>{$row->waktu}</td>
        </tr>";
    }

    $content .= "</table>";

    return response($content, 200, $headers);

})->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| PDF JADWAL
|--------------------------------------------------------------------------
*/
Route::get('/jadwal', function () {

    return response()->file(
        public_path('jadwal_kereta.pdf')
    );

})->middleware(['auth']);