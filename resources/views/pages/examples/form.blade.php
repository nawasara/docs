<x-nawasara-ui::layouts.app>
    <x-slot:title>
        Form Component - Nawasara Core
    </x-slot:title>

    <x-slot name="breadcrumb">
        <livewire:nawasara-ui.shared-components.breadcrumb :items="[['label' => 'Dashboard', 'url' => '/'], ['label' => 'Users', 'url' => '/'], ['label' => 'Create']]" />
    </x-slot>
    <x-nawasara-ui::page.container>

        <x-slot name="title">
            <x-nawasara-ui::page.title>Form - Nawasara Core</x-nawasara-ui::page.title>
        </x-slot>
        <x-nawasara-ui::page.card>
            <form class="space-y-6" method="POST" action="#">
                @csrf
                <div>
                    <x-nawasara-ui::form.input id="name" name="name" label="Name" placeholder="Enter your name"
                        required autofocus />
                </div>
                <div>
                    <x-nawasara-ui::form.label for="email" label="Email" />
                    <x-nawasara-ui::form.input id="email" name="email" type="email" label=""
                        placeholder="Enter your email" required />
                </div>
                <div>
                    <x-nawasara-ui::form.label for="password" label="Password" />
                    <x-nawasara-ui::form.input id="password" name="password" usePasswordField="true"
                        useGenPassword="true" label="" placeholder="Enter your password" required />
                </div>
                <div>
                    <x-nawasara-ui::form.label for="gender" label="Gender" />
                    <div class="flex gap-4">
                        <x-nawasara-ui::form.radio id="male" name="gender" value="male" label="Male" />
                        <x-nawasara-ui::form.radio id="female" name="gender" value="female" label="Female" />
                    </div>
                </div>
                <div>
                    <x-nawasara-ui::form.label for="role" label="Role" />
                    <x-nawasara-ui::form.select id="role" name="role">
                        <option value="">Select role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="guest">Guest</option>
                    </x-nawasara-ui::form.select>
                </div>

                <div>
                    <x-nawasara-ui::form.label for="dropdown" label="Role (Dropdown)" />
                    <x-nawasara-ui::form.select-dropdown name="dropdown" defaultValue="user">
                        <button type="button" class="hs-dropdown-item w-full text-left" value="admin">Admin</button>
                        <button type="button" class="hs-dropdown-item w-full text-left" value="user">User</button>
                        <button type="button" class="hs-dropdown-item w-full text-left" value="guest">Guest</button>
                    </x-nawasara-ui::form.select-dropdown>
                </div>
                <div>
                    <x-nawasara-ui::form.label for="bio" label="Bio" />
                    <x-nawasara-ui::form.textarea id="bio" name="bio" label="Biodata"
                        placeholder="Tell us about yourself..." rows="3" />
                </div>
                <div class="flex items-center">
                    <x-nawasara-ui::form.checkbox id="agree" name="agree" label="label" />
                    <label for="agree" class="ml-2 text-sm text-gray-600">I agree to the terms and conditions</label>
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Submit</button>
                </div>
            </form>
        </x-nawasara-ui::page.card>
    </x-nawasara-ui::page.container>

</x-nawasara-ui::layouts.app>
