<?php

namespace App\Services;

use App\Repositories\JadwalRepository;

class JadwalService
{
    public function __construct(private JadwalRepository $repo) {}

    public function listForUser($user, ?int $tahunAkademikId)
    {
        if ($user->hasRole('admin')) {
            return $this->repo->forAdmin($tahunAkademikId);
        }

        if ($user->hasRole('dosen')) {
            return $this->repo->forDosen($user->dosen->id, $tahunAkademikId);
        }

        return $this->repo->forMahasiswa($user->mahasiswa->id, $tahunAkademikId);
    }
}
