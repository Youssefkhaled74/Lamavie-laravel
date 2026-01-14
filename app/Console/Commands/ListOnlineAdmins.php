<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Admin;

class ListOnlineAdmins extends Command
{
    protected $signature = 'admins:online {--ids : Show only admin IDs} {--minutes=5 : TTL minutes used to consider online}';
    protected $description = 'List online admins based on cache keys (admin_online:{id}).';

    public function handle()
    {
        $showIds = $this->option('ids');
        $minutes = (int) $this->option('minutes');

        $this->info("Checking admins online (TTL ~{$minutes} minutes)...");

        $admins = Admin::all();
        $rows = [];
        foreach ($admins as $admin) {
            $key = "admin_online:{$admin->id}";
            $isOnline = Cache::has($key);
            $lastSeen = Cache::get("admin_last_seen:{$admin->id}");
            if ($showIds) {
                if ($isOnline) $rows[] = [$admin->id];
            } else {
                $rows[] = [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                    $isOnline ? 'Online' : 'Offline',
                    $lastSeen ?? ($admin->last_login_at ? $admin->last_login_at->toDateTimeString() : 'Never'),
                ];
            }
        }

        if ($showIds) {
            foreach ($rows as $r) $this->line($r[0]);
            return 0;
        }

        $this->table(['ID','Name','Email','Status','Last Seen'], $rows);
        return 0;
    }
}
