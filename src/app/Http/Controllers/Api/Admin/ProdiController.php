<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prodi\StoreProdiRequest;
use App\Http\Requests\Prodi\UpdateProdiRequest;
use App\Http\Resources\ProdiResource;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $data = Prodi::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('kode', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('kode')
            ->paginate((int) $request->integer('per_page', 10));

        return ProdiResource::collection($data);
    }

    public function store(StoreProdiRequest $request)
    {
        $prodi = Prodi::create($request->validated());
        return (new ProdiResource($prodi))->response()->setStatusCode(201);
    }

    public function show(Prodi $prodi)
    {
        return new ProdiResource($prodi);
    }

    public function update(UpdateProdiRequest $request, Prodi $prodi)
    {
        $prodi->update($request->validated());
        return new ProdiResource($prodi);
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();
        return response()->json(['message' => 'Prodi deleted.']);
    }
}
