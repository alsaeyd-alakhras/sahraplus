<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ChannelProgram;
use App\Models\LiveTvChannel;
use Illuminate\Http\Request;
use App\Services\ChannelProgramService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelProgramRequest;
use Illuminate\Support\Facades\Storage;

class ChannelProgramController extends Controller
{
    protected ChannelProgramService $channelProgramService;

    public function __construct(ChannelProgramService $channelProgramService)
    {
        $this->channelProgramService = $channelProgramService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', ChannelProgram::class);

        if (request()->ajax()) {
            return $this->channelProgramService->datatableIndex(request());
        }

        return view('dashboard.channel-programs.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->channelProgramService->getFilterOptions($request, $column);
    }

    /**
     * Export all programs to Excel
     */
    public function export(Request $request)
    {
        $this->authorize('view', ChannelProgram::class);
        return $this->channelProgramService->export($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', ChannelProgram::class);
        $program = new ChannelProgram();
        $channels = LiveTvChannel::active()->ordered()->get();
        return view('dashboard.channel-programs.create', compact('program', 'channels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChannelProgramRequest $request)
    {
        $this->authorize('create', ChannelProgram::class);

        $data = $request->except('_token');

        // Validate time conflict
        if ($this->channelProgramService->checkTimeConflict(
            $data['channel_id'],
            $data['start_time'],
            $data['end_time']
        )) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['start_time' => __('admin.time_conflict_error')])
                ->with('error', __('admin.time_conflict_error'));
        }

        // Handle file uploads
        if ($request->hasFile('poster_url_out')) {
            $data['poster_url_out'] = $request->file('poster_url_out')->store('channel-programs/posters', 'public');
        } else {
            unset($data['poster_url_out']);
        }

        $this->channelProgramService->save($data);

        return redirect()
            ->route('dashboard.channel-programs.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ChannelProgram $channelProgram)
    {
        $this->authorize('update', ChannelProgram::class);

        $program = $channelProgram;
        $channels = LiveTvChannel::active()->ordered()->get();
        $btn_label = __('admin.Edit');
        return view('dashboard.channel-programs.edit', compact('program', 'channels', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChannelProgramRequest $request, ChannelProgram $channelProgram)
    {
        $this->authorize('update', ChannelProgram::class);

        $data = $request->except('_token', '_method');

        // Validate time conflict (excluding current program)
        if ($this->channelProgramService->checkTimeConflict(
            $data['channel_id'],
            $data['start_time'],
            $data['end_time'],
            $channelProgram->id
        )) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['start_time' => __('admin.time_conflict_error')])
                ->with('error', __('admin.time_conflict_error'));
        }

        // Handle file uploads
        if ($request->hasFile('poster_url_out')) {
            $data['poster_url_out'] = $request->file('poster_url_out')->store('channel-programs/posters', 'public');
            // Delete old poster if exists
            if ($channelProgram->poster_url && Storage::disk('public')->exists($channelProgram->poster_url)) {
                Storage::disk('public')->delete($channelProgram->poster_url);
            }
        } else {
            unset($data['poster_url_out']);
        }

        $this->channelProgramService->update($data, $channelProgram->id);

        return redirect()
            ->route('dashboard.channel-programs.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ChannelProgram $channelProgram)
    {
        $this->authorize('delete', ChannelProgram::class);

        $this->channelProgramService->deleteById($channelProgram->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.channel-programs.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
