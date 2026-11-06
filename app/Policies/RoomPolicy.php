<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Room;

class RoomPolicy
{
    public function update(User $user, Room $room): bool
    {
        return (int) $user->id_user === (int) $room->id_user;
    }
 
    // menghapus room bagi id guru yang memiliki room
    public function delete(User $user, Room $room): bool
    {
        return (int) $user->id_user === (int) $room->id_user;
    }
 
    // melihat dan menghapus room untuk id guru yang memiliki room
    public function kelolaHasil(User $user, Room $room): bool
    {
        return (int) $user->id_user === (int) $room->id_user;
    }
}
