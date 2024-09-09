<?php
require '../vendor/autoload.php'; // Adjust path as needed
require_once '../includes/functions.php'; // Include your functions file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ensure the script only runs when accessed directly
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['export'])) {
    try {
        $pdo = getDbConnection(); // Use the existing getDbConnection function from functions.php

        // Fetch orders based on the current filter criteria
        // (you might want to add filtering logic here if needed)

        $query = "SELECT * FROM customers ORDER BY created_at DESC";
        $stmt = $pdo->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Shipping Address');
        $sheet->setCellValue('D1', 'Nearest Landmark');
        $sheet->setCellValue('E1', 'Contact Info');
        $sheet->setCellValue('F1', 'Items Purchased');
        $sheet->setCellValue('G1', 'Price');
        $sheet->setCellValue('H1', 'Payment Method');
        $sheet->setCellValue('I1', 'Payment Status');
        $sheet->setCellValue('J1', 'Paid Amount');
        $sheet->setCellValue('K1', 'Remaining Amount');
        $sheet->setCellValue('L1', 'Created At');

        // Populate rows
        $row = 2;
        foreach ($result as $order) {
            // Decode JSON data for items_purchased
            $itemsPurchased = json_decode($order['items_purchased'], true);
            $itemsText = '';

            if (is_array($itemsPurchased)) {
                foreach ($itemsPurchased as $item) {
                    $itemsText .= htmlspecialchars($item['name']) . ' - Rs. ' . htmlspecialchars($item['price']) . ' x ' . htmlspecialchars($item['quantity']) . '; ';
                }
            } else {
                $itemsText = 'No items';
            }

            $sheet->setCellValue('A' . $row, $order['order_id']);
            $sheet->setCellValue('B' . $row, $order['full_name']);
            $sheet->setCellValue('C' . $row, $order['shipping_address']);
            $sheet->setCellValue('D' . $row, $order['nearest_landmark']);
            $sheet->setCellValue('E' . $row, $order['contact_information']);
            $sheet->setCellValue('F' . $row, $itemsText);
            $sheet->setCellValue('G' . $row, $order['price']);
            $sheet->setCellValue('H' . $row, $order['payment_method']);
            $sheet->setCellValue('I' . $row, $order['payment_status']);
            $sheet->setCellValue('J' . $row, $order['paid_amount']);
            $sheet->setCellValue('K' . $row, $order['remaining_amount']);
            $sheet->setCellValue('L' . $row, $order['created_at']);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'orders_export_' . date('Y-m-d') . '.xlsx';

        // Clear any previous output
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Output the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        die("Error exporting orders: " . htmlspecialchars($e->getMessage()));
    }
}
?>
