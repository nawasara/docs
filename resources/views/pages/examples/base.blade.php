<x-nawasara-ui::layouts.app>
    <x-slot:title>
        Blade Component - Nawasara Core
    </x-slot:title>

    <x-slot name="breadcrumb">
        <livewire:nawasara-ui.shared-components.breadcrumb :items="[['label' => 'Dashboard', 'url' => '/'], ['label' => 'Base Component', 'url' => '/']]" />
    </x-slot>
    <x-nawasara-ui::page.container>

        <x-slot name="title">
            <x-nawasara-ui::page.title>Blade Component - Nawasara Core</x-nawasara-ui::page.title>
        </x-slot>
        <x-nawasara-ui::page.card>
            @include('nawasara-docs::pages.sections.button')
            @include('nawasara-docs::pages.sections.button-group')
            @include('nawasara-docs::pages.sections.toaster')
            @include('nawasara-docs::pages.sections.modal')
        </x-nawasara-ui::page.card>
    </x-nawasara-ui::page.container>

</x-nawasara-ui::layouts.app>
