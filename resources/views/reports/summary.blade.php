<x-layouts.app :title="'Сводная таблица'">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="mb-4">
            <flux:heading level="2" size="xl">Сводная таблица</flux:heading>
            <flux:text class="text-sm text-zinc-500">Отчет по данным СКУД и планам</flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button
                href="{{ route('reports.summary.export', request()->query()) }}"
                variant="primary"
                size="sm"
                icon="arrow-down-tray">
                Экспорт Excel
            </flux:button>
        </div>
    </div>

    @include('components.filter-form-summary')

    <livewire:report-summary-table
        :employee="request('employee')"
        :department="request('department')"
        :date="request('date')"
        :only_deviations="request('only_deviations')" />

</x-layouts.app>
