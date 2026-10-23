<?php

namespace App\Policies;

use App\Models\Soal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SoalPolicy
{
    // Untuk mengubah/edit soal (user hanya dapat mengedit soal yang dia buat)
    public function update(User $user, Soal $soal): bool
    {
        return (int) $user->id_user === (int) $soal->id_user;
    }
    // Untuk  menghapus soal (oleh user yang membuat soal dan admin)
    public function delete(User $user, Soal $soal): bool
    {
        return $user->role === 'admin'
            || (int) $user->id_user === (int) $soal->id_user;
    }
}
