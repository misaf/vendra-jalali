<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema as Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

it('formats state to jalali date', function () {
    Table::configureUsing(function (Table $table) {
        $table->defaultDateDisplayFormat('F d, Y');
    });

    $page = new ListRecords;
    $table = Table::make($page);

    $column = TextColumn::make('created_at');
    $column->table($table);

    expect($column)
        ->jalaliDate()
        ->formatState(Carbon::parse('1989-10-07'))
        ->toBe('مهر 15, 1368');
});

it('formats state based on default date display format', function () {
    Table::configureUsing(function (Table $table) {
        $table->defaultDateDisplayFormat('Y-m-d');
    });

    $page = new ListRecords;
    $table = Table::make($page);

    $column = TextColumn::make('created_at');
    $column->table($table);

    expect($column)
        ->jalaliDate()
        ->formatState(Carbon::parse('1989-10-07'))
        ->toBe('1368-07-15');
});

it('uses farsi numbers if app locale is fa', function () {
    $locale = App::getLocale();
    App::setLocale('fa');

    try {
        Table::configureUsing(function (Table $table) {
            $table->defaultDateDisplayFormat('F d, Y');
        });

        $page = new ListRecords;
        $table = Table::make($page);

        $column = TextColumn::make('created_at');
        $column->table($table);

        expect($column)
            ->jalaliDate()
            ->formatState(Carbon::parse('1989-10-07'))
            ->toBe('مهر ۱۵, ۱۳۶۸');
    } finally {
        App::setLocale($locale);
    }
});

it('evaluates closures for format', function () {
    $oldRecord = ['__key' => 1, 'created_at' => Carbon::parse('1989-10-07')];
    $newRecord = ['__key' => 1, 'created_at' => Carbon::now()];

    $page = new ListRecords;
    $table = Table::make($page);

    $column = TextColumn::make('created_at')->table($table);

    expect($column)
        ->jalaliDateTime(fn (Carbon $state) => $state->isToday() ? 'H:i:s' : 'Y-m-d')
        ->record($oldRecord)
        ->formatState($oldRecord['created_at'])
        ->toBe('1368-07-15');

    $column = TextColumn::make('created_at')->table($table);

    expect($column)
        ->jalaliDateTime(fn (Carbon $state) => $state->isToday() ? 'H:i:s' : 'Y-m-d')
        ->record($newRecord)
        ->formatState($newRecord['created_at'])
        ->toBe(now()->format('H:i:s'));
});

it('uses latin numbers when explicitly enabled for text columns', function () {
    $locale = App::getLocale();
    App::setLocale('fa');

    try {
        $page = new ListRecords;
        $table = Table::make($page);

        $column = TextColumn::make('created_at');
        $column->table($table);

        expect($column)
            ->jalaliDate('Y-m-d', latinNumbers: true)
            ->formatState(Carbon::parse('1990-09-12'))
            ->toBe('1369-06-21');
    } finally {
        App::setLocale($locale);
    }
});

it('uses farsi numbers when explicitly disabled for text columns', function () {
    $locale = App::getLocale();
    App::setLocale('en');

    try {
        $page = new ListRecords;
        $table = Table::make($page);

        $column = TextColumn::make('created_at');
        $column->table($table);

        expect($column)
            ->jalaliDate('Y-m-d', latinNumbers: false)
            ->formatState(Carbon::parse('1990-09-12'))
            ->toBe('۱۳۶۹-۰۶-۲۱');
    } finally {
        App::setLocale($locale);
    }
});

it('passes latin numbers option to jalali date time for text columns', function () {
    $locale = App::getLocale();
    App::setLocale('en');

    try {
        $page = new ListRecords;
        $table = Table::make($page);

        $column = TextColumn::make('created_at');
        $column->table($table);

        expect($column)
            ->jalaliDateTime('Y-m-d H:i:s', latinNumbers: false)
            ->formatState(Carbon::parse('1990-09-12 13:14:15'))
            ->toBe('۱۳۶۹-۰۶-۲۱ ۱۳:۱۴:۱۵');
    } finally {
        App::setLocale($locale);
    }
});

it('uses latin numbers when explicitly enabled for text entries', function () {
    $locale = App::getLocale();
    App::setLocale('fa');

    try {
        $infolist = Infolist::make();

        expect(TextEntry::make('created_at'))
            ->container($infolist)
            ->jalaliDate('Y-m-d', latinNumbers: true)
            ->formatState(Carbon::parse('1990-09-12'))
            ->toBe('1369-06-21');
    } finally {
        App::setLocale($locale);
    }
});

it('passes latin numbers option to jalali date time for text entries', function () {
    $locale = App::getLocale();
    App::setLocale('en');

    try {
        $infolist = Infolist::make();

        expect(TextEntry::make('created_at'))
            ->container($infolist)
            ->jalaliDateTime('Y-m-d H:i:s', latinNumbers: false)
            ->formatState(Carbon::parse('1990-09-12 13:14:15'))
            ->toBe('۱۳۶۹-۰۶-۲۱ ۱۳:۱۴:۱۵');
    } finally {
        App::setLocale($locale);
    }
});
