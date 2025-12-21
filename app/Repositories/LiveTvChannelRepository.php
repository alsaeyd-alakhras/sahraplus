<?php

namespace App\Repositories;

use App\Models\LiveTvChannel;
use Illuminate\Database\Eloquent\Builder;

class LiveTvChannelRepository
{
    protected LiveTvChannel $liveTvChannel;

    public function __construct(LiveTvChannel $liveTvChannel)
    {
        $this->liveTvChannel = $liveTvChannel;
    }

    public function getQuery(): Builder
    {
        return $this->liveTvChannel->query();
    }

    public function getById(int $id): ?LiveTvChannel
    {
        return $this->liveTvChannel->with('category')->find($id);
    }

    public function save(array $data): LiveTvChannel
    {
        return LiveTvChannel::create($data);
    }

    public function update(array $data, int $id): LiveTvChannel
    {
        $channel = $this->liveTvChannel->findOrFail($id);
        $channel->update($data);
        return $channel;
    }

    public function delete(int $id): ?LiveTvChannel
    {
        $channel = $this->liveTvChannel->find($id);
        if ($channel) $channel->delete();
        return $channel;
    }

    public function findBySlug(string $slug): ?LiveTvChannel
    {
        return $this->liveTvChannel->where('slug', $slug)->first();
    }
}
