<x-nawasara-ui::layouts.app>
    <x-slot:title>
        Table Component - Nawasara Core
    </x-slot:title>

    <x-slot name="breadcrumb">
        <livewire:nawasara-ui.shared-components.breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => '/'],
            ['label' => 'Component', 'url' => '/'],
            ['label' => 'Table'],
        ]" />
    </x-slot>

    <x-nawasara-ui::page.container>
        <x-slot name="title">
            <x-nawasara-ui::page.title>Tambah User</x-nawasara-ui::page.title>
        </x-slot>

        <x-slot name="actions">
            <x-nawasara-ui::page.actions>
                <a href="{{ '/' }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    Kembali
                </a>
            </x-nawasara-ui::page.actions>
        </x-slot>

        {{-- <x-nawasara-ui::page.card> --}}
        <x-nawasara-ui::table :headers="['Aksi', 'Tanggal', 'Jenis Kegiatan']" title="Data Kegiatan Gerakan Penanaman Pohon" useSearch="true">
            <!-- Table Content -->
            <x-slot:table>
                <tr>
                    <td class="p-4"><x-nawasara-ui::dropdown-menu-action id="1" modalName="deleteModal"
                            :items="[
                                [
                                    'label' => 'Edit',
                                    'type' => 'href-navigate',
                                    'url' => '#',
                                    'permission' => 'village.edit',
                                ],
                                [
                                    'label' => 'Delete',
                                    'type' => 'delete',
                                    'permission' => 'village.delete',
                                ],
                                [
                                    'label' => 'Custom Action',
                                    'type' => 'click',
                                    'action' => 'doSomething',
                                    'param' => 1,
                                    'permission' => 'village.custom',
                                ],
                            ]" />
                    </td>
                    <td class="p-4">2 Januari 2025</td>
                    <td class="p-4">Reboisasi</td>
                </tr>
                <td class="p-4"><x-nawasara-ui::dropdown-menu-action id="2" modalName="deleteModal"
                        :items="[
                            [
                                'label' => 'Edit',
                                'type' => 'href-navigate',
                                'url' => '#',
                                'permission' => 'village.edit',
                            ],
                            [
                                'label' => 'Delete',
                                'type' => 'delete',
                                'permission' => 'village.delete',
                            ],
                            [
                                'label' => 'Custom Action',
                                'type' => 'click',
                                'action' => 'doSomething',
                                'param' => 1,
                                'permission' => 'village.custom',
                            ],
                        ]" />
                </td>
                <td class="p-4">03 Juni 2025</td>
                <td class="p-4">CSR</td>
                </tr>
            </x-slot:table>

            <!-- Footer for Pagination -->
            <x-slot:footer>
                {{-- {{ $this->items->links() }} --}}
            </x-slot:footer>
        </x-nawasara-ui::table>
        {{-- </x-nawasara-ui::page.card> --}}
    </x-nawasara-ui::page.container>

</x-nawasara-ui::layouts.app>
