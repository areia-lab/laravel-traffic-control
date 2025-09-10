<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ip = $request->input('ip');
        $blockedIps = config('traffic.ip.blacklist', []);

        if (!in_array($ip, $blockedIps)) {
            $blockedIps[] = $ip;

            // Update config or store persistently (file/db)
            $this->updateConfig('blacklist', $blockedIps);
        }

        return redirect()->back()->with('success', "$ip added to blocked list.");
    }

    /**
     * Remove Block IP.
     */
    public function removeBlockIP($ip)
    {
        $blockedIps = config('traffic.ip.blacklist', []);
        $blockedIps = array_filter($blockedIps, fn($bip) => $bip !== $ip);

        $this->updateConfig('blacklist', array_values($blockedIps));

        return redirect()->back()->with('success', "$ip removed from blocked list.");
    }

    /**
     * Store Allow IP.
     */
    public function storeAllowIP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ip = $request->input('ip');
        $allowedIps = config('traffic.ip.whitelist', []);

        if (!in_array($ip, $allowedIps)) {
            $allowedIps[] = $ip;

            $this->updateConfig('whitelist', $allowedIps);
        }

        return redirect()->back()->with('success', "$ip added to allowed list.");
    }

    /**
     * Remove Allow IP.
     */
    public function removeAllowIP($ip)
    {
        $allowedIps = config('traffic.ip.whitelist', []);
        $allowedIps = array_filter($allowedIps, fn($aip) => $aip !== $ip);

        $this->updateConfig('whitelist', array_values($allowedIps));

        return redirect()->back()->with('success', "$ip removed from allowed list.");
    }

    /**
     * Helper function to update config dynamically.
     * WARNING: This only writes to a config cache file for demonstration.
     * In production, use a database or a proper storage method.
     */
    protected function updateConfig(string $key, array $values)
    {
        $configFile = config_path('traffic.php');
        $config = include $configFile;

        $config['ip'][$key] = $values;

        // Convert array to PHP code
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";

        file_put_contents($configFile, $content);
    }
}
