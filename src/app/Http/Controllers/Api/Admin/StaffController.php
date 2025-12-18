<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $data = Staff::query()
            ->with(['user'])
            ->when($q, function ($qq) use ($q) {
                $qq->where('nip', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%")
                   ->orWhere('bagian', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate((int) $request->integer('per_page', 10));

        return StaffResource::collection($data);
    }

    public function store(StoreStaffRequest $request)
    {
        $payload = $request->validated();

        $staff = DB::transaction(function () use ($payload) {
            $user = User::create([
                'name' => $payload['nama'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password'] ?? 'password'),
            ]);

            // Jika kamu punya role 'staff', aktifkan:
            $user->assignRole('admin');

            return Staff::create([
                'user_id' => $user->id,
                'nip' => $payload['nip'],
                'nama' => $payload['nama'],
                'bagian' => $payload['bagian'] ?? null,
            ]);
        });

        $staff->load(['user']);
        return (new StaffResource($staff))->response()->setStatusCode(201);
    }

    public function show(Staff $staff)
    {
        $staff->load(['user']);
        return new StaffResource($staff);
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload, $staff) {
            $staff->update([
                'nip' => $payload['nip'],
                'nama' => $payload['nama'],
                'bagian' => $payload['bagian'] ?? null,
            ]);

            $user = $staff->user;
            $user->name = $payload['nama'];
            $user->email = $payload['email'];
            if (array_key_exists('is_active', $payload)) {
                $user->is_active = (bool) $payload['is_active'];
            }
            if (!empty($payload['password'])) {
                $user->password = Hash::make($payload['password']);
            }
            $user->save();
        });

        $staff->load(['user']);
        return new StaffResource($staff);
    }

    public function destroy(Staff $staff)
    {
        DB::transaction(function () use ($staff) {
            $user = $staff->user;
            $staff->delete();
            $user?->delete();
        });

        return response()->json(['message' => 'Staff deleted.']);
    }
}
