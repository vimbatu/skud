<div class="flex flex-col gap-3 w-full">

    {{-- Поля ввода --}}
    <div class="flex flex-wrap items-center gap-2">
        <flux:input wire:model.defer="name" placeholder="Фамилия Имя" class="w-48" />

        <flux:select wire:model.defer="department_id" class="w-48">
            <option value="">Без подразделения</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </flux:select>

        <flux:input wire:model.defer="position" placeholder="Должность" class="w-48" />
    </div>

    {{-- Кнопки --}}
    <div class="flex items-center justify-between gap-4 w-full">
        <flux:button wire:click="addEmployee">
            Добавить
        </flux:button>

        <div class="flex items-center gap-2">
            <div class="w-[10rem] shrink-0">
                <flux:select wire:model="month">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">
                            {{ Str::ucfirst(\Carbon\Carbon::create()->month($i)->locale('ru')->translatedFormat('F')) }}
                        </option>
                    @endfor
                </flux:select>
            </div>

            <flux:select wire:model="year">
                @for($i = 2024; $i <= 2028; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </flux:select>

            <flux:button wire:click="syncPlan" variant="primary">
                Обновить план
            </flux:button>
        </div>
    </div>
</div>
