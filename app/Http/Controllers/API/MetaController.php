<?php

namespace App\Http\Controllers\API;

use App\Enums\Department;
use App\Enums\Faculty;
use App\Enums\UserPosition;
use App\Http\Controllers\Controller;

class MetaController extends Controller
{
    public function faculties()
    {
        return response()->json([
            'data' => array_map(
                fn (Faculty $faculty) => [
                    'name' => $faculty->name,
                    'value' => $faculty->value,
                    'label' => $faculty->label(),
                ],
                Faculty::cases()
            )
        ]);
    }

    public function positions()
    {
        return response()->json([
            'data' => array_map(
                fn (UserPosition $position) => [
                    'name' => $position->name,
                    'value' => $position->value,
                    'label' => $position->label(),
                    'role' => $position->getRole()->value,
                ],
                UserPosition::cases()
            )
        ]);
    }

    public function departments()
    {
        return response()->json([
            'data' => array_map(
                fn (Department $department) => [
                    'name' => $department->name,
                    'value' => $department->value,
                    'label' => $department->label(),
                    'faculty' => [
                        'name' => $department->faculty()->name,
                        'value' => $department->faculty()->value,
                        'label' => $department->faculty()->label(),
                    ]
                ],
                Department::cases()
            )
        ]);
    }
}
