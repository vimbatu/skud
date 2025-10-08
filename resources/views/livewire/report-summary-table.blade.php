<flux:table :paginate="$attendances">
    <flux:table.columns>
        <flux:table.column sortable :sorted="$sortBy === 'department_id'" :direction="$sortDirection"
                           wire:click="sort('department_id')">
            Подразделение
        </flux:table.column>

        <flux:table.column sortable :sorted="$sortBy === 'employee_id'" :direction="$sortDirection"
                           wire:click="sort('employee_id')">
            Сотрудник
        </flux:table.column>

        <flux:table.column sortable :sorted="$sortBy === 'time_in'" :direction="$sortDirection"
                           wire:click="sort('time_in')">Приход
        </flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'time_out'" :direction="$sortDirection"
                           wire:click="sort('time_out')">Уход
        </flux:table.column>

        <flux:table.column sortable :sorted="$sortBy === 'worked_hours'" :direction="$sortDirection"
                           wire:click="sort('worked_hours')">
            Часы (факт)
        </flux:table.column>

        <flux:table.column sortable :sorted="$sortBy === 'plan_hours'" :direction="$sortDirection"
                           wire:click="sort('plan_hours')">
            План
        </flux:table.column>

                <flux:table.column>Отклонение</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">
            Дата
        </flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($attendances as $a)
            <flux:table.row :key="$a->id" @class(['bg-amber-50' => (bool) $a->deviation])>
                <flux:table.cell>{{ $a->employee->department->name ?? '—' }}</flux:table.cell>

                <flux:table.cell>
                    <a href="{{ route('reports.detail', $a->employee->id) }}" class="underline">
                        {{ $a->employee->name }}
                    </a>
                </flux:table.cell>

                <flux:table.cell variant="strong" class="{{ $a->time_in_color }}">
                    {{ $a->time_in ?? '—' }}
                </flux:table.cell>

                <flux:table.cell variant="strong" class="{{ $a->time_out_color }}">
                    {{ $a->time_out ?? '—' }}
                </flux:table.cell>

                <flux:table.cell variant="strong" class="{{ $a->worked_hours_color }}">
                    {{ $a->worked_hours ?? '—' }}
                </flux:table.cell>

                <flux:table.cell>{{ $a->plan_hours ?? '—' }}</flux:table.cell>

                <flux:table.cell>
                    @if($a->deviation)
                        <flux:badge variant="solid" color="amber" size="sm">{{ $a->deviation }}</flux:badge>
                    @else
                        <flux:badge variant="solid" color="green" size="sm">OK</flux:badge>
                    @endif
                </flux:table.cell>

                <flux:table.cell>{{ $a->date->format('d.m.Y') }}</flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
