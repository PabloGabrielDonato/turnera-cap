<?php

namespace App\Filament\Pages\Auth;

use App\Models\UserData;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as RegisterPage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Register extends RegisterPage
{
    // protected static string $view = 'filament.pages.auth.register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getLastNameFormComponent(),
                        $this->getEmailFormComponent(),

                        Section::make('user_data')
                            //->relationship('userData')
                            ->schema([
                                $this->getAddressFormComponent(),
                                $this->getPhoneFormComponent(),
                          //      $this->getBirthDateFormComponent(),
                            ]),

                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),

                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function handleRegistration(array $data): Model
    {
        $user = $this->getUserModel()::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $userData = UserData::create([
            'address' => $data['address'],
            'phone' => $data['phone'],
            'user_id' => $user->id,
            //  'birth_date' => $data['birth_date'],
        ]);

        return $user;
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->required();
    }

    protected function getAddressFormComponent(): Component
    {
        return TextInput::make('address')
            ->label('Direccion')
            ->placeholder('Calle 123');
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->numeric();
    }

    protected function getBirthDateFormComponent(): Component
    {
        return DatePicker::make('birth_date');
    }

}
