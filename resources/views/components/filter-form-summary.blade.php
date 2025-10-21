@php
    use App\Models\Attendance;
    use App\Models\Department;
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp
<form method="GET" action="{{ url()->current() }}">
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <flux:field>
                <flux:input type="text" name="employee" value="{{ request('employee') }}"
                            placeholder="Введите фамилию или имя сотрудника"
                            label="Сотрудник"/>
            </flux:field>

            <flux:table.cell>
                <flux:select name="department" label="Подразделение">
                    <option value="">—</option>
                    @foreach(Department::all()->pluck('name') as $name)
                        <option value="{{ $name }}" @selected($name == request('department'))>
                            {{ $name }}
                        </option>
                    @endforeach
                </flux:select>
            </flux:table.cell>

            <flux:table.cell>
                <div class="flex items-center gap-2">
                    <flux:select name="month" label="Месяц" class="w-48">
                        <option value="">—</option>
                        @for ($i = 1; $i <= 12; $i++)
                            @php
                                $monthName = Str::ucfirst(
                                    Carbon::create()->month($i)->locale('ru')->translatedFormat('F')
                                );
                            @endphp
                            <option value="{{ $i }}" @selected($i == request('month'))>
                                {{ $monthName }}
                            </option>
                        @endfor
                    </flux:select>

                    <flux:select name="year" label="Год" class="w-32">
                        <option value="">—</option>
                        @foreach(
                            Attendance::selectRaw('YEAR(date) as year')
                                ->distinct()
                                ->orderBy('year', 'desc')
                                ->pluck('year')
                            as $year
                        )
                            <option value="{{ $year }}" @selected($year == (request('year') ?? now()->year))>
                                {{ $year }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
            </flux:table.cell>

            <flux:field>
                <flux:label>Только отклонения</flux:label>
                <flux:checkbox name="only_deviations"
                               wire:model="only_deviations"
                               value="1"
                               :checked="request()->boolean('only_deviations')"/>
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
