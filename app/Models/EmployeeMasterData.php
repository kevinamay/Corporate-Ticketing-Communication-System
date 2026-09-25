<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'ktp_number',
    'name',
    'department_id',
])]
class EmployeeMasterData extends Model
{
    use HasFactory;

    protected $table = 'employee_master_data';

    protected $fillable = [
        'ktp_number',
        'name',
        'department_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'department_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Optional link to the registered user account matching this KTP number.
     *
     * @return HasOne<User, $this>
     */
    public function registeredUser(): HasOne
    {
        return $this->hasOne(User::class, 'ktp_number', 'ktp_number')
            ->orWhere('national_id_ktp', $this->ktp_number);
    }
}
