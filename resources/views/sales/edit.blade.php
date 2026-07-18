@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Update Data</h1>
    </div>

    <form action="{{ route('updatedata', $data->id) }}" method="POST" class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        @csrf
        @include('partials.sales-form-fields', ['dropdownData' => $dropdownData, 'data' => $data, 'lockedOrgCode' => $lockedOrgCode])
        <div class="flex justify-end border-t border-slate-100 pt-4">
            <button type="submit" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                @include('partials.icon', ['name' => 'pencil', 'class' => 'h-4 w-4'])
                Update
            </button>
        </div>
    </form>
</div>
@endsection
