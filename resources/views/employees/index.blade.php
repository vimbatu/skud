@php use Carbon\Carbon; @endphp
<x-layouts.app :title="'Сотрудники'">

    <livewire:employee-header/>
    <flux:separator class="my-5"/>
    <livewire:employee-table/>

</x-layouts.app>
