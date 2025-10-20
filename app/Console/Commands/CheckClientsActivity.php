<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class CheckClientsActivity extends Command
{
    protected $signature = 'clients:check-activity';
    protected $description = 'Check all clients for last activity and update clients.last_activity_at';

    public function handle()
    {
        $lastChecked = DB::table('clients_last_checked')->where('id', 1)->value('last_checked');

        if ($lastChecked && now()->diffInHours($lastChecked) < 24) {
            $this->info("Skipped: Last checked {$lastChecked} (less than 24 hours ago).");
            return 0;
        }

        $this->info('Checking client activity...');

        $clients = Client::all();

        foreach ($clients as $client) {
            try {
                if (empty($client->db_host) ||
                    empty($client->db_port) ||
                    empty($client->db_name) ||
                    empty($client->db_username) ||
                    empty($client->db_password))
                {
                    continue;
                }

                $password = $client->db_password != '' ? Crypt::decryptString($client->db_password) : '';

                config([
                    'database.connections.dynamic_client' => [
                        'driver'    => 'mysql',
                        'host'      => $client->db_host,
                        'port'      => $client->db_port,
                        'database'  => $client->db_name,
                        'username'  => $client->db_username,
                        'password'  => $password,
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix'    => '',
                    ]
                ]);
                $lastSession = DB::connection('dynamic_client')->table('user_sessions')
                    ->orderBy('login_time', 'desc')
                    ->value('login_time');

                if ($lastSession) {
                    $client->last_activity = date('Y-m-d', strtotime($lastSession));
                    $client->is_active     = ($lastSession && Carbon::parse($lastSession)->lt(now()->subMonths(3))) ? 0 : 1;
                    $client->save();
                }

            } catch (\Exception $e) {
                $this->error("Failed for client {$client->id}: " . $e->getMessage());
            }
        }

        DB::table('clients_last_checked')->updateOrInsert(
            ['id' => 1],
            ['last_checked' => now()]
        );

        $this->info('All client activity checked successfully.');
    }
}
