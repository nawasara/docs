<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('nawasara-docs')->group(function () {
    Route::get('/components/table', function () {
        return view('nawasara-docs::pages.examples.table', [
            'title' => 'Table Component Example'
        ]);
    })->name('nawasara-docs.components.table');

    Route::get('/components/base', function () {
        return view('nawasara-docs::pages.examples.base', [
            'title' => 'Base Component Example'
        ]);
    })->name('nawasara-docs.components.base');

    Route::get('/components/form', function () {
        return view('nawasara-docs::pages.examples.form', [
            'title' => 'Form Component Example'
        ]);
    })->name('nawasara-docs.components.form');
});
