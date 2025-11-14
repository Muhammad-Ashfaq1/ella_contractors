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

        if ($this->input->is_cli_request()) {
            $key = $this->resolve_cli_key($key);
        }

        if (defined('APP_CRON_KEY') && APP_CRON_KEY !== '' && APP_CRON_KEY !== $key) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Invalid cron key.');
        }

        if (!function_exists('ella_run_reminder_dispatch')) {
            require_once module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php');
        }

        $result = ella_run_reminder_dispatch();

        if ($this->input->is_cli_request()) {
            echo json_encode(array_merge(['success' => true], $result)) . PHP_EOL;
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array_merge([
                    'success' => true,
                ], $result)));
        }
    }

    /**
     * Resolve cron key when executed via CLI.
     *
     * Allows supplying the key as either:
     *   php index.php ella_contractors reminders cron --key=SECRET
     *   php index.php ella_contractors reminders cron SECRET
     *
     * If no key is provided but APP_CRON_KEY is defined, that constant is used.
     *
     * @param string|null $key
     * @return string|null
     */
    private function resolve_cli_key($key)
    {
        if (!empty($key)) {
            return $key;
        }

        $argv = $this->input->server('argv');

        if (is_array($argv)) {
            foreach ($argv as $arg) {
                if (strpos($arg, '--key=') === 0) {
                    return substr($arg, 6);
                }
            }

            // Support positional argument (4th param) e.g. php index.php module controller method KEY
            if (isset($argv[4]) && strpos($argv[4], 'index.php') === false && strpos($argv[4], '--') !== 0) {
                return $argv[4];
            }
        }

        if (defined('APP_CRON_KEY') && APP_CRON_KEY !== '') {
            return APP_CRON_KEY;
        }

        return null;
    }

    /**
     * Temporary test endpoint to verify SMTP delivery.
     * Usage (HTTP): /ella_contractors/reminders/test_email?key=APP_CRON_KEY
     * Usage (CLI):  php index.php ella_contractors reminders test_email --key=APP_CRON_KEY
     */
    public function test_email()
    {
        $key = $this->input->get('key', true);
        if ($this->input->is_cli_request()) {
            $key = $this->resolve_cli_key($key);
        }

        if (defined('APP_CRON_KEY') && APP_CRON_KEY !== '' && APP_CRON_KEY !== $key) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Invalid cron key.');
        }

        $CI =& get_instance();
        $CI->load->library('email');

        $to        = 'mashfaq86861@gmail.com';
        $fromEmail = get_option('smtp_email');
        $fromName  = get_option('companyname') ?: 'Ella Contractors CRM';

        if (empty($fromEmail) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $fromEmail = 'noreply@ellasbubbles.com';
        }

        $subject = 'Ella Contractors Reminder SMTP Test (' . date('Y-m-d H:i:s') . ')';
        $message = '<p>This is a live SMTP test sent from the Ella Contractors reminder cron environment.</p>'
                 . '<p>Timestamp: ' . date('c') . '</p>';

        $CI->email->clear(true);
        $CI->email->from($fromEmail, $fromName);
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);
        $CI->email->SMTPDebug = 2;
        $CI->email->set_debug_output('error_log');

        $sent  = $CI->email->send();
        $debug = $CI->email->print_debugger(['headers', 'subject', 'body']);

        if (!$sent) {
            log_message('error', 'EllaContractors Test Email failed: ' . $debug);
        }

        $response = [
            'success'   => (bool) $sent,
            'sent_to'   => $to,
            'timestamp' => date('c'),
            'debug'     => $debug,
        ];

        if ($this->input->is_cli_request()) {
            echo json_encode($response) . PHP_EOL;
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }
}

