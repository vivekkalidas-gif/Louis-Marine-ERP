<?php
// Shared setup for the PDF certificate views (loaded with require inside the view, so $certificate is in scope).
// Returns $brand, $sig (closure), $fmt (closure), $statusValid for extract().
$s = config('settings', []);
$imgData = function (?string $path) {
    if (! $path) return null;
    $rel = preg_replace('#^/?(public/)?storage/#', '', ltrim($path, '/'));
    $file = storage_path('app/public/' . $rel);
    if (! is_file($file)) return null;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'][$ext] ?? null;
    return $mime ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file)) : null;
};

$brand = [
    'name' => $s['company_name'] ?? config('app.name'),
    'address' => trim(preg_replace('/\s*\R\s*/', ', ', (string) ($s['company_address'] ?? ''))),
    'phone' => $s['company_phone'] ?? null,
    'email' => $s['company_email'] ?? null,
    'website' => isset($s['company_website']) ? preg_replace('#^https?://|/$#', '', $s['company_website']) : null,
    'logo' => $imgData($s['company_logo'] ?? null),
];

$sig = fn ($user) => $user ? $imgData($user->signature_path ?? null) : null;

$fmt = function ($field, $raw) {
    if ($raw === null || $raw === '') return '—';
    if ($field && $field->field_type === 'date') {
        try { return \Carbon\Carbon::parse($raw)->format('d M Y'); } catch (\Throwable $e) {}
    }
    return $raw;
};

$statusValid = ! $certificate->expiry_date || $certificate->expiry_date->endOfDay()->isFuture();

return compact('brand', 'sig', 'fmt', 'statusValid');
