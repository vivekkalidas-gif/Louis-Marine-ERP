{{-- <option>s for the equipment-category select on the certificate forms.
     Categories without an active default template are shown but disabled, so they
     can't be chosen (the form would only show "No template found").
     Params: $categories (collection), $selected (current category id or null). --}}
@php
    $templatedCategoryIds = \App\Models\CertificateTemplate::where('is_active', true)
        ->where('is_default', true)
        ->pluck('equipment_category_id')
        ->map(fn ($id) => (string) $id)
        ->all();
    $selected = isset($selected) ? (string) $selected : null;
@endphp
@foreach($categories->sortBy(fn ($c) => [in_array((string) $c->id, $templatedCategoryIds, true) ? 0 : 1, $c->name]) as $category)
    @php
        $hasTemplate = in_array((string) $category->id, $templatedCategoryIds, true);
        $isSelected = $selected !== null && $selected === (string) $category->id;
    @endphp
    <option value="{{ $category->id }}" @selected($isSelected) @disabled(! $hasTemplate && ! $isSelected)>
        {{ $category->name }}{{ $hasTemplate ? '' : ' — no template yet' }}
    </option>
@endforeach
