<?php

namespace App\Exports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantsExport implements ShouldAutoSize, WithMapping, WithHeadings, FromCollection
{
    /**
     * @var int
     */
    protected int $user_id;

    /**
     * CompanyUsersExport constructor.
     *
     * @param int $user_id
     */
    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Set data for export
     *
     * @return \Illuminate\Support\Collection|mixed
     */
    public function collection()
    {
        return Tenant::where('user_id', $this->user_id)->get();
    }

    /**
     * Set heading rows for export
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tenant First Name',
            'Tenant Last Name',
            'Email',
            'Address',
            'City',
            'Post Code',
            'Country',
        ];
    }

    /**
     * Return data for excel
     *
     * @param $item
     * @return array
     */
    public function map($item): array
    {
        return [
            $item->first_name,
            $item->last_name,
            $item->email,
            $item->address,
            $item->city,
            $item->post_code,
            $item->country,
        ];
    }
}
