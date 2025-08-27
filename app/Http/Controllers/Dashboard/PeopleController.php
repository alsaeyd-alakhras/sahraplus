<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Services\PersonService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Models\Person;

class PeopleController extends Controller
{
    protected PersonService $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    public function index()
    {
        $this->authorize('view', Person::class);

        if (request()->ajax()) {
            return $this->personService->datatableIndex(request());
        }

        return view('dashboard.people.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->personService->getFilterOptions($request, $column);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Person::class);
        $person = new Person();
        return view('dashboard.people.create', compact('person'));
    }

    public function store(PersonRequest $request)
    {
        $this->authorize('create', Person::class);

        $this->personService->save(
            $request->validated() + $request->only(['photoUpload'])
        );

        return redirect()
            ->route('dashboard.people.index')
            ->with('success', 'تم إضافة شخص جديد');
    }

    public function show(Person $person)
    {
        $this->authorize('show', Person::class);
        return view('dashboard.people.show', compact('person'));
    }

    public function edit(Request $request, Person $person)
    {
        $this->authorize('update', Person::class);

        $btn_label = "تعديل";
        return view('dashboard.people.edit', compact('person', 'btn_label'));
    }

    public function update(PersonRequest $request, Person $person)
    {
        $this->authorize('update', Person::class);

        $this->personService->update(
            $request->validated() + $request->only(['photoUpload']),
            $person->id
        );

        return redirect()
            ->route('dashboard.people.index')
            ->with('success', 'تم تعديل بيانات الشخص');
    }

    public function destroy(Request $request, Person $person)
    {
        $this->authorize('delete', Person::class);

        $this->personService->deleteById($person->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف الشخص'])
            : redirect()->route('dashboard.people.index')->with('success', 'تم حذف الشخص');
    }
}
