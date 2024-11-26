<?php

namespace App\Filament\Resources\LocationResource\RelationManagers;

use App\Core\UseCases\Locations\AddTimeSlot;
use App\Core\UseCases\Locations\ValidateTimeSlot;
use App\Models\Location;
use App\Models\TimeSlot;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeSlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeSlots';
    protected static ?string $recordTitleAttribute = 'day_of_week';
    protected static ?string $inverseRelationship = 'location';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('day_of_week')
                    ->label('Dia de la semana')
                    ->options([
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miercoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sabado',
                        0 => 'Domingo'
                    ])
                    ->required(),
                Forms\Components\TextInput::make('cost_per_hour')
                    ->prefix('ARS $')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->seconds(false)
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->groups([
                Tables\Grouping\Group::make('day_of_week')
                    ->getTitleFromRecordUsing(fn (TimeSlot $record): string => match ($record->day_of_week){
                        0 => 'Domingo',
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miercoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sabado',
                    })
                ->collapsible(),
            ])


            //->recordTitleAttribute('day_of_week')
            ->columns([
                /*
                  Tables\Columns\TextColumn::make('day_of_week')
                  ->getStateUsing(fn($state) => )
                  ->label('Dia de la Semana'),
  */

                Tables\Columns\TextColumn::make('cost_per_hour')
                    ->label('Costo por hora')
                    ->prefix('ARS $'),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora de inicio'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora de cierre')

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->before(function (CreateAction $action, $livewire, array $data) {
                        $isValid = app()->make(ValidateTimeSlot::class)->execute($livewire->ownerRecord->id, $data);
                        if (!$isValid) {
                            Notification::make('notification_error')
                                ->title('Error')
                                ->body('El horario ya existe')
                                ->icon('heroicon-o-exclamation-circle')
                                ->color('danger')
                                ->send();
                            $action->halt();
                        }

                    })
                    ->using(function ($livewire, array $data): Model {
                        $locationId = $livewire->ownerRecord->id;
                        return app()->make(AddTimeSlot::class)
                            ->execute($locationId, $data);
                    })
                    ->successNotification(fn(Notification $notification) => $notification
                        ->title('Horario creado')
                        ->icon('heroicon-o-check-circle')
                    )

                //   ->action('Ver horarios', fn () => route('locations.show', $livewire->ownerRecord->id
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
