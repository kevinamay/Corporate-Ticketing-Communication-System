<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HcmEmployeeMaster extends Component
{
    use WithFileUploads, WithPagination;

    // Search & Filter
    public string $search = '';

    public string $departmentFilter = 'all';

    // Form Modal (Add / Edit)
    public bool $isFormModalOpen = false;

    public ?int $editingEmployeeId = null;

    public string $name = '';

    public string $whatsapp_number = '';

    public string $email = '';

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
     * Strict Authorization: Ensure user is strictly user123@gmail.com.
     */
    public function mount(): void
    {
        $this->ensureAuthorized();

        $defaultDept = Department::first();
        if ($defaultDept) {
            $this->department_id = $defaultDept->id;
        }
    }

    protected function ensureAuthorized(): void
    {
        $user = Auth::user();
        if (! $user && session('active_user_id')) {
            $user = User::find(session('active_user_id'));
            if ($user) {
                Auth::login($user);
            }
        }

        if (! $user) {
            abort(403, 'Akses Ditolak: Anda belum terotentikasi.');
        }

        if ($user->email !== 'user123@gmail.com') {
            abort(403, 'Akses Ditolak: Modul Manajemen Data Karyawan khusus dan hanya dapat diakses oleh Admin IT (user123@gmail.com).');
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
        $this->ensureAuthorized();
        $this->resetValidation();
        $this->editingEmployeeId = null;
        $this->name = '';
        $this->whatsapp_number = '';
        $this->email = '';

        $firstDept = Department::first();
        $this->department_id = $firstDept ? $firstDept->id : null;

        $this->isFormModalOpen = true;
    }

    /**
     * Open Modal to Edit an existing employee record.
     */
    public function openEditModal(int $id): void
    {
        $this->ensureAuthorized();
        $this->resetValidation();
        $user = User::findOrFail($id);

        $this->editingEmployeeId = $user->id;
        $this->name = $user->name;
        $this->whatsapp_number = $user->whatsapp_number ?? '';
        $this->email = $user->email;
        $this->department_id = $user->department_id;

        $this->isFormModalOpen = true;
    }

    /**
     * Close the Add/Edit form modal.
     */
    public function closeFormModal(): void
    {
        $this->isFormModalOpen = false;
        $this->editingEmployeeId = null;
        $this->reset(['name', 'whatsapp_number', 'email']);
        $this->resetValidation();
    }

    /**
     * Save employee record (Add or Edit).
     */
    public function saveEmployee(): void
    {
        $this->ensureAuthorized();

        $this->validate([
            'name' => 'required|string|min:3|max:150',
            'whatsapp_number' => 'required|string|min:10|max:20|regex:/^[0-9+ ]+$/',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->editingEmployeeId),
            ],
            'department_id' => 'required|exists:departments,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'whatsapp_number.required' => 'Nomor HP / WhatsApp aktif wajib diisi.',
            'whatsapp_number.min' => 'Nomor HP minimal 10 digit.',
            'whatsapp_number.regex' => 'Format nomor HP tidak valid.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'department_id.required' => 'Silakan pilih divisi penugasan.',
            'department_id.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode(trim($this->name)).'&background=0284c7&color=fff';

        if ($this->editingEmployeeId) {
            $user = User::findOrFail($this->editingEmployeeId);
            $user->update([
                'name' => trim($this->name),
                'whatsapp_number' => trim($this->whatsapp_number),
                'email' => strtolower(trim($this->email)),
                'department_id' => (int) $this->department_id,
                'avatar' => $avatarUrl,
            ]);

            session()->flash('success_message', 'Data akun karyawan "'.$user->name.'" berhasil diperbarui.');
        } else {
            User::create([
                'name' => trim($this->name),
                'whatsapp_number' => trim($this->whatsapp_number),
                'email' => strtolower(trim($this->email)),
                'password' => 'password123',
                'department_id' => (int) $this->department_id,
                'role' => 'staff',
                'avatar' => $avatarUrl,
                'email_verified_at' => now(),
            ]);

            session()->flash('success_message', 'Akun karyawan baru berhasil ditambahkan.');
        }

        $this->closeFormModal();
    }

    /**
     * Open confirmation modal for deleting an employee.
     */
    public function confirmDelete(int $id): void
    {
        $this->ensureAuthorized();
        $user = User::findOrFail($id);
        $this->deletingEmployeeId = $user->id;
        $this->deletingEmployeeName = $user->name;
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
     * Delete employee record from database.
     */
    public function deleteEmployee(): void
    {
        $this->ensureAuthorized();

        if ($this->deletingEmployeeId) {
            $user = User::findOrFail($this->deletingEmployeeId);

            // Prevent self-deletion
            if ($user->id === Auth::id() || $user->email === 'user123@gmail.com') {
                session()->flash('error_message', 'Anda tidak dapat menghapus akun Admin IT utama.');
                $this->closeDeleteModal();

                return;
            }

            $name = $user->name;
            $user->delete();

            session()->flash('success_message', 'Akun karyawan "'.$name.'" berhasil dihapus dari sistem.');
            $this->closeDeleteModal();
        }
    }

    /**
     * Open CSV Import modal.
     */
    public function openCsvModal(): void
    {
        $this->ensureAuthorized();
        $this->reset(['csvFile', 'csvSuccessMessage', 'csvErrorMessage', 'csvImportStats']);
        $this->resetValidation();
        $this->isCsvModalOpen = true;
    }

    /**
     * Close CSV Import modal.
     */
    public function closeCsvModal(): void
    {
        $this->isCsvModalOpen = false;
        $this->reset(['csvFile', 'csvSuccessMessage', 'csvErrorMessage', 'csvImportStats']);
        $this->resetValidation();
    }

    /**
     * Process bulk CSV import for employee accounts.
     */
    public function importCsv(): void
    {
        $this->ensureAuthorized();
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
            if (str_starts_with($content, "\xEF\xBB\xBF")) {
                $content = substr($content, 3);
            }

            $lines = preg_split('/\r\n|\r|\n/', trim($content));
            if (empty($lines)) {
                $this->csvErrorMessage = 'File CSV kosong atau tidak memiliki data.';

                return;
            }

            $firstLine = $lines[0];
            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }

            $headers = str_getcsv($firstLine, $delimiter);
            $normalizedHeaders = array_map(function ($h) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $h)));
            }, $headers);

            $nameIdx = array_search('name', $normalizedHeaders, true);
            if ($nameIdx === false) {
                $nameIdx = array_search('nama', $normalizedHeaders, true);
            }

            $waIdx = array_search('whatsapp_number', $normalizedHeaders, true);
            if ($waIdx === false) {
                $waIdx = array_search('whatsapp', $normalizedHeaders, true) ?: array_search('nohp', $normalizedHeaders, true);
            }

            $emailIdx = array_search('email', $normalizedHeaders, true);

            $deptIdx = array_search('department_id', $normalizedHeaders, true);
            if ($deptIdx === false) {
                $deptIdx = array_search('divisi', $normalizedHeaders, true) ?: array_search('department', $normalizedHeaders, true);
            }

            if ($nameIdx === false || $emailIdx === false) {
                $this->csvErrorMessage = 'Format CSV tidak valid. Kolom "name" dan "email" wajib ada di header CSV.';

                return;
            }

            $departments = Department::all()->keyBy('id');
            $deptNames = Department::all()->mapWithKeys(function ($d) {
                return [strtolower(trim($d->name)) => $d->id];
            });

            $insertedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $errors = [];

            for ($i = 1; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if (empty($line)) {
                    continue;
                }

                $row = str_getcsv($line, $delimiter);
                $rowNumber = $i + 1;

                $name = isset($row[$nameIdx]) ? trim($row[$nameIdx]) : '';
                $email = isset($row[$emailIdx]) ? strtolower(trim($row[$emailIdx])) : '';
                $wa = ($waIdx !== false && isset($row[$waIdx])) ? trim($row[$waIdx]) : '081234567890';
                $deptRaw = ($deptIdx !== false && isset($row[$deptIdx])) ? trim($row[$deptIdx]) : '';

                if (empty($name) || empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skippedCount++;
                    $errors[] = "Baris {$rowNumber}: Nama atau Email tidak valid.";

                    continue;
                }

                $resolvedDeptId = null;
                if (is_numeric($deptRaw) && isset($departments[(int) $deptRaw])) {
                    $resolvedDeptId = (int) $deptRaw;
                } elseif (isset($deptNames[strtolower($deptRaw)])) {
                    $resolvedDeptId = $deptNames[strtolower($deptRaw)];
                } else {
                    $resolvedDeptId = Department::first()?->id ?? 1;
                }

                $existing = User::where('email', $email)->first();
                $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=0284c7&color=fff';

                if ($existing) {
                    $existing->update([
                        'name' => $name,
                        'whatsapp_number' => $wa,
                        'department_id' => $resolvedDeptId,
                    ]);
                    $updatedCount++;
                } else {
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'whatsapp_number' => $wa,
                        'department_id' => $resolvedDeptId,
                        'password' => 'password123',
                        'role' => 'staff',
                        'avatar' => $avatarUrl,
                        'email_verified_at' => now(),
                    ]);
                    $insertedCount++;
                }
            }

            $this->csvImportStats = [
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'total_processed' => $insertedCount + $updatedCount + $skippedCount,
                'errors' => array_slice($errors, 0, 5),
            ];

            $this->csvSuccessMessage = "Import berhasil diproses: {$insertedCount} akun baru ditambahkan, {$updatedCount} diperbarui.";
            $this->reset('csvFile');
        } catch (\Throwable $e) {
            Log::error('CSV Upload Error: '.$e->getMessage());
            $this->csvErrorMessage = 'Gagal memproses file CSV: '.$e->getMessage();
        }
    }

    /**
     * Download CSV template file for bulk import.
     */
    public function downloadTemplateCsv(): StreamedResponse
    {
        $this->ensureAuthorized();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_karyawan_admin_it.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'whatsapp_number', 'email', 'department_id']);

            $departments = Department::all();
            if ($departments->isNotEmpty()) {
                foreach ($departments as $idx => $dept) {
                    fputcsv($handle, [
                        'Karyawan '.$dept->name,
                        '0812'.str_pad((string) (10000000 + $idx + 1), 8, '0', STR_PAD_LEFT),
                        'staff.'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $dept->name)).'@asiaplastik.com',
                        $dept->id,
                    ]);
                }
            } else {
                fputcsv($handle, ['Contoh Karyawan', '081234567890', 'karyawan@asiaplastik.com', 1]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function render(): View
    {
        $query = User::query()->with('department');

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('whatsapp_number', 'like', $searchTerm)
                    ->orWhereHas('department', function ($dq) use ($searchTerm) {
                        $dq->where('name', 'like', $searchTerm);
                    });
            });
        }

        if ($this->departmentFilter !== 'all') {
            $query->where('department_id', (int) $this->departmentFilter);
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('livewire.hcm-employee-master', [
            'employees' => $users,
            'departments' => Department::orderBy('name')->get(),
            'totalEmployees' => User::count(),
            'registeredCount' => User::whereNotNull('email_verified_at')->count(),
        ]);
    }
}
