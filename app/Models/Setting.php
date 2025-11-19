<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting->value) ? (str_contains($setting->value, '.') ? (float) $setting->value : (int) $setting->value) : $default,
            'json' => json_decode($setting->value, true) ?? $default,
            default => $setting->value ?? $default,
        };
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        $setting->type = $type;
        $setting->group = $group;
        $setting->save();

        return $setting;
    }
}
