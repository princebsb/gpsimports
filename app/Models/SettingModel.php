<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected static array $cache = [];

    /**
     * Get a setting value by key
     */
    public function get(string $key, $default = null)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $setting = $this->where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        $value = $this->castValue($setting['value'], $setting['type']);
        self::$cache[$key] = $value;

        return $value;
    }

    /**
     * Set a setting value
     */
    public function setValue(string $key, $value, string $group = 'general', string $type = 'text'): bool
    {
        $existing = $this->where('key', $key)->first();

        if ($existing) {
            $result = $this->update($existing['id'], ['value' => $value]);
        } else {
            $result = $this->insert([
                'key' => $key,
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]);
        }

        if ($result) {
            self::$cache[$key] = $value;
        }

        return (bool) $result;
    }

    /**
     * Get all settings by group
     */
    public function getByGroup(string $group): array
    {
        $settings = $this->where('group', $group)->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }

        return $result;
    }

    /**
     * Get all settings
     */
    public function getAll(): array
    {
        $settings = $this->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }

        return $result;
    }

    /**
     * Cast value based on type
     */
    protected function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : 0,
            'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        self::$cache = [];
    }
}
