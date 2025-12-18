<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\StoreMahasiswaRequest;
use App\Http\Requests\Mahasiswa\UpdateMahasiswaRequest;
use App\Http\Resources\MahasiswaResource;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $prodiId = $request->integer('prodi_id');

        $data = Mahasiswa::query()
            ->with(['prodi', 'user'])
            ->when($prodiId, fn($qq) => $qq->where('prodi_id', $prodiId))
            ->when($q, function ($qq) use ($q) {
                $qq->where('nim', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate((int) $request->integer('per_page', 10));

        return MahasiswaResource::collection($data);
    }

    public function store(StoreMahasiswaRequest $request)
    {
        $payload = $request->validated();

        $mhs = DB::transaction(function () use ($payload) {
            $user = User::create([
                'name' => $payload['nama'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password'] ?? 'password'),
                'is_active' => $payload['is_active'] ?? true,
            ]);

            $user->assignRole('mahasiswa');

            return Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $payload['nim'],
                'nama' => $payload['nama'],
                'prodi_id' => $payload['prodi_id'],
                'angkatan' => $payload['angkatan'] ?? null,
                'alamat' => $payload['alamat'] ?? null,
                'no_hp' => $payload['no_hp'] ?? null,
                'tanggal_lahir' => $payload['tanggal_lahir'] ?? null,
            ]);
        });

        $mhs->load(['prodi', 'user']);
        return (new MahasiswaResource($mhs))->response()->setStatusCode(201);
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['prodi', 'user']);
        return new MahasiswaResource($mahasiswa);
    }

    public function update(UpdateMahasiswaRequest $request, Mahasiswa $mahasiswa)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload, $mahasiswa) {
            $mahasiswa->update([
                'nim' => $payload['nim'],
                'nama' => $payload['nama'],
                'prodi_id' => $payload['prodi_id'],
                'angkatan' => $payload['angkatan'] ?? null,
                'alamat' => $payload['alamat'] ?? null,
                'no_hp' => $payload['no_hp'] ?? null,
                'tanggal_lahir' => $payload['tanggal_lahir'] ?? null,
            ]);

            $user = $mahasiswa->user;
            $user->name = $payload['nama'];
            $user->email = $payload['email'];
            if (array_key_exists('is_active', $payload)) {
                $user->is_active = (bool) $payload['is_active'];
            }
            if (!empty($payload['password'])) {
                $user->password = Hash::make($payload['password']);
            }
            $user->save();

            if (!$user->hasRole('mahasiswa')) {
                $user->syncRoles(['mahasiswa']);
            }
        });

        $mahasiswa->load(['prodi', 'user']);
        return new MahasiswaResource($mahasiswa);
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        DB::transaction(function () use ($mahasiswa) {
            $user = $mahasiswa->user;
            $mahasiswa->delete();
            $user?->delete();
        });

        return response()->json(['message' => 'Mahasiswa deleted.']);
    }
}
