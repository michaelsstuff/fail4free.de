<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DeployController extends Controller
{
    public function show(): View
    {
        $this->ensureDeployEnabled();

        return view('deploy');
    }

    public function run(Request $request): View|JsonResponse
    {
        $expectedKey = $this->ensureDeployEnabled();

        $providedKey = (string) ($request->header('X-Deploy-Key') ?: $request->input('deploy_key', ''));

        if ($providedKey === '' || ! hash_equals($expectedKey, $providedKey)) {
            Log::warning('Unauthorized deploy attempt', ['ip' => $request->ip()]);
            abort(403, 'Ungültiger Deploy-Key.');
        }

        $outputs = [];

        Artisan::call('optimize:clear');
        $outputs['optimize:clear'] = trim(Artisan::output());

        Artisan::call('migrate', ['--force' => true]);
        $outputs['migrate'] = trim(Artisan::output());

        $seededAdmin = false;
        if (! User::query()->exists()) {
            Artisan::call('db:seed', [
                '--class' => AdminUserSeeder::class,
                '--force' => true,
            ]);
            $outputs['admin_seed'] = trim(Artisan::output());
            $seededAdmin = User::query()->exists();
        }

        Log::info('Web deploy executed', [
            'ip' => $request->ip(),
            'seeded_admin' => $seededAdmin,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'seeded_admin' => $seededAdmin,
                'outputs' => $outputs,
            ]);
        }

        return view('deploy', [
            'executed' => true,
            'seededAdmin' => $seededAdmin,
            'outputs' => $outputs,
        ]);
    }

    private function ensureDeployEnabled(): string
    {
        $expectedKey = config('deploy.key');

        if (! is_string($expectedKey) || trim($expectedKey) === '') {
            abort(404);
        }

        return trim($expectedKey);
    }
}
