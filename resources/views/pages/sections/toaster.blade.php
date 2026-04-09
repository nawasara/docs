<div>
    <div class="mt-6">
        <h3 @click="Toast.success('Success message!')">Basic Toaster</h3>
        {{-- @include('nawasara-docs::pages.blade-component.toaster') --}}
        <x-nawasara-ui::button @click="Toast.success('Success message!')">
            Toaster Success
        </x-nawasara-ui::button>

        <x-nawasara-ui::button color="danger" @click="Toast.error('Error message!')">
            Toaster Error
        </x-nawasara-ui::button>

        <x-nawasara-ui::button color="danger" @click="Toast.warning('Error message!')">
            Toaster Warning
        </x-nawasara-ui::button>

        <x-nawasara-ui::button color="success" @click="Toast.info('Error message!')">
            Toaster Info
        </x-nawasara-ui::button>

        <x-nawasara-ui::button color="success"
            @click="Toast.warning('<b>Toaster</b> Loading message!', {showProgress: true})">
            Toaster show progress
        </x-nawasara-ui::button>
    </div>
</div>
