<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Form extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->fill($user->only(['name', 'email']));
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($this->user?->id)],
        ];

        if (!$this->user || !$this->user->exists || $this->password) {
            $rules['password'] = ['required', 'string', 'confirmed', Password::defaults()];
        }

        $data = $this->validate($rules);

        if ($this->user && $this->user->exists) {
            $this->user->name = $data['name'];
            $this->user->email = $data['email'];
            if ($this->password) {
                $this->user->password = Hash::make($data['password']);
            }
            $this->user->save();
            session()->flash('success', 'Usuário atualizado com sucesso.');
        } else {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            session()->flash('success', 'Usuário criado com sucesso.');
        }

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        $title = $this->user?->exists ? 'Editar Usuário' : 'Novo Usuário';

        return view('livewire.usuarios.form')
            ->layout('layouts.app', ['title' => $title]);
    }
}
