<?php

namespace App\Repositories;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Builder;

class SystemSettingRepository
{
    protected SystemSetting $setting;

    public function __construct(SystemSetting $setting)
    {
        $this->setting = $setting;
    }

    public function getQuery(): Builder
    {
        return $this->setting->query();
    }

    public function all()
    {
        return $this->setting->get();
    }

    public function getAllKeyed(): array
    {
        return $this->setting->pluck('value', 'key')->toArray();
    }

    public function getByKey(string $key): ?SystemSetting
    {
        return $this->setting->where('key', $key)->first();
    }

    public function set(string $key, $value): SystemSetting
    {
        return $this->setting->updateOrCreate(['key' => $key], ['value' => $value ?? '']);
    }

    public function setMany(array $keyValuePairs): void
    {
        foreach ($keyValuePairs as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function delete(string $key): ?bool
    {
        return $this->setting->where('key', $key)->delete();
    }
}
