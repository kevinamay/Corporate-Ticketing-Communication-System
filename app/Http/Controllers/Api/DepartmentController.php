<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Get list of all corporate departments.
     */
    public function index(): JsonResponse
    {
        $departments = Department::orderBy('id', 'asc')->get()->map(function ($dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'icon' => $dept->icon,
                'description' => $dept->description,
            ];
        });

        return response()->json([
            'success' => true,
            'departments' => $departments,
        ]);
    }

    /**
     * Get standard category options mapped by department category groups.
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'categories' => [
                'IT Support' => ['Network & WiFi', 'Hardware & PC', 'Software & OS', 'Printer & Scanner', 'Sistem Informasi Korporat', 'Lainnya'],
                'Facility & Maintenance' => ['AC & Sirkulasi', 'Kelistrikan Pabrik', 'Pipa & Plumbing', 'Fasilitas Gedung', 'Lainnya'],
                'Produksi & Mesin' => ['Mesin Injection Molding', 'Mesin Blow Molding', 'Kompresor Udara', 'Conveyor & Matriks', 'Lainnya'],
                'Gudang & Logistik' => ['Stok Biji Plastik', 'Karton & Pengemasan', 'Forklift & Pallet', 'Ekspedisi & Kargo', 'Lainnya'],
                'Human Resources (HRD)' => ['Absensi & Jam Kerja', 'Izin Cuti', 'Payroll & Gaji', 'Fasilitas Karyawan', 'Lainnya'],
            ],
            'priorities' => ['Low', 'Medium', 'High', 'Urgent'],
        ]);
    }
}
