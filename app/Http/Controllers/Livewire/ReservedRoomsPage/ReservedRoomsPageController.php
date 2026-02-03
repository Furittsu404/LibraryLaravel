<?php

namespace App\Http\Controllers\Livewire\ReservedRoomsPage;

use Livewire\Component;
use App\Models\Room;

class ReservedRoomsPageController extends Component
{
    public $title = 'Reserved Rooms';

    public function render()
    {
        $rooms = Room::all();
        $activePage = 'reserved-rooms';

        return view('components.reservedRoomsPage.reservedRoomsPage', compact('rooms', 'activePage'));
    }
}
