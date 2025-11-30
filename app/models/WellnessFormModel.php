<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class WellnessFormModel extends Model {
    protected $table = 'wellness_forms';
    protected $primary_key = 'id';

    public function get_active_forms_with_submission_flag($student_id = null) {
        $forms = $this->db->table('wellness_forms wf')
                          ->join('users u', 'wf.counselor_id = u.id', 'LEFT')
                          ->select('wf.*, u.username as counselor_name')
                          ->where('wf.is_active', 1)
                          ->order_by('wf.created_at DESC')
                          ->get_all();

        if (empty($forms) || !$student_id) {
            return $forms;
        }

        $submissions = $this->db->table('wellness_form_responses')
                                ->select('wellness_form_id')
                                ->where('student_id', $student_id)
                                ->get_all();
        $submitted_ids = array_column($submissions, 'wellness_form_id');

        return array_map(function ($form) use ($submitted_ids) {
            $form['has_submitted'] = in_array($form['id'], $submitted_ids);
            return $form;
        }, $forms);
    }

    public function get_forms_by_counselor($counselor_id) {
        return $this->db->table('wellness_forms wf')
                        ->select('wf.*')
                        ->where('wf.counselor_id', $counselor_id)
                        ->order_by('wf.created_at DESC')
                        ->get_all();
    }

    public function get_form_with_author($id) {
        return $this->db->table('wellness_forms wf')
                        ->join('users u', 'wf.counselor_id = u.id', 'LEFT')
                        ->select('wf.*, u.username as counselor_name')
                        ->where('wf.id', $id)
                        ->get();
    }

    public function get_questions($form_id) {
        return $this->db->table('wellness_form_questions')
                        ->where('wellness_form_id', $form_id)
                        ->order_by('id ASC')
                        ->get_all();
    }

    public function create_form_with_questions($form_data, $questions) {
        $form_id = $this->insert($form_data);

        foreach ($questions as $question) {
            $question['wellness_form_id'] = $form_id;
            
            // Always remove scale fields to avoid database errors
            // This is a temporary workaround until the database schema is updated
            unset($question['scale_min']);
            unset($question['scale_max']);
            
            $this->db->table('wellness_form_questions')->insert($question);
        }

        return $form_id;
    }

    public function has_student_submitted($form_id, $student_id) {
        $response = $this->db->table('wellness_form_responses')
                             ->where([
                                 'wellness_form_id' => $form_id,
                                 'student_id' => $student_id
                             ])
                             ->limit(1)
                             ->get();
        return !empty($response);
    }

    public function save_response($form_id, $student_id, $answers) {
        if ($this->has_student_submitted($form_id, $student_id)) {
            return false;
        }

        $response_id = $this->db->table('wellness_form_responses')->insert([
            'wellness_form_id' => $form_id,
            'student_id' => $student_id,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);

        foreach ($answers as $question_id => $answer_text) {
            $this->db->table('wellness_form_answers')->insert([
                'response_id' => $response_id,
                'question_id' => $question_id,
                'answer_text' => $answer_text
            ]);
        }

        return $response_id;
    }

    public function get_responses_with_answers($form_id) {
        $responses = $this->db->table('wellness_form_responses r')
                              ->join('users u', 'r.student_id = u.id', 'LEFT')
                              ->select('r.*, u.username as student_name')
                              ->where('r.wellness_form_id', $form_id)
                              ->order_by('r.submitted_at DESC')
                              ->get_all();

        if (empty($responses)) {
            return [];
        }

        $answers = $this->db->table('wellness_form_answers a')
                            ->join('wellness_form_questions q', 'a.question_id = q.id', 'LEFT')
                            ->select('a.*, q.question_text, q.question_type, q.wellness_form_id')
                            ->where('q.wellness_form_id', $form_id)
                            ->get_all();

        $grouped_answers = [];
        foreach ($answers as $answer) {
            $grouped_answers[$answer['response_id']][] = $answer;
        }

        foreach ($responses as &$response) {
            $response['answers'] = $grouped_answers[$response['id']] ?? [];
        }

        return $responses;
    }
    
    public function count_all() {
        return $this->db->table('wellness_forms')->count();
    }
    
    public function count_by_counselor($counselor_id) {
        return $this->db->table('wellness_forms')->where('counselor_id', $counselor_id)->count();
    }
    
    public function get_all() {
        return $this->db->table('wellness_forms wf')
                        ->join('users u', 'wf.counselor_id = u.id', 'LEFT')
                        ->select('wf.*, u.username as counselor_name')
                        ->order_by('wf.created_at DESC')
                        ->get_all();
    }
}
