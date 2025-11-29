<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\LiveTvChannel;
use App\Models\LiveTvCategory;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\LiveTvChannelService;
use App\Http\Controllers\Controller;
use App\Http\Requests\LiveTvChannelRequest;
use Illuminate\Support\Facades\Storage;

class LiveTvChannelController extends Controller
{
    protected LiveTvChannelService $liveTvChannelService;

    public function __construct(LiveTvChannelService $liveTvChannelService)
    {
        $this->liveTvChannelService = $liveTvChannelService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', LiveTvChannel::class);

        if (request()->ajax()) {
            return $this->liveTvChannelService->datatableIndex(request());
        }

        return view('dashboard.live-tv-channels.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->liveTvChannelService->getFilterOptions($request, $column);
    }

    /**
     * Export all channels to Excel
     */
    public function export(Request $request)
    {
        $this->authorize('view', LiveTvChannel::class);
        return $this->liveTvChannelService->export($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', LiveTvChannel::class);
        $channel = new LiveTvChannel();
        $categories = LiveTvCategory::active()->ordered()->get();
        $countries = Country::where('is_active', true)->orderBy('name_ar')->get();
        return view('dashboard.live-tv-channels.create', compact('channel', 'categories', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LiveTvChannelRequest $request)
    {
        $this->authorize('create', LiveTvChannel::class);

        $data = $request->except('_token');

        // Handle file uploads
        if ($request->hasFile('logo_url_out')) {
            $data['logo_url_out'] = $request->file('logo_url_out')->store('live-tv/logos', 'public');
        } else {
            unset($data['logo_url_out']);
        }

        if ($request->hasFile('poster_url_out')) {
            $data['poster_url_out'] = $request->file('poster_url_out')->store('live-tv/posters', 'public');
        } else {
            unset($data['poster_url_out']);
        }

        $this->liveTvChannelService->save($data);

        return redirect()
            ->route('dashboard.live-tv-channels.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, LiveTvChannel $liveTvChannel)
    {
        $this->authorize('update', LiveTvChannel::class);

        $channel = $liveTvChannel;
        $categories = LiveTvCategory::active()->ordered()->get();
        $countries = Country::where('is_active', true)->orderBy('name_ar')->get();
        $btn_label = __('admin.Edit');
        return view('dashboard.live-tv-channels.edit', compact('channel', 'categories', 'countries', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LiveTvChannelRequest $request, LiveTvChannel $liveTvChannel)
    {
        $this->authorize('update', LiveTvChannel::class);

        $data = $request->except('_token', '_method');

        // Handle file uploads
        if ($request->hasFile('logo_url_out')) {
            $data['logo_url_out'] = $request->file('logo_url_out')->store('live-tv/logos', 'public');
            // Delete old logo if exists
            if ($liveTvChannel->logo_url && Storage::disk('public')->exists($liveTvChannel->logo_url)) {
                Storage::disk('public')->delete($liveTvChannel->logo_url);
            }
        } else {
            unset($data['logo_url_out']);
        }

        if ($request->hasFile('poster_url_out')) {
            $data['poster_url_out'] = $request->file('poster_url_out')->store('live-tv/posters', 'public');
            // Delete old poster if exists
            if ($liveTvChannel->poster_url && Storage::disk('public')->exists($liveTvChannel->poster_url)) {
                Storage::disk('public')->delete($liveTvChannel->poster_url);
            }
        } else {
            unset($data['poster_url_out']);
        }

        $this->liveTvChannelService->update($data, $liveTvChannel->id);

        return redirect()
            ->route('dashboard.live-tv-channels.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, LiveTvChannel $liveTvChannel)
    {
        $this->authorize('delete', LiveTvChannel::class);

        $this->liveTvChannelService->deleteById($liveTvChannel->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.live-tv-channels.index')->with('success', __('controller.Deleted_item_successfully'));
    }

    /**
     * Test stream connection to Flussonic server
     */
    public function testStream(Request $request)
    {
        $request->validate([
            'stream_name' => 'required|string|max:100',
            'protocol' => 'required|in:hls,dash,rtmp'
        ]);

        try {
            $flussonicService = app(\App\Services\FlussonicService::class);

            // First test server connection
            $connectionTest = $flussonicService->testConnection();

            // Generate stream URL
            $streamData = $flussonicService->generateStreamUrl(
                streamName: $request->stream_name,
                protocol: $request->protocol
            );

            // Check stream health using the generated URL (includes auth token)
            $health = $flussonicService->checkStreamHealth($request->stream_name, $streamData['url']);

            // Build response message
            $message = __('admin.stream_test_successful');
            if (!$connectionTest['success']) {
                $message = '⚠️ ' . __('admin.Server_Unreachable') . ': ' . $connectionTest['message'];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'url' => $streamData['url'],
                'expires_at' => $streamData['expires_at'],
                'status' => $health['status'] ?? 'unknown',
                'server_reachable' => $connectionTest['success'],
                'warning' => !$connectionTest['success'] ? __('admin.Server_Connection_Warning') : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
