<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TahunAkademik\StoreTahunAkademikRequest;
use App\Http\Requests\TahunAkademik\UpdateTahunAkademikRequest;
use App\Http\Resources\TahunAkademikResource;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        $data = TahunAkademik::query()
            ->orderByDesc('id')
            ->paginate((int) $request->integer('per_page', 10));

        return TahunAkademikResource::collection($data);
    }

    public function store(StoreTahunAkademikRequest $request)
    {
        $payload = $request->validated();

        $ta = DB::transaction(function () use ($payload) {
            if (($payload['is_active'] ?? false) === true) {
                TahunAkademik::query()->where('is_active', true)->update(['is_active' => false]);
            }
            return TahunAkademik::create($payload);
        });

        return (new TahunAkademikResource($ta))->response()->setStatusCode(201);
    }

    public function show(TahunAkademik $tahun_akademik)
    {
        return new TahunAkademikResource($tahun_akademik);
    }

    public function update(UpdateTahunAkademikRequest $request, TahunAkademik $tahun_akademik)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload, $tahun_akademik) {
            if (($payload['is_active'] ?? false) === true) {
                TahunAkademik::query()
                    ->where('is_active', true)
                    ->where('id', '!=', $tahun_akademik->id)
                    ->update(['is_active' => false]);
            }
            $tahun_akademik->update($payload);
        });

        return new TahunAkademikResource($tahun_akademik);
    }

    public function destroy(TahunAkademik $tahun_akademik)
    {
        $tahun_akademik->delete();
        return response()->json(['message' => 'Tahun akademik deleted.']);
    }
}
