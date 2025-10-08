<form method="GET" action="{{ url()->current() }}">
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <flux:field>
                <flux:input type="text" name="employee" value="{{ request('employee') }}" placeholder="ФИО"
                            label="Сотрудник"/>
            </flux:field>

            <flux:table.cell>
                <flux:select name="department" label="Подразделение">
                    <option value="">—</option>
                    @foreach(\App\Models\Department::all()->pluck('name') as $name)
                        <option value="{{ $name }}" @selected($name == request('department'))>
                            {{ $name }}
                        </option>
                    @endforeach
                </flux:select>
            </flux:table.cell>

            <flux:table.cell>
                <flux:select name="date" label="Период">
                    <option value="">—</option>
                    @foreach(
                        \App\Models\Attendance::orderBy('date')
                            ->pluck('date')
                            ->map(fn ($date) => $date->translatedFormat('F Y'))
                            ->unique()
                    as $date)
                        <option value="{{ $date }}" @selected($date == request('date'))>
                            {{ mb_ucfirst($date) }}
                        </option>
                    @endforeach
                </flux:select>
            </flux:table.cell>

            <flux:field>
                <flux:label>Только отклонения</flux:label>
                <flux:checkbox name="only_deviations"
                               wire:model="only_deviations"
                               value="1"
                               :checked="request()->boolean('only_deviations')" />
            </flux:field>
        </div>

        <div class="mt-4 flex gap-2">
            <flux:button type="submit" variant="primary" icon="funnel">
                Применить
            </flux:button>
            <flux:button type="reset" href="{{ url()->current() }}" variant="primary">
                Сбросить
            </flux:button>
        </div>
    </flux:card>
</form>
