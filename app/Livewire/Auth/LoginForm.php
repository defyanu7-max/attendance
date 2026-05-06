<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Login')]
class LoginForm extends Component
{
    public string $username = '';
    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'username' => $this->username,
            'password' => $this->password,
        ])) {
            session()->regenerate();
            $this->redirect(route('dashboard'), navigate: true);
        } else {
            $this->addError('username', 'Username atau password salah.');
        }
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
