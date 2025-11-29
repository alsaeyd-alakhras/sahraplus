<?php

namespace App\Repositories;

use App\Models\ChannelProgram;
use Illuminate\Database\Eloquent\Builder;

class ChannelProgramRepository
{
    protected ChannelProgram $channelProgram;

    public function __construct(ChannelProgram $channelProgram)
    {
        $this->channelProgram = $channelProgram;
    }

    public function getQuery(): Builder
    {
        return $this->channelProgram->query();
    }

    public function getById(int $id): ?ChannelProgram
    {
        return $this->channelProgram->with('channel')->find($id);
    }

    public function save(array $data): ChannelProgram
    {
        return ChannelProgram::create($data);
    }

    public function update(array $data, int $id): ChannelProgram
    {
        $program = $this->channelProgram->findOrFail($id);
        $program->update($data);
        return $program;
    }

    public function delete(int $id): ?ChannelProgram
    {
        $program = $this->channelProgram->find($id);
        if ($program) $program->delete();
        return $program;
    }

    public function findOrFail(int $id): ChannelProgram
    {
        return $this->channelProgram->with('channel')->findOrFail($id);
    }
}
