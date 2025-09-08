<?php defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Measurements_model', 'measurements_model');
    }

    public function index($category = 'windows')
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $allowed = ['windows','doors','roofing','siding','other'];
        if (!in_array($category, $allowed)) {
            $category = 'windows';
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($this->measurements_model->list($category, $this->input->get()));
            return;
        }

        $data['title']    = 'Measurements - ' . ucfirst($category);
        $data['category'] = $category;
        $this->load->view('ella_contractors/measurements/list', $data);
    }

    public function save()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $post = $this->input->post(null, true);
        $id   = isset($post['id']) ? (int) $post['id'] : 0;

        $width  = (float) ($post['width_val'] ?? 0);
        $height = (float) ($post['height_val'] ?? 0);
        if ($width && $height) {
            if (!isset($post['united_inches_val']) || $post['united_inches_val'] === '') {
                $post['united_inches_val'] = $width + $height;
            }
            if (!isset($post['area_val']) || $post['area_val'] === '') {
                $lenU  = $post['length_unit'] ?? 'in';
                $areaU = $post['area_unit'] ?? 'sqft';
                if ($lenU === 'in' && $areaU === 'sqft') {
                    $post['area_val'] = ($width * $height) / 144.0;
                }
            }
        }

        if (isset($post['attributes_json']) && is_string($post['attributes_json']) && $post['attributes_json'] !== '') {
            $decoded = json_decode($post['attributes_json'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $post['attributes_json'] = json_encode($decoded);
            }
        } else {
            unset($post['attributes_json']);
        }

        if ($id > 0) {
            $ok  = $this->measurements_model->update($id, $post);
            $msg = $ok ? 'Updated successfully' : 'Nothing changed';
        } else {
            $ok  = (bool) $this->measurements_model->create($post);
            $msg = $ok ? 'Created successfully' : 'Failed to create';
        }

        set_alert($ok ? 'success' : 'danger', $msg);
        redirect(admin_url('ella_contractors/measurements/' . ($post['category'] ?? 'windows')));
    }

    public function delete($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        $ok = $this->measurements_model->delete($id);
        set_alert($ok ? 'success' : 'danger', $ok ? 'Deleted' : 'Not found');
        redirect($_SERVER['HTTP_REFERER'] ?? admin_url('ella_contractors/measurements'));
    }
}
