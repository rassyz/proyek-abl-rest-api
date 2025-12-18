<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dosen\StoreDosenRequest;
use App\Http\Requests\Dosen\UpdateDosenRequest;
use App\Http\Resources\DosenResource;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $prodiId = $request->integer('prodi_id');

        $data = Dosen::query()
            ->with(['prodi', 'user'])
            ->when($prodiId, fn($qq) => $qq->where('prodi_id', $prodiId))
            ->when($q, function ($qq) use ($q) {
                $qq->where('nidn', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate((int) $request->integer('per_page', 10));

        return DosenResource::collection($data);
    }

    public function store(StoreDosenRequest $request)
    {
        $payload = $request->validated();

        $dosen = DB::transaction(function () use ($payload) {
            $user = User::create([
                'name' => $payload['nama'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password'] ?? 'password'),
            ]);

            $user->assignRole('dosen');

            return Dosen::create([
                'user_id' => $user->id,
                'nidn' => $payload['nidn'],
                'nama' => $payload['nama'],
                'prodi_id' => $payload['prodi_id'],
                'no_hp' => $payload['no_hp'] ?? null,
            ]);
        });

        $dosen->load(['prodi', 'user']);
        return (new DosenResource($dosen))->response()->setStatusCode(201);
    }

    public function show(Dosen $dosen)
    {
        $dosen->load(['prodi', 'user']);
        return new DosenResource($dosen);
    }

    public function update(UpdateDosenRequest $request, Dosen $dosen)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload, $dosen) {
            $dosen->update([
                'nidn'      => $payload['nidn'],
                'nama'      => $payload['nama'],
                'prodi_id'  => $payload['prodi_id'],
                'no_hp'     => $payload['no_hp'] ?? null,
            ]);

            $user = $dosen->user;
            $user->name = $payload['nama'];
            $user->email = $payload['email'];
            if (array_key_exists('is_active', $payload)) {
                $user->is_active = (bool) $payload['is_active'];
            }
            if (!empty($payload['password'])) {
                $user->password = Hash::make($payload['password']);
            }
            $user->save();

            // pastikan role dosen tetap ada
            if (!$user->hasRole('dosen')) {
                $user->syncRoles(['dosen']);
            }
        });

        $dosen->load(['prodi', 'user']);
        return new DosenResource($dosen);
    }

    public function destroy(Dosen $dosen)
    {
        DB::transaction(function () use ($dosen) {
            // hapus profil dosen lalu user (cascade bisa juga, tapi aman eksplisit)
            $user = $dosen->user;
            $dosen->delete();
            $user?->delete();
        });

        return response()->json(['message' => 'Dosen deleted.']);
    }
}
