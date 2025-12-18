<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jadwal\StoreJadwalRequest;
use App\Http\Requests\Jadwal\UpdateJadwalRequest;
use App\Http\Resources\JadwalResource;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->integer('kelas_id');
        $hari = $request->integer('hari');

        $data = Jadwal::query()
            ->with(['kelas', 'ruangan'])
            ->when($kelasId, fn($qq) => $qq->where('kelas_id', $kelasId))
            ->when($hari, fn($qq) => $qq->where('hari', $hari))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate((int) $request->integer('per_page', 10));

        return JadwalResource::collection($data);
    }

    public function store(StoreJadwalRequest $request)
    {
        $payload = $request->validated();

        // Optional: cek bentrok ruangan
        $this->ensureNoRoomConflict($payload);

        $jadwal = Jadwal::create($payload);
        $jadwal->load(['kelas','ruangan']);
        return (new JadwalResource($jadwal))->response()->setStatusCode(201);
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['kelas','ruangan']);
        return new JadwalResource($jadwal);
    }

    public function update(UpdateJadwalRequest $request, Jadwal $jadwal)
    {
        $payload = $request->validated();

        // Optional: cek bentrok ruangan (abaikan jadwal dirinya sendiri)
        $this->ensureNoRoomConflict($payload, $jadwal->id);

        $jadwal->update($payload);
        $jadwal->load(['kelas','ruangan']);
        return new JadwalResource($jadwal);
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal deleted.']);
    }

    private function ensureNoRoomConflict(array $payload, ?int $ignoreId = null): void
    {
        if (empty($payload['ruangan_id'])) return;

        $ruanganId = (int) $payload['ruangan_id'];
        $hari = (int) $payload['hari'];
        $mulai = $payload['jam_mulai'];
        $selesai = $payload['jam_selesai'];

        $q = Jadwal::query()
            ->where('ruangan_id', $ruanganId)
            ->where('hari', $hari)
            // overlap: startA < endB AND startB < endA
            ->where('jam_mulai', '<', $selesai)
            ->where('jam_selesai', '>', $mulai);

        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        if ($q->exists()) {
            throw ValidationException::withMessages([
                'ruangan_id' => ['Bentrok jadwal ruangan pada hari & jam tersebut.']
            ]);
        }
    }
}
