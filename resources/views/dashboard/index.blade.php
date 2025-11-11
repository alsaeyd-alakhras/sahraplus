<x-dashboard-layout>
    <x-row />
    <x-index
        :topWatched="$topWatched"
        :topRated="$topRated"
        :topDownloaded="$topDownloaded"
        :dailyInteractions="$dailyInteractions"
        :activeUsers="$activeUsers"
        :mostAdded="$mostAdded"
        :mostSavedByUsers="$mostSavedByUsers"
    />

</x-dashboard-layout>
