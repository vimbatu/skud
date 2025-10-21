<div>
    @if ($attendances->isEmpty())
        <flux:card class="mt-4">
            <div class="flex items-center space-x-2">
                По вашему запросу ничего не найдено
            </div>
        </flux:card>
    @else
        <flux:table :paginate="$attendances">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'month_year'" :direction="$sortDirection" wire:click="sort('month_year')">
                    Месяц / год
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'department'" :direction="$sortDirection" wire:click="sort('department')">
                    Подразделение
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'employee'" :direction="$sortDirection" wire:click="sort('employee')">
                    Сотрудник
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'plan_hours'" :direction="$sortDirection" wire:click="sort('plan_hours')">
                    Часы (план)
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'fact_hours'" :direction="$sortDirection" wire:click="sort('fact_hours')">
                    Часы (факт)
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'avg_in'" :direction="$sortDirection" wire:click="sort('avg_in')">
                    Среднее время прихода
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'avg_out'" :direction="$sortDirection" wire:click="sort('avg_out')">
                    Среднее время ухода
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($attendances as $a)
                    <flux:table.row :key="$loop->index" @class(['bg-amber-50' => $a['has_deviation']])>
                        <flux:table.cell>{{ $a['month_year'] }}</flux:table.cell>
                        <flux:table.cell>{{ $a['department'] }}</flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('reports.detail', $a['employee']->id) }}" class="underline">
                                {{ $a['employee']->name }}
                            </a>
                        </flux:table.cell>

                        <flux:table.cell>{{ $a['plan_hours'] ?: '—' }}</flux:table.cell>

                        <flux:table.cell @class(['!text-red-600' => $a['has_deviation']])>
                            {{ $a['fact_hours'] }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $a['avg_in'] }}</flux:table.cell>
                        <flux:table.cell>{{ $a['avg_out'] }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
