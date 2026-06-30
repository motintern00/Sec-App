<?php

namespace App\Services;

use App\Models\Attendance;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceExportService
{
    public function exportCsv(array $filters = []): StreamedResponse
    {
        $query = $this->buildQuery($filters);
        $filename = 'laporan-absensi-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $this->headers());

            $query->chunk(200, function ($attendances) use ($handle) {
                foreach ($attendances as $attendance) {
                    fputcsv($handle, $this->row($attendance));
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportXlsx(array $filters = []): StreamedResponse
    {
        $query = $this->buildQuery($filters);
        $filename = 'laporan-absensi-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($query) {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Absensi');
            $sheet->fromArray($this->headers(), null, 'A1');

            $row = 2;
            $query->chunk(200, function ($attendances) use ($sheet, &$row) {
                foreach ($attendances as $attendance) {
                    $sheet->fromArray($this->row($attendance), null, 'A'.$row);
                    $row++;
                }
            });

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildQuery(array $filters)
    {
        $query = Attendance::query()
            ->with(['employee.department', 'employee.shift', 'recorder'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_at');

        if (! empty($filters['start_date'])) {
            $query->whereDate('attendance_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('attendance_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        if (! empty($filters['name'])) {
            $query->whereHas('employee', fn ($q) => $q->where('name', 'like', '%'.$filters['name'].'%'));
        }

        return $query;
    }

    private function headers(): array
    {
        return ['Tanggal', 'Nama Pegawai', 'Departemen', 'Shift', 'Jam Masuk', 'Jam Pulang', 'Status', 'Petugas', 'Latitude Masuk', 'Longitude Masuk'];
    }

    private function row($attendance): array
    {
        return [
            $attendance->attendance_date->format('d/m/Y'),
            $attendance->employee->name,
            $attendance->employee->department->name,
            $attendance->employee->shift->name,
            $attendance->check_in_at?->format('H:i:s') ?? '-',
            $attendance->check_out_at?->format('H:i:s') ?? '-',
            $attendance->status->label(),
            $attendance->recorder?->name ?? '-',
            $attendance->check_in_latitude ?? '-',
            $attendance->check_in_longitude ?? '-',
        ];
    }
}
