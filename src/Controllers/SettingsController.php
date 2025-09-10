<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $config = config('traffic');
        return view('traffic-control::settings', compact('config'));
    }

    /**
     * Update traffic control settings.
     */
    public function update(Request $request)
    {
        // Validate request
        $validated = $this->validateRequest($request);

        // Transform inputs (arrays, booleans, numbers)
        $settings = $this->transformInput($validated);

        // Update the .env values for specific keys
        $this->updateEnv([
            'TRAFFIC_CONTROL_ENABLED'       => $settings['enabled'],
            'TRAFFIC_CONTROL_STORAGE'       => $settings['storage'],
            'TRAFFIC_CONTROL_SLACK_WEBHOOK' => $settings['alerts']['slack'],
            'TRAFFIC_CONTROL_ALERT_EMAIL'   => $settings['alerts']['email'],
        ]);

        // Save settings to config/traffic.php
        $this->saveConfig($settings);

        // Clear and rebuild config cache
        Artisan::call('config:clear');
        Artisan::call('config:cache');

        return redirect()
            ->back()
            ->with('success', 'Traffic control settings updated successfully.');
    }

    /**
     * Validate request input.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'enabled' => 'required|boolean',
            'storage' => 'required|string|in:redis,database,file',

            'rate_limits.default.requests' => 'required|integer|min:1',
            'rate_limits.default.per'      => 'required|integer|min:1',
            'rate_limits.api.requests'     => 'required|integer|min:1',
            'rate_limits.api.per'          => 'required|integer|min:1',

            'ip.blacklist' => 'nullable|string',
            'ip.whitelist' => 'nullable|string',
            'ip.block_tor' => 'required|boolean',

            'bot_detection.enabled'     => 'required|boolean',
            'bot_detection.user_agents' => 'nullable|string',

            'alerts.slack'     => 'nullable|string',
            'alerts.email'     => 'nullable|email',
            'alerts.threshold' => 'required|integer|min:1',

            'dashboard.enabled'    => 'required|boolean',
            'dashboard.prefix'     => 'required|string',
            'dashboard.middleware' => 'nullable|array',

            'api_quota.default' => 'required|integer|min:1',

            'logging.log_blocked'     => 'required|boolean',
            'logging.log_sample_rate' => 'required|integer|min:1',
        ]);
    }

    /**
     * Transform validated input into proper config format.
     */
    protected function transformInput(array $validated): array
    {
        // Comma-separated strings -> arrays
        $validated['ip']['blacklist'] = $this->toArray($validated['ip']['blacklist'] ?? '');
        $validated['ip']['whitelist'] = $this->toArray($validated['ip']['whitelist'] ?? '');
        $validated['bot_detection']['user_agents'] = $this->toArray($validated['bot_detection']['user_agents'] ?? '');

        // Dashboard middleware can be string or array
        if (is_string($validated['dashboard']['middleware'] ?? '')) {
            $validated['dashboard']['middleware'] = $this->toArray($validated['dashboard']['middleware']);
        }

        // Cast booleans
        $validated['enabled'] = (bool) $validated['enabled'];
        $validated['ip']['block_tor'] = (bool) $validated['ip']['block_tor'];
        $validated['bot_detection']['enabled'] = (bool) $validated['bot_detection']['enabled'];
        $validated['dashboard']['enabled'] = (bool) $validated['dashboard']['enabled'];
        $validated['logging']['log_blocked'] = (bool) $validated['logging']['log_blocked'];

        // Cast numeric strings to int
        $validated['rate_limits']['default']['requests'] = (int) $validated['rate_limits']['default']['requests'];
        $validated['rate_limits']['default']['per'] = (int) $validated['rate_limits']['default']['per'];
        $validated['rate_limits']['api']['requests'] = (int) $validated['rate_limits']['api']['requests'];
        $validated['rate_limits']['api']['per'] = (int) $validated['rate_limits']['api']['per'];
        $validated['alerts']['threshold'] = (int) $validated['alerts']['threshold'];
        $validated['api_quota']['default'] = (int) $validated['api_quota']['default'];
        $validated['logging']['log_sample_rate'] = (int) $validated['logging']['log_sample_rate'];

        return $validated;
    }

    /**
     * Save config back to traffic.php file with nice formatting, preserving env() for specific keys.
     */
    protected function saveConfig(array $settings): void
    {
        $envKeys = [
            'enabled' => 'TRAFFIC_CONTROL_ENABLED',
            'storage' => 'TRAFFIC_CONTROL_STORAGE',
            'alerts.slack' => 'TRAFFIC_CONTROL_SLACK_WEBHOOK',
            'alerts.email' => 'TRAFFIC_CONTROL_ALERT_EMAIL',
        ];

        $content = "<?php\n\nreturn [\n";

        foreach ($settings as $key => $value) {
            $content .= $this->arrayExportLine($key, $value, 1, $envKeys, $key);
        }

        $content .= "];\n";

        File::put(config_path('traffic.php'), $content);
    }

    /**
     * Recursive function to format array nicely, supports env() for certain keys.
     */
    protected function arrayExportLine(string $key, mixed $value, int $level = 1, array $envKeys = [], string $parentKey = ''): string
    {
        $indent = str_repeat('    ', $level);
        $line = '';
        $fullKey = $parentKey === $key ? $key : "$parentKey.$key";

        if (isset($envKeys[$fullKey])) {
            $line .= "$indent'$key' => env('{$envKeys[$fullKey]}', " . var_export($value, true) . "),\n";
        } elseif (is_array($value)) {
            $line .= "$indent'$key' => [\n";
            foreach ($value as $k => $v) {
                $line .= $this->arrayExportLine($k, $v, $level + 1, $envKeys, $fullKey);
            }
            $line .= "$indent],\n";
        } elseif (is_int($value) || is_float($value)) {
            $line .= "$indent'$key' => $value,\n";
        } elseif (is_bool($value)) {
            $line .= "$indent'$key' => " . ($value ? 'true' : 'false') . ",\n";
        } elseif (is_null($value)) {
            $line .= "$indent'$key' => null,\n";
        } else {
            $line .= "$indent'$key' => '$value',\n";
        }

        return $line;
    }

    /**
     * Convert comma-separated string into trimmed array.
     */
    protected function toArray(string $value): array
    {
        return array_filter(array_map('trim', explode(',', $value)));
    }

    /**
     * Update specific keys in the .env file. Adds key if missing.
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            return;
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = '';
            }

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }
}
