<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\NactaList;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class NactaListController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Nacta List  of Proscribed Persons';
        if ($request->ajax()) {
            $nactaLists = NactaList::query();

            return DataTables::of($nactaLists)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%")
                                ->orWhere('father', 'like', "%{$searchValue}%")
                                ->orWhere('cnic', 'like', "%{$searchValue}%")
                                ->orWhere('province', 'like', "%{$searchValue}%")
                                ->orWhere('district', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->addColumn('name', function ($nactaLists) {
                    return $nactaLists->name ?? '';
                })
                ->addColumn('father_name', function ($nactaLists) {
                    return $nactaLists->father ?? '';
                })
                ->addColumn('cnic', function ($nactaLists) {
                    return $nactaLists->cnic ?? '';
                })
                ->addColumn('province', function ($nactaLists) {
                    return $nactaLists->province ?? '';
                })
                ->addColumn('district', function ($nactaLists) {
                    return $nactaLists->district ?? '';
                })

                ->rawColumns(['name', 'father_name', 'cnic', 'province', 'district']) // Include columns to allow raw HTML if needed
                ->make(true);
        }

        return view('admin.nacta.index', compact('title'));
    }

    public function create()
    {
        $title = 'Upload Nacta List';

        return view('admin.nacta.create', compact('title'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        // Load Excel file
        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray([], $request->file('file'));

        if (!empty($data) && isset($data[0])) {
            // Truncate the NactaList table before inserting new data
            NactaList::truncate();

            // Loop through each row and insert or update the data
            foreach ($data[0] as $row) {
                // Skip header row
                if ($row === $data[0][0]) {
                    continue;
                }

                NactaList::create([
                    'name' => $row[0],
                    'father' => $row[1],
                    'cnic' => $row[2],
                    'province' => $row[3],
                    'district' => $row[4],
                ]);
            }
        }

        return redirect()->route('show-nacta')->with('success', 'File uploaded and data inserted successfully!');
    }

}
