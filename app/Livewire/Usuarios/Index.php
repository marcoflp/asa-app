<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.usuarios.index', [
            'users' => User::all(),
        ])->layout('layouts.app', ['title' => 'Usuários']);
    }
}
