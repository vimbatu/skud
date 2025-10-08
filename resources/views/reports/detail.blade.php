<x-layouts.app :title="'Детализация по сотруднику'">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="mb-4">
            <flux:heading level="2" size="xl">Детализация по сотруднику</flux:heading>
            <flux:text class="text-sm text-zinc-500">{{ $employee->name }}</flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button
                href="{{ route('reports.export', [...request()->query(), 'employee_id' => request('id')]) }}"
                variant="primary"
                size="sm"
                icon="arrow-down-tray">
                Экспорт Excel
            </flux:button>
        </div>
    </div>

    @include('components.filter-form', ['showEmployee' => false])

    <livewire:report-detail-table
        :employee-id="$employee->id"
        :from="request('from')"
        :to="request('to')"
        :only_deviations="request('only_deviations')" />

</x-layouts.app>
