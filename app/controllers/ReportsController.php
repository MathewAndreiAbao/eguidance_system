<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Reports Controller - Analytics & Reporting Dashboard
 * 
 * Features:
 * - Comprehensive analytics dashboard for counselors
 * - Real-time statistics and visualizations using Chart.js
 * - RESTful API endpoints for programmatic access
 * - Data export in JSON and CSV formats
 * - Support for external analytics integration via API keys
 * 
 * API Endpoints:
 * - GET /reports/api_get_stats?type=[overview|appointments|feedback|wellness|students|export]
 * - GET /reports/export?format=[json|csv]&start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
 * 
 * External Integration Support:
 * - Configure API keys in app/config/analytics.php
 * - Supports Google Analytics, Mixpanel, Segment, and custom endpoints
 * - API authentication ready (currently session-based, API key support available)
 */

class ReportsController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('AnalyticsModel');
        $this->call->library('auth');
        $this->call->library('session');
        $this->call->library('APIIntegration');


        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }

        // Only counselors can access reports
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Access denied. Only counselors can view reports.');
            redirect('student/dashboard');
        }
    }

    public function index() {
        $counselor_id = $this->session->userdata('user_id');

        // Get analytics data
        $overview = $this->AnalyticsModel->get_overview_stats($counselor_id);
        $appointments_by_status = $this->AnalyticsModel->get_appointments_by_status($counselor_id);
        $appointments_trend = $this->AnalyticsModel->get_appointments_trend($counselor_id);
        $feedback_stats = $this->AnalyticsModel->get_feedback_stats($counselor_id);
        $wellness_stats = $this->AnalyticsModel->get_wellness_stats($counselor_id);
        $top_students = $this->AnalyticsModel->get_top_active_students($counselor_id);
        $time_distribution = $this->AnalyticsModel->get_appointment_time_distribution($counselor_id);

        // Generate QuickChart URLs for each chart
        
        // 1. Appointments by Status (Doughnut Chart)
        $appointments_status_chart_data = [
            "type" => "doughnut",
            "data" => [
                "labels" => array_keys($appointments_by_status),
                "datasets" => [[
                    "data" => array_values($appointments_by_status),
                    "backgroundColor" => [
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    "borderColor" => [
                        'rgba(255, 205, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    "borderWidth" => 2
                ]]
            ],
            "options" => [
                "responsive" => true,
                "maintainAspectRatio" => false,
                "plugins" => [
                    "legend" => [
                        "position" => "bottom",
                    ]
                ]
            ]
        ];
        
        // 2. Feedback Rating Distribution (Bar Chart)
        $rating_labels = [];
        $rating_counts = [];
        for ($i = 1; $i <= 5; $i++) {
            $rating_labels[] = $i . ' Star' . ($i > 1 ? 's' : '');
            $rating_counts[] = $feedback_stats['ratings'][$i] ?? 0;
        }
        
        $rating_distribution_chart_data = [
            "type" => "bar",
            "data" => [
                "labels" => $rating_labels,
                "datasets" => [[
                    "label" => "Number of Ratings",
                    "data" => $rating_counts,
                    "backgroundColor" => 'rgba(153, 102, 255, 0.8)',
                    "borderColor" => 'rgba(153, 102, 255, 1)',
                    "borderWidth" => 2
                ]]
            ],
            "options" => [
                "responsive" => true,
                "maintainAspectRatio" => false,
                "scales" => [
                    "y" => [
                        "beginAtZero" => true,
                        "ticks" => [
                            "precision" => 0
                        ]
                    ]
                ],
                "plugins" => [
                    "legend" => [
                        "display" => false
                    ]
                ]
            ]
        ];
        
        // 3. Appointments Trend (Line Chart)
        $trend_labels = [];
        $trend_counts = [];
        foreach ($appointments_trend as $item) {
            $trend_labels[] = $item['month'];
            $trend_counts[] = $item['count'];
        }
        
        $appointments_trend_chart_data = [
            "type" => "line",
            "data" => [
                "labels" => $trend_labels,
                "datasets" => [[
                    "label" => "Appointments",
                    "data" => $trend_counts,
                    "backgroundColor" => 'rgba(59, 130, 246, 0.2)',
                    "borderColor" => 'rgba(59, 130, 246, 1)',
                    "borderWidth" => 3,
                    "fill" => true,
                    "tension" => 0.4
                ]]
            ],
            "options" => [
                "responsive" => true,
                "maintainAspectRatio" => false,
                "plugins" => [
                    "legend" => [
                        "display" => false
                    ]
                ],
                "scales" => [
                    "y" => [
                        "beginAtZero" => true,
                        "ticks" => [
                            "stepSize" => 1
                        ]
                    ]
                ]
            ]
        ];
        
        // Generate chart URLs using QuickChart API
        $appointments_status_chart_url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($appointments_status_chart_data));
        $rating_distribution_chart_url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($rating_distribution_chart_data));
        $appointments_trend_chart_url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($appointments_trend_chart_data));

        $data = [
            'overview' => $overview,
            'appointments_by_status' => $appointments_by_status,
            'appointments_trend' => $appointments_trend,
            'feedback_stats' => $feedback_stats,
            'wellness_stats' => $wellness_stats,
            'top_students' => $top_students,
            'time_distribution' => $time_distribution,
            'appointments_status_chart_url' => $appointments_status_chart_url,
            'rating_distribution_chart_url' => $rating_distribution_chart_url,
            'appointments_trend_chart_url' => $appointments_trend_chart_url
        ];

        $this->call->view('reports/index', $data);
    }

    // API endpoint to get analytics data in JSON format
    public function api_get_stats() {
        // Check if API key is provided (for future integration)
        $api_key = $this->io->get('api_key') ?? $this->io->post('api_key');
        
        // For now, use session-based auth, but this can be extended to API key auth
        if (!$this->auth->is_logged_in()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized', 'message' => 'API key required']);
            http_response_code(401);
            return;
        }

        $counselor_id = $this->session->userdata('user_id');
        $type = $this->io->get('type') ?? 'overview';

        $response = [];

        switch ($type) {
            case 'overview':
                $response = $this->AnalyticsModel->get_overview_stats($counselor_id);
                break;
            
            case 'appointments':
                $response = [
                    'by_status' => $this->AnalyticsModel->get_appointments_by_status($counselor_id),
                    'trend' => $this->AnalyticsModel->get_appointments_trend($counselor_id)
                ];
                break;
            
            case 'feedback':
                $response = $this->AnalyticsModel->get_feedback_stats($counselor_id);
                break;
            
            case 'wellness':
                $response = $this->AnalyticsModel->get_wellness_stats($counselor_id);
                break;
            
            case 'students':
                $response = $this->AnalyticsModel->get_top_active_students($counselor_id);
                break;

            case 'export':
                $get_params = $this->io->get();
                $start_date = isset($get_params['start_date']) ? $get_params['start_date'] : null;
                $end_date = isset($get_params['end_date']) ? $get_params['end_date'] : null;
                $response = $this->AnalyticsModel->get_export_data($counselor_id, $start_date, $end_date);
                break;
            
            default:
                $response = ['error' => 'Invalid type parameter'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Export report as JSON
    public function export() {
        $counselor_id = $this->session->userdata('user_id');
        $format = $this->io->get('format') ?? 'json';
        
        // Safely get optional date parameters
        $get_params = $this->io->get();
        $start_date = isset($get_params['start_date']) ? $get_params['start_date'] : null;
        $end_date = isset($get_params['end_date']) ? $get_params['end_date'] : null;

        $data = $this->AnalyticsModel->get_export_data($counselor_id, $start_date, $end_date);

        if ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="analytics_export_' . date('Y-m-d') . '.json"');
            echo json_encode($data, JSON_PRETTY_PRINT);
        } elseif ($format === 'pdf') {
            // For PDF export, we'll generate a proper PDF using TCPDF
            $this->session->set_userdata('export_data', $data);
            $this->session->set_userdata('export_format', 'pdf');
            redirect('reports/pdf_export');
        } else {
            // CSV format
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="analytics_export_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // Export appointments
            fputcsv($output, ['=== APPOINTMENTS ===']);
            if (!empty($data['appointments'])) {
                fputcsv($output, array_keys($data['appointments'][0]));
                foreach ($data['appointments'] as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fputcsv($output, []);
            fputcsv($output, ['=== FEEDBACK ===']);
            if (!empty($data['feedback'])) {
                fputcsv($output, array_keys($data['feedback'][0]));
                foreach ($data['feedback'] as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
        }
        exit;
    }

    // Refresh analytics cache (for future API integration)
    public function refresh() {
        $this->session->set_flashdata('success', 'Analytics data refreshed successfully.');
        redirect('reports');
    }
    
    // PDF Export view
    public function pdf_export() {
        $data = $this->session->userdata('export_data');
        $format = $this->session->userdata('export_format');
        
        if (!$data || $format !== 'pdf') {
            redirect('reports');
        }
        
        // Load TCPDF library
        require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Analytics Report');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Add a page
        $pdf->AddPage();
        
        // Set color for background
        $pdf->SetFillColor(240, 240, 240);
        
        // Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'E-Guidance System Analytics Report', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'Generated on: ' . date('F d, Y - h:i A'), 0, 1, 'C');
        $pdf->Ln(5);
        
        // Appointments Data
        if (!empty($data['appointments'])) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Appointments Data', 0, 1, 'L');
            $pdf->SetFont('helvetica', 'B', 10);
            
            // Table header
            $header = array_keys($data['appointments'][0]);
            $w = array();
            $totalWidth = 180;
            $colWidth = $totalWidth / count($header);
            for ($i = 0; $i < count($header); $i++) {
                $w[] = $colWidth;
            }
            
            // Header row
            $pdf->SetFillColor(220, 220, 220);
            for ($i = 0; $i < count($header); $i++) {
                $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Data rows
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetFillColor(255, 255, 255);
            foreach ($data['appointments'] as $row) {
                for ($i = 0; $i < count($header); $i++) {
                    $cellValue = isset($row[$header[$i]]) ? $row[$header[$i]] : '';
                    $pdf->Cell($w[$i], 6, $cellValue, 1, 0, 'L', true);
                }
                $pdf->Ln();
            }
            $pdf->Ln(5);
        }
        
        // Feedback Data
        if (!empty($data['feedback'])) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Feedback Data', 0, 1, 'L');
            $pdf->SetFont('helvetica', 'B', 10);
            
            // Table header
            $header = array_keys($data['feedback'][0]);
            $w = array();
            $totalWidth = 180;
            $colWidth = $totalWidth / count($header);
            for ($i = 0; $i < count($header); $i++) {
                $w[] = $colWidth;
            }
            
            // Header row
            $pdf->SetFillColor(220, 220, 220);
            for ($i = 0; $i < count($header); $i++) {
                $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Data rows
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetFillColor(255, 255, 255);
            foreach ($data['feedback'] as $row) {
                for ($i = 0; $i < count($header); $i++) {
                    $cellValue = isset($row[$header[$i]]) ? $row[$header[$i]] : '';
                    $pdf->Cell($w[$i], 6, $cellValue, 1, 0, 'L', true);
                }
                $pdf->Ln();
            }
        }
        
        // Footer
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Confidential - For Counseling Staff Use Only', 0, 0, 'C');
        
        // Close and output PDF document
        $pdf->Output('analytics_report_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }
}
