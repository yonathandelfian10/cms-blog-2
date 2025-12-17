<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Pilih Penulis (Relasi ke User)
                Select::make('user_id')
                    ->relationship('user', 'name') // Ambil data dari tabel users, tampilkan kolom name
                    ->required()
                    ->label('Penulis')
                    ->searchable(), // Agar bisa cari nama kalau usernya banyak

                // 2. Input Judul (Dengan Magic Auto-Slug)
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true) // Tunggu sampai user selesai ngetik baru jalan
                    ->afterStateUpdated(
                        fn(Set $set, ?string $state) =>
                        $set('slug', Str::slug($state)) // Ubah "Halo Dunia" jadi "halo-dunia"
                    ),

                // 3. Input Slug (Otomatis terisi, user tidak perlu isi manual)
                TextInput::make('slug')
                    ->required()
                    ->disabled() // Dimatikan agar tidak diubah sembarangan
                    ->dehydrated() // TAPI tetap dikirim ke database saat save
                    ->unique(ignoreRecord: true), // Pastikan tidak ada link kembar

                // 4. Input Konten Artikel (Editor Teks Kaya)
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(), // Agar lebarnya memenuhi layar
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Judul Artikel
                Tables\Columns\TextColumn::make('title')
                    ->searchable() // Bisa dicari
                    ->sortable()
                    ->weight('bold') // Cetak tebal biar jelas
                    ->limit(50), // Batasi panjang teks biar tabel ga kepanjangan

                // 2. Nama Penulis (Relasi)
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable(),

                // 3. Slug (Link) - Opsional biar admin tau linknya
                Tables\Columns\TextColumn::make('slug')
                    ->color('gray')
                    ->limit(30),

                // 4. Tanggal Dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Tombol hapus
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
