@php
  $editing = isset($sector);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="text-sm font-medium">Nome</label>
    <input type="text" name="name" value="{{ old('name', $sector->name ?? '') }}" required
           class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-sm font-medium">Código</label>
    <input type="text" name="code" value="{{ old('code', $sector->code ?? '') }}"
           placeholder="ex.: DSO, FIN, TIC"
           class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    @error('code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-sm font-medium">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $sector->slug ?? '') }}"
           class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-sm font-medium">Email do setor</label>
    <input type="email" name="email" value="{{ old('email', $sector->email ?? '') }}"
           class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    @error('email')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="md:col-span-2">
    <label class="text-sm font-medium">Descrição</label>
    <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">{{ old('description', $sector->description ?? '') }}</textarea>
    @error('description')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="inline-flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active', ($sector->is_active ?? true))) class="rounded">
      Ativo
    </label>
  </div>

  <div>
    <label class="text-sm font-medium">Ordem (opcional)</label>
    <input type="number" name="order_column" value="{{ old('order_column', $sector->order_column ?? 0) }}"
           class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    @error('order_column')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="md:col-span-2">
    <label class="text-sm font-medium">Membros (staff) do setor</label>
    <select name="users[]" multiple size="8"
            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
      @foreach($users as $u)
        <option value="{{ $u->id }}"
          @selected(in_array($u->id, old('users', $selected ?? [])))>
          {{ $u->name }} — {{ $u->email }}
        </option>
      @endforeach
    </select>
    @error('users')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>
</div>
