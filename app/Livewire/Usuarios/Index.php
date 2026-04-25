<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.usuarios.index', [
            'users' => \App\Models\User::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Usuários']);
    }
}
