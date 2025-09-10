<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ManageIpController extends Controller
{
    /**
     * Display the IPs.
     */
    public function index()
    {
        $blockedIps = config('traffic.ip.blacklist', []);
        $allowedIps = config('traffic.ip.whitelist', []);

        return view('traffic-control::manage-ip', compact(['blockedIps', 'allowedIps']));
    }

    /**
     * Store Block IP.
     */
    public function storeBlockIP(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        $ip = $request->input('ip'); // <- Use input from form

        $config = $this->loadConfig();
        $blockedIps = $config['ip']['blacklist'] ?? [];

        if (!in_array($ip, $blockedIps)) {
            $blockedIps[] = $ip;
            $config['ip']['blacklist'] = array_values($blockedIps);
            $this->saveConfig($config);
        }

        return redirect()->back()->with('success', "$ip added to blocked list.");
    }

    /**
     * Remove Block IP.
     */
    public function removeBlockIP($ip)
    {
        $config = $this->loadConfig();
        $blockedIps = $config['ip']['blacklist'] ?? [];
        $blockedIps = array_filter($blockedIps, fn($bip) => $bip !== $ip);
        $config['ip']['blacklist'] = array_values($blockedIps);

        $this->saveConfig($config);

        return redirect()->back()->with('success', "$ip removed from blocked list.");
    }

    /**
     * Store Allow IP.
     */
    public function storeAllowIP(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        $ip = $request->input('ip'); // <- Use input from form

        $config = $this->loadConfig();
        $allowedIps = $config['ip']['whitelist'] ?? [];

        if (!in_array($ip, $allowedIps)) {
            $allowedIps[] = $ip;
            $config['ip']['whitelist'] = array_values($allowedIps);
            $this->saveConfig($config);
        }

        return redirect()->back()->with('success', "$ip added to allowed list.");
    }

    /**
     * Remove Allow IP.
     */
    public function removeAllowIP($ip)
    {
        $config = $this->loadConfig();
        $allowedIps = $config['ip']['whitelist'] ?? [];
        $allowedIps = array_filter($allowedIps, fn($aip) => $aip !== $ip);
        $config['ip']['whitelist'] = array_values($allowedIps);

        $this->saveConfig($config);

        return redirect()->back()->with('success', "$ip removed from allowed list.");
    }

    /**
     * Load traffic.php config.
     */
    protected function loadConfig(): array
    {
        $configFile = config_path('traffic.php');
        return include $configFile;
    }

    /**
     * Save config array back to traffic.php using short array syntax.
     */
    protected function saveConfig(array $config)
    {
        $content = "<?php\n\nreturn " . $this->arrayToPhp($config) . ";\n";

        File::put(config_path('traffic.php'), $content);

        Artisan::call('config:clear');
        Artisan::call('config:cache');
    }

    /**
     * Convert array to PHP code using short array syntax with proper formatting.
     */
    protected function arrayToPhp(array $array, int $level = 0): string
    {
        $indent = str_repeat('    ', $level);
        $lines = [];
        $lines[] = '[';

        foreach ($array as $key => $value) {
            $keyPart = is_int($key) ? $key : "'$key'";
            if (is_array($value)) {
                $lines[] = $indent . '    ' . "$keyPart => " . $this->arrayToPhp($value, $level + 1) . ",";
            } elseif (is_bool($value)) {
                $lines[] = $indent . '    ' . "$keyPart => " . ($value ? 'true' : 'false') . ",";
            } elseif (is_null($value)) {
                $lines[] = $indent . '    ' . "$keyPart => null,";
            } elseif (is_int($value) || is_float($value)) {
                $lines[] = $indent . '    ' . "$keyPart => $value,";
            } else { // string
                $lines[] = $indent . '    ' . "$keyPart => '$value',";
            }
        }

        $lines[] = $indent . ']';
        return implode("\n", $lines);
    }
}
