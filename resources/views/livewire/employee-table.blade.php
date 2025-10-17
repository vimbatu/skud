<flux:table>
    <flux:table.columns>
        <flux:table.column>Подразделение</flux:table.column>
        <flux:table.column>Должность</flux:table.column>
        <flux:table.column>Сотрудник</flux:table.column>
        <flux:table.column>Плановые часы</flux:table.column>
        <flux:table.column></flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($employees as $e)
            <flux:table.row :key="$e->id">
                <flux:table.cell>
                    <flux:select wire:change="updateDepartment({{ $e }}, $event.target.value)" class="w-48">
                        <option value="">—</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected($d->id == $e->department_id)>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </flux:select>
                </flux:table.cell>

                <flux:table.cell>
                    <flux:input type="text" class="w-48"
                                value="{{ $e->position ?? '' }}"
                                wire:input="updatePosition({{ $e }}, $event.target.value)" />
                </flux:table.cell>

                <flux:table.cell>
                    <a href="{{ route('reports.detail', $e->id) }}" class="underline">
                        {{ $e->name }}
                    </a>
                </flux:table.cell>

                <flux:table.cell>
                    <flux:input disabled value="{{ $e->planHours->sum('hours') ?: '—' }}" />
                </flux:table.cell>

                <flux:table.cell class="text-right">
                    <flux:button size="sm" variant="outline" class="text-red-600"
                                 wire:click="delete({{ $e }})"
                                 onclick="return confirm('Удалить {{ $e->name }}?')">
                        Удалить
                    </flux:button>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
