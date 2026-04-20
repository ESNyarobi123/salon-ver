<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BackfillGlobalWaiterNumbers extends Command
{
    protected $signature = 'waiters:backfill-global-numbers
                            {--dry-run : List waiters that would be updated without saving}
                            {--migrate-legacy : Also replace legacy TIPTAP-W-##### codes with new 8-hex ids}';

    protected $description = 'Assign global_waiter_number (8 hex) to waiters missing one; optionally migrate legacy TIPTAP-W-##### codes';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $migrateLegacy = $this->option('migrate-legacy');

        $waiters = User::role('waiter')
            ->where(function ($q) use ($migrateLegacy) {
                $q->whereNull('global_waiter_number');
                if ($migrateLegacy) {
                    $q->orWhere('global_waiter_number', 'like', 'TIPTAP-W-%');
                }
            })
            ->orderBy('id')
            ->get();

        if ($waiters->isEmpty()) {
            $this->info($migrateLegacy
                ? 'No waiters found without a number or with legacy TIPTAP-W- codes.'
                : 'No waiters without a global number found.');

            return self::SUCCESS;
        }

        $this->info($dryRun
            ? 'Would assign global numbers to '.$waiters->count().' waiter(s):'
            : 'Assigning global numbers to '.$waiters->count().' waiter(s)...');

        $pendingReserved = [];
        foreach ($waiters as $waiter) {
            $previous = $waiter->global_waiter_number;
            $number = User::generateGlobalWaiterNumber($dryRun ? $pendingReserved : []);
            if ($dryRun) {
                $pendingReserved[] = $number;
            }
            $this->line("  {$waiter->name} (id: {$waiter->id})".($previous ? " ← {$previous}" : '')." → {$number}");
            if (! $dryRun) {
                $waiter->global_waiter_number = $number;
                $waiter->save();
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Run without --dry-run to apply changes.');
        } else {
            $this->info('Done. These waiters can now be found by manager search (Link Waiter).');
        }

        return self::SUCCESS;
    }
}
