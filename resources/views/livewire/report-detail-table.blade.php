<div>
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="my-4 w-full">
            <flux:card class="space-y-6 p-6">
                <div>
                    <flux:heading size="lg">Средние показатели за период</flux:heading>
                    <flux:text class="text-sm text-zinc-500">
                        {{ $from ?? 'начало' }} – {{ $to ?? 'сегодня' }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <flux:heading size="md">Средний приход</flux:heading>
                        <flux:text class="text-xl font-bold text-emerald-600">
                            {{ $avgTimeIn ?? '—' }}
                        </flux:text>
                    </div>

                    <div>
                        <flux:heading size="md">Средний уход</flux:heading>
                        <flux:text class="text-xl font-bold text-rose-600">
                            {{ $avgTimeOut ?? '—' }}
                        </flux:text>
                    </div>

                    <div>
                        <flux:heading size="md">Средняя смена</flux:heading>
                        <flux:text class="text-xl font-bold text-indigo-600">
                            {{ $avgWorked ?? '—' }}
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>

    <flux:table :paginate="$records">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection"
                               wire:click="sort('date')">
                Дата
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'time_in'" :direction="$sortDirection"
                               wire:click="sort('time_in')">
                Приход
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'time_out'" :direction="$sortDirection"
                               wire:click="sort('time_out')">
                Уход
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'worked_hours'" :direction="$sortDirection"
                               wire:click="sort('worked_hours')">
                Часы (факт)
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'worked_hours'" :direction="$sortDirection"
                               wire:click="sort('worked_hours')">
                Отклонение
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'plan_hours'" :direction="$sortDirection"
                               wire:click="sort('plan_hours')">
                План
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'deviation'" :direction="$sortDirection"
                               wire:click="sort('deviation')">
                Отклонение
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'absence_type'" :direction="$sortDirection"
                               wire:click="sort('absence_type')">
                Вид отсутствия
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($records as $r)
                <flux:table.row :key="$r->id" @class(['bg-amber-50' => (bool) $r->deviation])>

                    <flux:table.cell>{{ $r->date?->format('d.m.Y') ?? '—' }}</flux:table.cell>

                    <flux:table.cell variant="strong" class="{{ $r->time_in_color }}">
                        {{ $r->time_in ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell variant="strong" class="{{ $r->time_out_color }}">
                        {{ $r->time_out ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell variant="strong" class="{{ $r->worked_hours_color }}">
                        {{ $r->worked_hours ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell variant="strong" class="{{ $r->deviation_color }}">
                        {{ \App\Services\ExcelExportService::deviationTime($r->worked_hours, $r->employee->currentHours($r->date) ?? '—') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:input type="text"
                                    style="{{ $r->plan_hours_style }}"
                                    value="{{ $r->employee->currentHours($r->date) ?? '—' }}"
                                    wire:input="updateHours({{ $r }}, $event.target.value)" />
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($r->deviation)
                            <flux:badge variant="solid" color="amber" size="sm">{{ $r->deviation }}</flux:badge>
                        @else
                            <flux:badge variant="solid" color="green" size="sm">OK</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:select wire:change="updateAbsence({{ $r }}, $event.target.value)" class="w-48">
                            <option value="">—</option>
                            @foreach($absences as $name)
                                <option value="{{ $name }}" @selected($name == $r->absence_type)>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
