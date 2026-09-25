<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\EmployeeMasterData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HcmEmployeeMaster extends Component
{
    use WithFileUploads, WithPagination;

    // Search & Filter
    public string $search = '';

    public string $departmentFilter = 'all';

    // Form Modal (Add / Edit)
    public bool $isFormModalOpen = false;

    public ?int $editingEmployeeId = null;

    public string $ktp_number = '';

    public string $name = '';

    public ?int $department_id = null;

    // Delete Confirmation Modal
    public bool $isDeleteModalOpen = false;

    public ?int $deletingEmployeeId = null;

    public ?string $deletingEmployeeName = null;

    // CSV Bulk Upload Modal
    public bool $isCsvModalOpen = false;

    public $csvFile = null;

    public ?string $csvSuccessMessage = null;

    public ?string $csvErrorMessage = null;

    public array $csvImportStats = [];

    /**
     * Strict Authorization: Ensure user is from HR Department (department_id === 2 or HRD).
     */
    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Akses Ditolak: Anda belum terotentikasi.');
        }

        $isHrDepartment = ((int) $user->department_id === 2)
            || ((int) $user->department_id === 4)
            || ($user->department && (
                str_contains(strtolower($user->department->name), 'human resources') ||
                str_contains(strtolower($user->department->name), 'hr')
            ));

        if (! $isHrDepartment) {
            abort(403, 'Akses Ditolak: Modul HCM Master Data hanya dapat diakses oleh Departemen HRD (department_id = 2).');
        }

        // Set default department for adding employee if available
        $defaultDept = Department::first();
        if ($defaultDept) {
            $this->department_id = $defaultDept->id;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open Modal to Add a new employee manually.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingEmployeeId = null;
        $this->ktp_number = '';
        $this->name = '';

        $firstDept = Department::first();
        $this->department_id = $firstDept ? $firstDept->id : null;

        $this->isFormModalOpen = true;
    }

    /**
     * Open Modal to Edit an existing employee record.
     */
    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $employee = EmployeeMasterData::findOrFail($id);

        $this->editingEmployeeId = $employee->id;
        $this->ktp_number = $employee->ktp_number;
        $this->name = $employee->name;
        $this->department_id = $employee->department_id;

        $this->isFormModalOpen = true;
    }

    /**
     * Close the Add/Edit form modal.
     */
    public function closeFormModal(): void
    {
        $this->isFormModalOpen = false;
        $this->editingEmployeeId = null;
        $this->reset(['ktp_number', 'name']);
        $this->resetValidation();
    }

    /**
     * Save employee record (Add or Edit).
     */
    public function saveEmployee(): void
    {
        $this->validate([
            'ktp_number' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('employee_master_data', 'ktp_number')->ignore($this->editingEmployeeId),
            ],
            'name' => 'required|string|min:3|max:150',
            'department_id' => 'required|exists:departments,id',
        ], [
            'ktp_number.required' => 'Nomor KTP (NIK) wajib diisi.',
            'ktp_number.size' => 'Nomor KTP harus tepat 16 digit angka.',
            'ktp_number.regex' => 'Nomor KTP hanya boleh berisi angka.',
            'ktp_number.unique' => 'Nomor KTP ini sudah terdaftar dalam Master Data.',
            'name.required' => 'Nama lengkap karyawan wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'department_id.required' => 'Silakan pilih departemen penugasan.',
            'department_id.exists' => 'Departemen yang dipilih tidak valid.',
        ]);

        if ($this->editingEmployeeId) {
            $employee = EmployeeMasterData::findOrFail($this->editingEmployeeId);
            $employee->update([
                'ktp_number' => trim($this->ktp_number),
                'name' => trim($this->name),
                'department_id' => (int) $this->department_id,
            ]);

            session()->flash('success_message', "Data karyawan {$employee->name} berhasil diperbarui.");
        } else {
            EmployeeMasterData::create([
                'ktp_number' => trim($this->ktp_number),
                'name' => trim($this->name),
                'department_id' => (int) $this->department_id,
            ]);

            session()->flash('success_message', 'Karyawan baru berhasil ditambahkan ke Master Data HRD.');
        }

        $this->closeFormModal();
    }

    /**
     * Open confirmation modal for deleting an employee.
     */
    public function confirmDelete(int $id): void
    {
        $employee = EmployeeMasterData::findOrFail($id);
        $this->deletingEmployeeId = $employee->id;
        $this->deletingEmployeeName = $employee->name;
        $this->isDeleteModalOpen = true;
    }

    /**
     * Close the delete confirmation modal.
     */
    public function closeDeleteModal(): void
    {
        $this->isDeleteModalOpen = false;
        $this->deletingEmployeeId = null;
        $this->deletingEmployeeName = null;
    }

    /**
     * Delete an employee record from Master Data.
     */
    public function deleteEmployee(): void
    {
        if ($this->deletingEmployeeId) {
            $employee = EmployeeMasterData::find($this->deletingEmployeeId);
            if ($employee) {
                $name = $employee->name;
                $employee->delete();
                session()->flash('success_message', "Data karyawan {$name} berhasil dihapus dari Master Data.");
            }
        }

        $this->closeDeleteModal();
    }

    /**
     * Open CSV Bulk Upload Modal.
     */
    public function openCsvModal(): void
    {
        $this->resetValidation();
        $this->csvFile = null;
        $this->csvSuccessMessage = null;
        $this->csvErrorMessage = null;
        $this->csvImportStats = [];
        $this->isCsvModalOpen = true;
    }

    /**
     * Close CSV Bulk Upload Modal.
     */
    public function closeCsvModal(): void
    {
        $this->isCsvModalOpen = false;
        $this->csvFile = null;
        $this->resetValidation();
    }

    /**
     * Bulk Upload and Synchronize Employees via CSV.
     * Expected CSV headers: ktp_number, name, department_id
     */
    public function uploadCsv(): void
    {
        $this->csvSuccessMessage = null;
        $this->csvErrorMessage = null;
        $this->csvImportStats = [];

        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csvFile.required' => 'Silakan pilih file CSV terlebih dahulu.',
            'csvFile.mimes' => 'File harus berekstensi .csv atau .txt.',
            'csvFile.max' => 'Ukuran file CSV maksimal 5MB.',
        ]);

        try {
            $filePath = $this->csvFile->getRealPath();

            if (! file_exists($filePath) || ! is_readable($filePath)) {
                $this->csvErrorMessage = 'File CSV tidak dapat dibaca dari penyimpanan sementara.';

                return;
            }

            $content = file_get_contents($filePath);

            // Strip UTF-8 BOM if present
            if (str_starts_with($content, "\xEF\xBB\xBF")) {
                $content = substr($content, 3);
            }

            // Split into lines
            $lines = preg_split('/\r\n|\r|\n/', trim($content));
            if (empty($lines)) {
                $this->csvErrorMessage = 'File CSV kosong atau tidak memiliki data.';

                return;
            }

            // Determine delimiter: comma, semicolon, or tab
            $firstLine = $lines[0];
            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }

            // Parse headers
            $headers = str_getcsv($firstLine, $delimiter);
            $normalizedHeaders = array_map(function ($h) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $h)));
            }, $headers);

            $ktpIdx = array_search('ktp_number', $normalizedHeaders, true);
            if ($ktpIdx === false) {
                // Try common alternative column names
                $ktpIdx = array_search('ktp', $normalizedHeaders, true);
                if ($ktpIdx === false) {
                    $ktpIdx = array_search('nik', $normalizedHeaders, true);
                }
            }

            $nameIdx = array_search('name', $normalizedHeaders, true);
            if ($nameIdx === false) {
                $nameIdx = array_search('nama', $normalizedHeaders, true);
            }

            $deptIdx = array_search('department_id', $normalizedHeaders, true);
            if ($deptIdx === false) {
                $deptIdx = array_search('department', $normalizedHeaders, true);
            }

            if ($ktpIdx === false || $nameIdx === false || $deptIdx === false) {
                $this->csvErrorMessage = 'Format header CSV tidak valid. Wajib memiliki kolom: ktp_number, name, department_id. Ditemukan: '.implode(', ', $headers);

                return;
            }

            $existingDeptIds = Department::pluck('id')->toArray();
            $defaultDeptId = $existingDeptIds[0] ?? 1;

            $insertedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $rowNum = 1;

            DB::beginTransaction();

            for ($i = 1; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if ($line === '') {
                    continue;
                }

                $rowNum++;
                $row = str_getcsv($line, $delimiter);

                $rawKtp = trim($row[$ktpIdx] ?? '');
                $rawName = trim($row[$nameIdx] ?? '');
                $rawDept = trim($row[$deptIdx] ?? '');

                // Clean KTP to alphanumeric/digits
                $cleanKtp = preg_replace('/[^0-9]/', '', $rawKtp);

                if (empty($cleanKtp) || empty($rawName)) {
                    $skippedCount++;
                    continue;
                }

                $deptId = is_numeric($rawDept) && in_array((int) $rawDept, $existingDeptIds, true)
                    ? (int) $rawDept
                    : $defaultDeptId;

                // Check if existing
                $existing = EmployeeMasterData::where('ktp_number', $cleanKtp)->first();

                // Secure updateOrCreate as explicitly specified
                EmployeeMasterData::updateOrCreate(
                    ['ktp_number' => $cleanKtp],
                    [
                        'name' => $rawName,
                        'department_id' => $deptId,
                    ]
                );

                if ($existing) {
                    $updatedCount++;
                } else {
                    $insertedCount++;
                }
            }

            DB::commit();

            $totalProcessed = $insertedCount + $updatedCount;
            $this->csvImportStats = [
                'total' => $totalProcessed,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
            ];

            $this->csvSuccessMessage = "Bulk Import Berhasil: {$totalProcessed} data karyawan diproses ({$insertedCount} baru, {$updatedCount} diperbarui, {$skippedCount} dilewati).";
            session()->flash('success_message', $this->csvSuccessMessage);
            $this->csvFile = null;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('HCM CSV Upload Error: '.$e->getMessage()."\n".$e->getTraceAsString());
            $this->csvErrorMessage = 'Gagal memproses file CSV: '.$e->getMessage();
        }
    }

    public function render(): View
    {
        $query = EmployeeMasterData::query()
            ->with(['department', 'registeredUser']);

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('ktp_number', 'like', $searchTerm)
                    ->orWhereHas('department', function ($dq) use ($searchTerm) {
                        $dq->where('name', 'like', $searchTerm);
                    });
            });
        }

        if ($this->departmentFilter !== 'all') {
            $query->where('department_id', (int) $this->departmentFilter);
        }

        $employees = $query->orderBy('name')->paginate(10);

        return view('livewire.hcm-employee-master', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->get(),
            'totalEmployees' => EmployeeMasterData::count(),
            'registeredCount' => EmployeeMasterData::whereHas('registeredUser')->count(),
        ]);
    }
}
