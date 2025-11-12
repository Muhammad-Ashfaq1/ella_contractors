<?php defined('BASEPATH') or exit('No direct script access allowed');

class Reminders extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cron endpoint to process Ella Contractors reminders.
     * Usage: /ella_contractors/reminders/cron?key=APP_CRON_KEY
     */
    public function cron()
    {
        $key = $this->input->get('key', true);

        if (defined('APP_CRON_KEY') && APP_CRON_KEY !== '' && APP_CRON_KEY !== $key) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Invalid cron key.');
        }

        if (!function_exists('ella_run_reminder_dispatch')) {
            require_once module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php');
        }

        $result = ella_run_reminder_dispatch();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_merge([
                'success' => true,
            ], $result)));
    }
}

