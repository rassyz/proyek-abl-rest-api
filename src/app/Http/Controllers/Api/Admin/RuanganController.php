<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ruangan\StoreRuanganRequest;
use App\Http\Requests\Ruangan\UpdateRuanganRequest;
use App\Http\Resources\RuanganResource;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $data = Ruangan::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('kode', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('kode')
            ->paginate((int) $request->integer('per_page', 10));

        return RuanganResource::collection($data);
    }

    public function store(StoreRuanganRequest $request)
    {
        $ruangan = Ruangan::create($request->validated());
        return (new RuanganResource($ruangan))->response()->setStatusCode(201);
    }

    public function show(Ruangan $ruangan)
    {
        return new RuanganResource($ruangan);
    }

    public function update(UpdateRuanganRequest $request, Ruangan $ruangan)
    {
        $ruangan->update($request->validated());
        return new RuanganResource($ruangan);
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return response()->json(['message' => 'Ruangan deleted.']);
    }
}
