<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class Login extends Component
{
    public $logo;
    public $logoHeigth;
    public $email;
    public $password;
    public $redirect;

    public function mount()
    {
        $application = app()->make(config("supernova.application", Application::class));
        $this->logo = $application->logo();
        $this->logoHeigth = $application->logoHeigth();
    }

    public function getRules()
    {
        return [
            'email' => $this->email === "root" ? 'required' : 'required|email',
            'password' => 'required'
        ];
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function submit()
    {
        $this->validate();
        $application = app()->make(config("supernova.application", Application::class));
        $model = app()->make($application->userModel());
        $user = $model->where("email", $this->email)->first();
        if (!$user) {
            $this->addError("email", "Usuário não encontrado");
            return;
        }
        if (!password_verify($this->password, $user->password)) {
            $this->addError("password", "Senha incorreta");
            return;
        }
        auth()->login($user);
        $this->redirect($this->redirect ?? route("supernova.dashboard"));
    }

    public function render()
    {
        return view('supernova-livewire-views::login');
    }
}
