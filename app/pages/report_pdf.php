<?php
require_once('../utils/tcpdf/tcpdf.php');
include '../utils/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: ../index.php");
    exit();
}

$conn = get_mysql_connection();

$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];

$query = "
    SELECT o.id, o.order_date, o.total_amount, u.name as user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.order_date BETWEEN ? AND ?
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
$conn->close();

// Creación del PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Reporte de Ventas');
$pdf->SetSubject('Reporte de Ventas Generado');
$pdf->SetKeywords('Reporte, Ventas, PDF');

// Configuración de la página
$pdf->SetMargins(15, 27, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

// Encabezado del documento
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Ventas', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Ventas hechas entre: ' . $fecha_inicio . ' y ' . $fecha_fin, 0, 1, 'C');
$pdf->Ln(10);

// Tabla de datos
$tbl = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
    }
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
</style>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

foreach ($orders as $order) {
    $tbl .= '<tr>';
    $tbl .= '<td>' . $order['id'] . '</td>';
    $tbl .= '<td>' . date('d/m/Y H:i', strtotime($order['order_date'])) . '</td>';
    $tbl .= '<td>' . $order['user_name'] . '</td>';
    $tbl .= '<td>' . number_format($order['total_amount'], 2) . '</td>';
    $tbl .= '</tr>';
}

$tbl .= '
    </tbody>
</table>';

// Agregar la tabla al PDF
$pdf->writeHTML($tbl, true, false, true, false, '');

// Pie de página con fecha de generación
$pdf->SetY(-30);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 10, 'Reporte generado el ' . date('d/m/Y'), 0, 0, 'C');

// Salida del PDF
$pdf->Output('reporte_ventas.pdf', 'I');
?>
