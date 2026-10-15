<?php

namespace Zanbug\Laravel\Console;

use Zanbug\Sdk\Zanbug;
use Illuminate\Console\Command;

/**
 * php artisan zanbug:update            upgrade every zanbug/* package to the newest release
 * php artisan zanbug:update --check    only report (exit 10 when an update is available)
 * php artisan zanbug:update --yes      no confirmation (cron / CI)
 * php artisan zanbug:update --dry-run  show what composer would do
 *
 * Thin wrapper over the core Updater so Symfony/CI/Yii users get the same
 * behaviour from `vendor/bin/zanbug update`.
 */
class UpdateCommand extends Command
{
    protected $signature = 'zanbug:update {--check : Only check, do not install} {--yes : Do not ask for confirmation} {--dry-run : Show what would change}';

    protected $description = 'Upgrade the Zanbug SDK packages (zanbug/*) to the newest published release';

    public function handle()
    {
        if (!class_exists('Zanbug\\Sdk\\Support\\Updater') || !method_exists('Zanbug\\Sdk\\Zanbug', 'checkForUpdate')) {
            $this->error('zanbug/php-sdk >= 1.7.0 is required for self-update. Run: composer require zanbug/php-sdk:^1.7.0 zanbug/laravel:^1.7.0');

            return 1;
        }

        $check = Zanbug::checkForUpdate();
        if ($check['error'] !== null) {
            $this->error('Zanbug: ' . $check['error']);

            return 1;
        }
        if (!$check['update_available']) {
            $this->info('Zanbug SDK is up to date (' . $check['current'] . ').');

            return 0;
        }
        $this->line('Zanbug SDK ' . $check['current'] . ' -> <info>' . $check['latest'] . '</info> is available.');
        if ($this->option('check')) {
            return 10;
        }
        $dry = (bool) $this->option('dry-run');
        if (!$dry && !$this->option('yes') && !$this->confirm('Run composer require for zanbug/* ^' . $check['latest'] . ' now?', true)) {
            $this->line('Aborted.');

            return 1;
        }

        $out = $this->output;
        $res = Zanbug::update($dry, function ($line) use ($out) {
            $out->writeln('  ' . $line);
        });
        if ($res['ok']) {
            $this->info($res['message']);
            if (!$dry && $res['mode'] !== 'none') {
                $this->comment('Restart queue workers / Octane / Horizon so they load the new code (php artisan queue:restart).');
            }

            return 0;
        }
        $this->error($res['message']);

        return 1;
    }
}
