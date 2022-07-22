<?php

namespace FilamentSentry\Resources;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use FilamentSentry\Resources\UserResource\Pages;
use FilamentAddons\Forms\Fields\PasswordGenerator;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use FilamentSentry\Resources\UserResource\Pages\EditUser;
use FilamentSentry\Resources\UserResource\Pages\ListUsers;
use FilamentSentry\Resources\UserResource\Pages\CreateUser;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'User';

    protected static ?string $navigationGroup = 'Filament Shield';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(User::class, 'email', fn ($record) => $record),
                        Forms\Components\Toggle::make('reset_password')
                            ->columnSpan('full')
                            ->reactive()
                            ->hidden(function ($livewire) {
                                if ($livewire instanceof CreateUser) {
                                    return true;
                                }
                            }),
                        PasswordGenerator::make('password')
                            ->columnSpan('full')
                            ->visible(function ($livewire, $get) {
                                if ($livewire instanceof CreateUser) {
                                    return true;
                                }
                                return $get('reset_password') == true;
                            })
                            ->rules(config('filament-breezy.password_rules', 'max:8'))
                            ->required()
                            ->dehydrateStateUsing(function ($state) {
                                return Hash::make($state);
                            }),
                        Forms\Components\CheckboxList::make('roles')
                            ->columnSpan('full')
                            ->relationship('roles', 'name', function(Builder $query) {
                                if (!auth()->user()->hasRole('super_admin')) {
                                    return $query->where('name', '<>', 'super_admin');
                                }

                                return $query;
                            })
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return Str::of($record->name)->headline();
                            })
                            ->columns(4),
                    ])->columns(['md' => 2]),
                Forms\Components\Section::make('Permissions')
                    ->description('Users with roles have permission to completely manage resources based on the permissions set under the Roles Menu. To limit a user\'s access to specific resources disable thier roles and assign them individual permissions below.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make([
                            'sm' => 2,
                            'lg' => 3,
                        ])
                        ->schema(static::getResourceEntitiesSchema())
                        ->columns([
                            'sm' => 2,
                            'lg' => 3,
                        ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->formatStateUsing(function ($state) {
                        return Str::of($state)->headline();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')->relationship('roles', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(FilamentShield::getResources())->sortKeys()->reduce(function ($entities, $entity) {
            $entities[] = Forms\Components\Card::make()
                    ->extraAttributes(['class' => 'border-0 shadow-lg p-2'])
                    ->schema([
                        Forms\Components\Fieldset::make('Permissions')
                            ->label(FilamentShield::getLocalizedResourceLabel($entity))
                            ->extraAttributes(['class' => 'text-primary-600','style' => 'border-color:var(--primary)'])
                            ->columns(2)
                            ->schema(RoleResource::getResourceEntityPermissionsSchema($entity)),
                    ])
                    ->columnSpan(1);

            return $entities;
        }, []);
    }
}
