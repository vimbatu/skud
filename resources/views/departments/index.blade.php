<x-layouts.app :title="'Подразделения'">

    {{-- Форма добавления --}}
    <form method="POST" action="{{ route('departments.store') }}" class="mb-6 flex gap-2">
        @csrf
        <flux:input name="name" placeholder="Название подразделения" />
        <flux:button type="submit">Добавить</flux:button>
    </form>

    {{-- Таблица --}}
    <flux:table>

        <flux:table.rows>
            @foreach ($departments as $d)
                <flux:table.row :key="$d->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('departments.update', $d) }}"
                                  onsubmit="return confirm('Обновить подразделение {{ $d->name }}?')"
                                  class="flex flex-1 items-center gap-2">
                                @csrf
                                @method('PUT')

                                <flux:input
                                    placeholder="Название подразделения"
                                    name="name"
                                    value="{{ $d->name }}"
                                />
                                <flux:button type="submit" size="sm">Обновить</flux:button>
                            </form>

                            <form method="POST" action="{{ route('departments.destroy', $d) }}"
                                  onsubmit="return confirm('Удалить подразделение {{ $d->name }}?')">
                                @csrf
                                @method('DELETE')
                                <flux:button variant="danger" type="submit" size="sm">Удалить</flux:button>
                            </form>
                        </div>
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $departments->links() }}
    </div>

</x-layouts.app>
