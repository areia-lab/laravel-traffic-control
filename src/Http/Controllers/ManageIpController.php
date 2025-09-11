<?php

namespace AreiaLab\TrafficControl\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ManageIpController extends Controller
{
    public function index()
    {
        $blockedIps = config('traffic.ip.blacklist', []);
        $allowedIps = config('traffic.ip.whitelist', []);

        return view('traffic-control::manage-ip', compact('blockedIps', 'allowedIps'));
    }

    public function storeBlockIP(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        $ip = $request->input('ip');

        $config = $this->loadConfig();
        $blockedIps = $config['ip']['blacklist'] ?? [];

        if (!in_array($ip, $blockedIps)) {
            $blockedIps[] = $ip;
        }

        $config['ip']['blacklist'] = $this->cleanList($blockedIps);
        $this->saveIpSection($config['ip']);

        return redirect()->back()->with('success', "$ip added to blocked list.");
    }

    public function removeBlockIP($ip)
    {
        $config = $this->loadConfig();
        $blockedIps = $config['ip']['blacklist'] ?? [];
        $blockedIps = array_filter($blockedIps, fn($bip) => $bip !== $ip);

        $config['ip']['blacklist'] = $this->cleanList($blockedIps);
        $this->saveIpSection($config['ip']);

        return redirect()->back()->with('success', "$ip removed from blocked list.");
    }

    public function storeAllowIP(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        $ip = $request->input('ip');

        $config = $this->loadConfig();
        $allowedIps = $config['ip']['whitelist'] ?? [];

        if (!in_array($ip, $allowedIps)) {
            $allowedIps[] = $ip;
        }

        $config['ip']['whitelist'] = $this->cleanList($allowedIps);
        $this->saveIpSection($config['ip']);

        return redirect()->back()->with('success', "$ip added to allowed list.");
    }

    public function removeAllowIP($ip)
    {
        $config = $this->loadConfig();
        $allowedIps = $config['ip']['whitelist'] ?? [];
        $allowedIps = array_filter($allowedIps, fn($aip) => $aip !== $ip);

        $config['ip']['whitelist'] = $this->cleanList($allowedIps);
        $this->saveIpSection($config['ip']);

        return redirect()->back()->with('success', "$ip removed from allowed list.");
    }

    protected function loadConfig(): array
    {
        return include config_path('traffic.php');
    }

    /**
     * Only replace the 'ip' section in traffic.php
     */
    protected function saveIpSection(array $ipConfig)
    {
        $configFile = config_path('traffic.php');
        $content = file_get_contents($configFile);

        // Convert the new blacklist and whitelist arrays to PHP strings
        $blacklist = $this->arrayToPhp($ipConfig['blacklist'], 2);
        $whitelist = $this->arrayToPhp($ipConfig['whitelist'], 2);

        // Regex patterns to replace only blacklist and whitelist
        $content = preg_replace(
            '/(\'blacklist\'\s*=>\s*)\[[^\]]*\]/s',
            "'blacklist' => " . $blacklist,
            $content
        );

        $content = preg_replace(
            '/(\'whitelist\'\s*=>\s*)\[[^\]]*\]/s',
            "'whitelist' => " . $whitelist,
            $content
        );

        File::put($configFile, $content);

        Artisan::call('config:clear');
        Artisan::call('config:cache');
    }

    protected function arrayToPhp(array $array, int $level = 0): string
    {
        $indent = str_repeat('    ', $level);
        $lines = ['['];

        foreach ($array as $key => $value) {
            $keyPart = is_int($key) ? '' : "'$key' => ";
            if (is_array($value)) {
                $lines[] = $indent . '    ' . $keyPart . $this->arrayToPhp($value, $level + 1) . ",";
            } elseif (is_bool($value)) {
                $lines[] = $indent . '    ' . $keyPart . ($value ? 'true' : 'false') . ",";
            } elseif (is_null($value)) {
                $lines[] = $indent . '    ' . $keyPart . 'null,';
            } elseif (is_int($value) || is_float($value)) {
                $lines[] = $indent . '    ' . $keyPart . $value . ",";
            } else {
                $lines[] = $indent . '    ' . $keyPart . "'$value',";
            }
        }

        $lines[] = $indent . ']';
        return implode("\n", $lines);
    }

    protected function cleanList(array $list): array
    {
        $list = array_unique($list);
        sort($list, SORT_STRING);
        return array_values($list);
    }
}
