<x-layouts.app :title="'Сводная таблица'">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="mb-4">
            <flux:heading level="2" size="xl">Сводная таблица</flux:heading>
            <flux:text class="text-sm text-zinc-500">Отчет по данным СКУД и планам</flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button
                href="{{ route('reports.export', request()->query()) }}"
                variant="primary"
                size="sm"
                icon="arrow-down-tray">
                Экспорт Excel
            </flux:button>
        </div>
    </div>

    @include('components.filter-form')

    <livewire:report-summary-table
        :from="request('from')"
        :to="request('to')"
        :employee="request('employee')"
        :only_deviations="request('only_deviations')" />

</x-layouts.app>
