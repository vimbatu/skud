@props([
    'showFrom' => true,
    'showTo' => true,
    'showEmployee' => true,
    'showDeviations' => true,
])

<form method="GET" action="{{ url()->current() }}">
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            @if($showFrom)
                <flux:field>
                    <flux:input type="date" name="from" value="{{ request('from') }}" label="Дата с"/>
                </flux:field>
            @endif

            @if($showTo)
                <flux:field>
                    <flux:input type="date" name="to" value="{{ request('to') }}" label="Дата по"/>
                </flux:field>
            @endif

            @if($showEmployee)
                <flux:field>
                    <flux:input type="text" name="employee" value="{{ request('employee') }}" placeholder="ФИО"
                                label="Сотрудник"/>
                </flux:field>
            @endif

            @if($showDeviations)
                <flux:field>
                    <flux:label>Только отклонения</flux:label>
                    <flux:checkbox name="only_deviations"
                                   wire:model="only_deviations"
                                   value="1"
                                   :checked="request()->boolean('only_deviations')" />
                </flux:field>
            @endif
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
